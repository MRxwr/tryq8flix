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

    // Get selected model and its type
    $modelName = isset($_POST['model']) ? $_POST['model'] : 'openai';
    $modelType = isset($_POST['type']) ? $_POST['type'] : 'text';

    // Get the current model's chat history
    $modelHistory = $_SESSION['chat_history'][$modelName] ?? [];

    // Add the new user message to history
    $modelHistory[] = [
        'role' => 'user',
        'content' => $prompt
    ];

    $generatedContent = '';
    $responseStatus = 'error';
    $errorMessage = 'An unknown error occurred.';

    if ($modelType === 'image') {
        // Handle Image Generation
        $imageUrl = "https://image.pollinations.ai/prompt/" . urlencode($prompt) . "?model=" . urlencode($modelName) . "&width=512&height=512";
        
        // Use cURL to fetch the image
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $imageData) {
            // Create a directory for generated images if it doesn't exist
            $imageDir = __DIR__ . '/../generated_images/';
            if (!is_dir($imageDir)) {
                mkdir($imageDir, 0755, true);
            }
            
            // Save the image to a file
            $imageFileName = 'img_' . time() . '_' . uniqid() . '.jpg';
            $imagePath = $imageDir . $imageFileName;
            file_put_contents($imagePath, $imageData);
            
            // The content for the chat will be the URL to the image
            $generatedContent = 'generated_images/' . $imageFileName;
            $responseStatus = 'success';
        } else {
            $errorMessage = "Failed to generate image. HTTP Status: $httpCode";
        }

    } else {
        // Handle Text Generation (existing logic)
        $url = 'https://text.pollinations.ai/openai';

        // Trim history for API request
        $apiHistory = $modelHistory; // Use a copy
        $maxHistoryCharsForApi = 4500;
        while (calculateHistorySize($apiHistory) > $maxHistoryCharsForApi && count($apiHistory) > 2) {
            array_shift($apiHistory);
        }

        // Add system message if needed
        if (empty(array_filter($apiHistory, fn($m) => $m['role'] === 'system'))) {
            array_unshift($apiHistory, ['role' => 'system', 'content' => 'You are a helpful assistant.']);
        }

        $data = [
            'model' => $modelName,
            'messages' => $apiHistory,
            'temperature' => 1,
            'max_tokens' => 300,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                $generatedContent = $result['choices'][0]['message']['content'];
                $responseStatus = 'success';
            } else {
                $errorMessage = 'Unexpected response format from text API.';
            }
        } else {
            $errorMessage = "HTTP $httpCode - " . $response;
        }
    }

    if ($responseStatus === 'success') {
        // Add the AI response to the chat history
        $modelHistory[] = [
            'role' => 'assistant',
            'content' => $generatedContent
        ];

        // Trim session history if it's too large
        $maxHistoryChars = 10000;
        while (calculateHistorySize($modelHistory) > $maxHistoryChars && count($modelHistory) > 2) {
            array_shift($modelHistory);
        }

        // Save updated history back to session
        $_SESSION['chat_history'][$modelName] = $modelHistory;
        
        if ($isAjax) {
            echo json_encode([
                'status' => 'success', 
                'content' => $generatedContent, 
                'model' => $modelName,
                'type' => $modelType
            ]);
        } else {
            // Fallback for non-AJAX
            if ($modelType === 'image') {
                echo "<div class='alert alert-success mt-4'><h2>Generated Image:</h2><img src='" . htmlspecialchars($generatedContent) . "' class='img-fluid' /></div>";
            } else {
                echo "<div class='alert alert-success mt-4'><h2>Generated Text:</h2><p>" . htmlspecialchars($generatedContent) . "</p></div>";
            }
        }
    } else {
        // Handle errors
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        } else {
            echo "<div class='alert alert-danger mt-4'>Error: " . htmlspecialchars($errorMessage) . "</div>";
        }
    }
    
    if ($isAjax) exit;
}