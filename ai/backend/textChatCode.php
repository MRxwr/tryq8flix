<?php
// Log session ID for debugging
error_log("Session ID: " . session_id());

// Helper function to calculate total characters in chat history
function calculateHistorySize($messages) {
    $totalChars = 0;
    foreach ($messages as $msg) {
        if (isset($msg['content'])) {
            $totalChars += strlen($msg['content']);
        }
    }
    return $totalChars;
}

// Debug endpoint to check session status
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['debug_session'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'session_id' => session_id(),
        'session_name' => session_name(),
        'session_status' => session_status(),
        'has_session_data' => isset($_SESSION['chat_history']),
        'models_with_history' => isset($_SESSION['chat_history']) ? array_keys($_SESSION['chat_history']) : [],
        'cookie' => isset($_COOKIE['POLLINATIONS_CHAT_SESSION']),
    ]);
    exit;
}

// Fetch available models
$modelsUrl = 'https://text.pollinations.ai/models';
$chModels = curl_init($modelsUrl);
curl_setopt($chModels, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chModels, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
$modelsResponse = curl_exec($chModels);
curl_close($chModels);

$models = json_decode($modelsResponse, true);
if (!is_array($models)) {
    // Fallback to hardcoded models if fetch fails
    $models = ['openai', 'mistral', 'openai-large', 'claude-hybridspace'];
}

// Endpoint to get chat history
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_history' && isset($_GET['model'])) {
    $model = $_GET['model'];
    
    // Ensure session is active and log for debugging
    error_log("Getting history for model: " . $model . ", Session ID: " . session_id());
    
    // Get chat history or empty array if not set
    $chatHistory = isset($_SESSION['chat_history'][$model]) ? $_SESSION['chat_history'][$model] : [];
    
    // Remove system messages for display
    $displayHistory = array_filter($chatHistory, function($msg) {
        return $msg['role'] !== 'system';
    });
    
    // Limit history to 5000 characters, keeping the most recent messages
    $maxCharacters = 5000;
    $limitedHistory = [];
    $totalChars = 0;
    
    // Reverse the array to process from the most recent message
    $reversedHistory = array_reverse($displayHistory);
    
    foreach ($reversedHistory as $msg) {
        $msgLength = strlen($msg['content']);
        if ($totalChars + $msgLength <= $maxCharacters) {
            // We can add this message
            array_unshift($limitedHistory, $msg); // Add to the beginning to restore original order
            $totalChars += $msgLength;
        } else {
            // We've reached the limit, stop adding messages
            break;
        }
    }
    
    error_log("Limited chat history from " . count($displayHistory) . " to " . count($limitedHistory) . " messages (total " . $totalChars . " characters)");
    
    // Force session write
    session_write_close();
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success', 
        'history' => $limitedHistory,
        'truncated' => count($limitedHistory) < count($displayHistory),
        'originalCount' => count($displayHistory),
        'limitedCount' => count($limitedHistory),
        'charactersUsed' => $totalChars
    ]);
    exit;
}

// Endpoint to clear chat history
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_history' && isset($_POST['model'])) {
    $model = $_POST['model'];
    
    // Log for debugging
    error_log("Clearing history for model: " . $model . ", Session ID: " . session_id());
    
    if (isset($_SESSION['chat_history'][$model])) {
        // Keep only the system message if it exists
        $systemMessage = null;
        foreach ($_SESSION['chat_history'][$model] as $msg) {
            if ($msg['role'] === 'system') {
                $systemMessage = $msg;
                break;
            }
        }
        
        $_SESSION['chat_history'][$model] = $systemMessage ? [$systemMessage] : [];
        
        // Explicitly save the session
        session_write_close();
        session_start();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'History cleared']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    
    $prompt = trim($_POST['prompt']);
    if (empty($prompt)) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a prompt.']);
            exit;
        } else {
            echo "<div class='alert alert-warning mt-4'>Please enter a prompt.</div>";
            exit;
        }
    }

    // API details
    $url = 'https://text.pollinations.ai/openai';

    // Get selected model, default to openai
    $model = isset($_POST['model']) ? $_POST['model'] : 'openai';

    // Get the current model's chat history
    $modelHistory = $_SESSION['chat_history'][$model] ?? [];

    // Trim history before adding new messages to stay within limits
    $maxHistoryCharsForApi = 4500; // A bit less than 5000 to be safe
    $currentChars = calculateHistorySize($modelHistory);

    if ($currentChars > $maxHistoryCharsForApi) {
        $systemMessages = [];
        $userAssistantMessages = [];
        
        foreach ($modelHistory as $msg) {
            if ($msg['role'] === 'system') {
                $systemMessages[] = $msg;
            } else {
                $userAssistantMessages[] = $msg;
            }
        }
        
        $trimmedMessages = $userAssistantMessages;
        while (calculateHistorySize(array_merge($systemMessages, $trimmedMessages)) > $maxHistoryCharsForApi && count($trimmedMessages) > 2) {
            array_shift($trimmedMessages);
        }
        
        $modelHistory = array_merge($systemMessages, $trimmedMessages);
        error_log("Trimmed chat history for API for $model from $currentChars to " . calculateHistorySize($modelHistory) . " characters");
    }
    
    // Add system message for context if this is a new conversation
    if (empty($modelHistory)) {
        $modelHistory[] = [
            'role' => 'system',
            'content' => 'You are a helpful assistant. Please provide informative and thoughtful responses.'
        ];
    }
    
    // Add the new user message to history
    $modelHistory[] = [
        'role' => 'user',
        'content' => $prompt
    ];
    
    // Prepare the request data with conversation history
    $data = [
        'model' => $model,
        'messages' => $modelHistory,
        'temperature' => 1,
        'max_tokens' => 300,
    ];

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Log the raw API response for debugging
    error_log("Pollinations API Response: " . $response);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        // Log the decoded response
        error_log("Decoded Response: " . print_r($result, true));
        
        if (isset($result['choices'][0]['message']['content'])) {
            $generatedText = $result['choices'][0]['message']['content'];
            // Log the extracted content
            error_log("Generated Text: " . $generatedText);
            
            // Add the AI response to the chat history
            $modelHistory[] = [
                'role' => 'assistant',
                'content' => $generatedText
            ];
            
            // Check if we need to trim history (keeping max 10000 characters)
            $maxHistoryChars = 10000; // Twice the display limit to allow for some buffer
            $totalChars = calculateHistorySize($modelHistory);
            
            if ($totalChars > $maxHistoryChars) {
                // We need to trim the history, preserving system messages and most recent messages
                $systemMessages = [];
                $userAssistantMessages = [];
                
                // Separate system messages from user/assistant messages
                foreach ($modelHistory as $msg) {
                    if ($msg['role'] === 'system') {
                        $systemMessages[] = $msg;
                    } else {
                        $userAssistantMessages[] = $msg;
                    }
                }
                
                // Keep removing oldest messages until we're under the limit
                $trimmedMessages = $userAssistantMessages;
                while (calculateHistorySize(array_merge($systemMessages, $trimmedMessages)) > $maxHistoryChars && count($trimmedMessages) > 2) {
                    // Remove the oldest non-system message (at the beginning)
                    array_shift($trimmedMessages);
                }
                
                // Rebuild the history with system messages and trimmed user/assistant messages
                $modelHistory = array_merge($systemMessages, $trimmedMessages);
                
                error_log("Trimmed chat history for $model from $totalChars to " . calculateHistorySize($modelHistory) . " characters");
            }
            
            // Save updated history back to session
            $_SESSION['chat_history'][$model] = $modelHistory;
            
            // Force session write to disk
            session_write_close();
            
            // Restart the session to ensure changes are available for future requests
            session_start();
            
            // Get full chat history for this model to send to the client
            $chatHistory = isset($_SESSION['chat_history'][$model]) ? $_SESSION['chat_history'][$model] : [];
            
            // Log for debugging
            error_log("Updated session data for model $model: " . print_r($_SESSION['chat_history'][$model], true));
            
            if ($isAjax) {
                echo json_encode([
                    'status' => 'success', 
                    'text' => $generatedText, 
                    'model' => $model, 
                    'raw_response' => $result,
                    'history' => $chatHistory
                ]);
            } else {
                echo "<div class='alert alert-success mt-4'><h2>Generated Text:</h2><p>" . htmlspecialchars($generatedText) . "</p></div>";
            }
        } else {
            if ($isAjax) {
                echo json_encode(['status' => 'error', 'message' => 'Unexpected response format.']);
            } else {
                echo "<div class='alert alert-warning mt-4'>Error: Unexpected response format.</div>";
            }
        }
    } else {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => "HTTP $httpCode - " . $response]);
        } else {
            echo "<div class='alert alert-danger mt-4'>Error: HTTP $httpCode - " . htmlspecialchars($response) . "</div>";
        }
    }
    
    if ($isAjax) exit;
}