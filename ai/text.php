<?php
// Ensure proper session configuration
ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400); // 24 hours
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// Set a cookie to help with session persistence
$cookieName = 'POLLINATIONS_CHAT_SESSION';
if (!isset($_COOKIE[$cookieName])) {
    setcookie($cookieName, '1', time() + 86400, '/', '', false, false);
}

// Start session for chat history
session_start();

// Pollinations.AI Text Generation Script
// Using the OpenAI-compatible POST endpoint with authentication

$token = '8x5QP4YGfNKsu8j-'; // Your provided token

// Initialize chat history session variable if not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

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

// Display the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Pollinations.AI Text Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --chat-primary: #128C7E;
            --chat-secondary: #25D366;
            --chat-light: #DCF8C6;
            --chat-bg: #F5F5F5;  /* Lighter background color */
            --app-height: 100%;
            --chat-header: #075E54;
            --chat-sent: #DCF8C6;
            --chat-received: #FFFFFF;
        }
        html, body {
            height: var(--app-height);
            overflow: hidden;
        }
        body {
            background-color: var(--chat-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .chat-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .chat-header {
            background: var(--chat-header);
            color: white;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .chat-header .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--chat-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-weight: bold;
        }
        .chat-header .chat-info {
            flex-grow: 1;
        }
        .chat-header h2 {
            font-size: 16px;
            margin: 0;
            padding: 0;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 16px;
            background-color: var(--chat-bg);
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAIAAAAC64paAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAO0lEQVQ4y2P8//8/A7UBEwMNwKhBWg0aNYgIg9iIU4tIs8Zok8aAGsQzDcTH5DQ2iI0AYz7QFhJh0AAACBAreUQggYUAAAAASUVORK5CYII=');
            background-repeat: repeat;
            background-color: rgba(248, 248, 248, 0.95);  /* Lighter color with higher opacity */
        }
        .user-message {
            background-color: var(--chat-sent);
            color: #303030;
            border-radius: 8px 8px 0 8px;
            padding: 8px 12px;
            max-width: 80%;
            margin-left: auto;
            margin-bottom: 12px;
            position: relative;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }
        .user-message::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -8px;
            width: 8px;
            height: 13px;
            background-color: var(--chat-sent);
            border-bottom-left-radius: 10px;
        }
        .ai-message {
            background-color: var(--chat-received);
            color: #303030;
            border-radius: 8px 8px 8px 0;
            padding: 8px 12px;
            max-width: 80%;
            margin-right: auto;
            margin-bottom: 12px;
            position: relative;
            box-shadow: 0 1px 0.5px rgba(0,0,0,.13);
        }
        .ai-message::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -8px;
            width: 8px;
            height: 13px;
            background-color: var(--chat-received);
            border-bottom-right-radius: 10px;
        }
        .error-message {
            background-color: #FFCCCC;
            color: #CC0000;
            border-radius: 8px;
            padding: 8px 12px;
            max-width: 90%;
            margin: 0 auto 12px auto;
            text-align: center;
        }
        .message-time {
            font-size: 0.65rem;
            margin-top: 4px;
            opacity: 0.7;
            text-align: right;
        }
        .chat-footer {
            padding: 10px;
            background-color: #F0F0F0;
            border-top: 1px solid #E0E0E0;
        }
        .message-input {
            border-radius: 20px;
            resize: none;
            transition: all 0.3s ease;
            border: 1px solid #DDD;
            padding: 9px 12px;
        }
        .message-input:focus {
            box-shadow: none;
            border-color: var(--chat-secondary);
        }
        .send-button {
            background-color: var(--chat-primary);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .send-button:hover {
            background-color: var(--chat-secondary);
        }
        .typing-indicator {
            display: none;
            align-items: center;
            margin-bottom: 12px;
        }
        .typing-indicator-container {
            background: white;
            border-radius: 8px;
            padding: 8px 16px;
            display: inline-block;
            box-shadow: 0 1px 0.5px rgba(0,0,0,.13);
        }
        .typing-indicator span {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            background-color: var(--chat-primary);
            display: inline-block;
            margin-right: 5px;
            animation: typing 1s infinite ease-in-out;
        }
        .typing-indicator span:nth-child(1) {
            animation-delay: 0.1s;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.3s;
            margin-right: 0;
        }
        
        /* History truncation notice */
        .history-truncated-notice {
            width: 100%;
            margin-bottom: 12px;
        }
        .history-truncated-notice .alert {
            padding: 8px;
            border-radius: 8px;
            background-color: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.2);
            color: #0d6efd;
            font-size: 0.8rem;
        }
        
        /* Model List (Contacts) Styling */
        .models-list-container {
            background: white;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .models-header {
            background: var(--chat-header);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        .models-search {
            margin-top: 10px;
            padding: 8px 15px;
            background: white;
            border-bottom: 1px solid #E0E0E0;
        }
        .models-search input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #DDD;
            background-color: #F0F0F0;
        }
        .models-list {
            overflow-y: auto;
            flex-grow: 1;
        }
        .model-item {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #F0F0F0;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .model-item:hover {
            background-color: #F5F5F5;
        }
        .model-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--chat-primary);
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        .model-info {
            flex-grow: 1;
        }
        .model-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .model-description {
            font-size: 0.8rem;
            color: #606060;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @keyframes typing {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @media (max-width: 768px) {
            .chat-messages {
                height: 350px;
            }
            .user-message, .ai-message {
                max-width: 90%;
            }
        }
        @media (max-width: 576px) {
            html, body {
                height: 100%;
                width: 100%;
                padding: 0;
                margin: 0;
                overflow: hidden;
                background-color: white;
                position: fixed;
                top: 0;
                left: 0;
            }
            .container {
                max-width: 100%;
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                position: absolute;
                top: 0;
                left: 0;
            }
            .chat-container {
                border-radius: 0;
                height: 100%;
                width: 100%;
                max-width: 100%;
                margin: 0;
                position: absolute;
                top: 0;
                left: 0;
                display: flex;
                flex-direction: column;
                box-shadow: none;
            }
            .chat-header {
                border-radius: 0;
                position: relative;
                z-index: 10;
            }
            .chat-messages {
                flex-grow: 1;
                height: auto;
            }
            .chat-footer {
                padding-bottom: env(safe-area-inset-bottom, 15px);
                position: relative;
                z-index: 10;
            }
            .mt-3, .mt-md-5 {
                margin-top: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-3 mt-md-5 px-0">
        <!-- Models List View (WhatsApp contacts style) -->
        <div class="models-list-container" id="modelsList">
            <div class="models-header">
                <h2 class="m-0"><i class="fas fa-comments me-2"></i>Pollinations Chat</h2>
            </div>
            <div class="models-search">
                <input type="text" id="modelSearch" placeholder="Search models..." class="form-control">
            </div>
            <div class="models-list" id="modelsListItems">
                <?php 
                if (is_array($models) && isset($models[0]) && is_array($models[0])) {
                    // API returned objects
                    foreach ($models as $modelObj): 
                        $name = $modelObj['name'] ?? 'unknown';
                        $desc = $modelObj['description'] ?? $name;
                        $firstLetter = strtoupper(substr($name, 0, 1));
                ?>
                <div class="model-item" data-model="<?php echo htmlspecialchars($name); ?>" data-desc="<?php echo htmlspecialchars($desc); ?>">
                    <div class="model-avatar"><?php echo htmlspecialchars($firstLetter); ?></div>
                    <div class="model-info">
                        <div class="model-name"><?php echo htmlspecialchars($desc); ?></div>
                        <div class="model-description">Tap to chat with this AI model</div>
                    </div>
                </div>
                <?php 
                    endforeach;
                } elseif (is_array($models)) {
                    // Fallback or simple array of strings
                    foreach ($models as $model): 
                        $firstLetter = strtoupper(substr($model, 0, 1));
                ?>
                <div class="model-item" data-model="<?php echo htmlspecialchars($model); ?>" data-desc="<?php echo htmlspecialchars($model); ?>">
                    <div class="model-avatar"><?php echo htmlspecialchars($firstLetter); ?></div>
                    <div class="model-info">
                        <div class="model-name"><?php echo htmlspecialchars($model); ?></div>
                        <div class="model-description">Tap to chat with this AI model</div>
                    </div>
                </div>
                <?php 
                    endforeach;
                }
                ?>
            </div>
        </div>

        <!-- Chat View -->
        <div class="chat-container" id="chatContainer" style="display: none;">
            <div class="chat-header">
                <div class="back-button me-2" id="backButton">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="avatar" id="modelAvatar">A</div>
                <div class="chat-info">
                    <h2 id="modelName">AI Model</h2>
                    <small>Powered by Pollinations.AI API</small>
                </div>
                <div class="clear-chat-button" id="clearChatButton" style="color: white; cursor: pointer; opacity: 0.8;" title="Clear chat history">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
            <div class="chat-messages" id="chat">
                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-indicator-container">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <form id="chatForm">
                    <input type="hidden" id="model" name="model" value="">
                    <div class="input-group">
                        <textarea id="prompt" name="prompt" class="form-control message-input" placeholder="Type a message..." rows="1" required></textarea>
                        <button type="submit" class="btn send-button" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fix for mobile browsers viewport height issues
        const appHeight = () => {
            const doc = document.documentElement;
            doc.style.setProperty('--app-height', `${window.innerHeight}px`);
        }
        window.addEventListener('resize', appHeight);
        appHeight();

        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const chatMessages = document.getElementById('chat');
            const chatForm = document.getElementById('chatForm');
            const promptInput = document.getElementById('prompt');
            const sendBtn = document.getElementById('sendBtn');
            const typingIndicator = document.getElementById('typingIndicator');
            const modelsList = document.getElementById('modelsList');
            const chatContainer = document.getElementById('chatContainer');
            const backButton = document.getElementById('backButton');
            const modelItems = document.querySelectorAll('.model-item');
            const modelSearch = document.getElementById('modelSearch');
            const modelNameElement = document.getElementById('modelName');
            const modelAvatarElement = document.getElementById('modelAvatar');
            const modelInput = document.getElementById('model');

            // Show models list by default
            modelsList.style.display = 'flex';
            chatContainer.style.display = 'none';

            // Handle model selection
            // Store the currently selected model
            let currentModel = '';
            
            // Function to check session status (for debugging)
            function checkSessionStatus() {
                fetch('?debug_session=1')
                    .then(response => response.json())
                    .then(data => {
                        console.log("Session status:", data);
                    })
                    .catch(error => console.error("Error checking session:", error));
            }
            
            // Check session on page load
            checkSessionStatus();
            
            modelItems.forEach(item => {
                item.addEventListener('click', function() {
                    const modelName = this.getAttribute('data-model');
                    const modelDesc = this.getAttribute('data-desc');
                    const firstLetter = modelDesc.charAt(0).toUpperCase();
                    
                    console.log(`Switching to model: ${modelName}`);
                    
                    // Update chat view with selected model
                    modelNameElement.textContent = modelDesc;
                    modelAvatarElement.textContent = firstLetter;
                    modelInput.value = modelName;
                    currentModel = modelName;
                    
                    // Switch views
                    modelsList.style.display = 'none';
                    chatContainer.style.display = 'flex';
                    
                    // Clear the chat UI
                    clearChatMessages();
                    
                    // Load chat history for this model
                    loadChatHistory(modelName);
                    
                    // Focus on input
                    setTimeout(() => {
                        promptInput.focus();
                    }, 100);
                });
            });

            // Handle back button
            backButton.addEventListener('click', function() {
                // Remember the current model and its chat before switching views
                const lastModel = currentModel;
                
                console.log(`Back button clicked, remembering model: ${lastModel}`);
                
                // Switch views
                chatContainer.style.display = 'none';
                modelsList.style.display = 'flex';
                
                // Check session to ensure data is preserved
                checkSessionStatus();
            });
            
            // Handle clear chat button
            const clearChatButton = document.getElementById('clearChatButton');
            clearChatButton.addEventListener('click', function() {
                const model = modelInput.value;
                if (!model) return;
                
                if (confirm('Are you sure you want to clear the chat history?')) {
                    // Send request to clear history
                    const formData = new FormData();
                    formData.append('action', 'clear_history');
                    formData.append('model', model);
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin' // Include cookies
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Clear chat UI
                            clearChatMessages();
                            console.log('Chat history cleared');
                        }
                    })
                    .catch(error => console.error('Error clearing history:', error));
                }
            });
            
            // Function to clear all messages from the chat UI
            function clearChatMessages() {
                // Remove all child elements except the typing indicator
                const children = Array.from(chatMessages.children);
                children.forEach(child => {
                    if (child !== typingIndicator) {
                        chatMessages.removeChild(child);
                    }
                });
            }
            
            // Function to load chat history for a model
            function loadChatHistory(model) {
                console.log(`Loading chat history for model: ${model}`);
                
                // Show loading indicator
                typingIndicator.style.display = 'block';
                
                // Add cache-busting parameter to prevent browser caching
                const timestamp = new Date().getTime();
                fetch(`?action=get_history&model=${encodeURIComponent(model)}&_=${timestamp}`, {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    },
                    credentials: 'same-origin' // Include cookies
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("History data received:", data);
                        
                        if (data.status === 'success') {
                            if (data.history && data.history.length > 0) {
                                // Show truncation notice if history was limited
                                if (data.truncated) {
                                    const noticeDiv = document.createElement('div');
                                    noticeDiv.className = 'history-truncated-notice';
                                    noticeDiv.innerHTML = `
                                        <div class="alert alert-info text-center small mb-2">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Showing the most recent ${data.limitedCount} of ${data.originalCount} messages 
                                            (${Math.round(data.charactersUsed / 1000 * 10) / 10}K characters)
                                        </div>
                                    `;
                                    chatMessages.appendChild(noticeDiv);
                                    console.log(`Chat history truncated: ${data.limitedCount}/${data.originalCount} messages`);
                                }
                                
                                // Display each message in the UI
                                data.history.forEach(msg => {
                                    if (msg.role === 'user') {
                                        addMessage('user', msg.content);
                                    } else if (msg.role === 'assistant') {
                                        addMessage('ai', formatResponse(msg.content), model);
                                    }
                                });
                            } else {
                                console.log("No history found for model:", model);
                            }
                        } else {
                            console.error("Error in history response:", data);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading chat history:', error);
                    })
                    .finally(() => {
                        // Hide loading indicator
                        typingIndicator.style.display = 'none';
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
            }

            // Filter models on search
            modelSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                modelItems.forEach(item => {
                    const modelName = item.getAttribute('data-desc').toLowerCase();
                    if (modelName.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Auto-resize textarea as user types
            promptInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Handle Enter key to submit the form, Shift+Enter to add a new line
            promptInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.dispatchEvent(new Event('submit'));
                }
            });

            // Handle form submission
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const userMessage = formData.get('prompt').trim();
                
                if (!userMessage) return;
                
                const model = formData.get('model');
                
                // Log for debugging
                console.log(`Sending message for model: ${model}`);
                
                // Check session status before sending
                checkSessionStatus();
                
                // Disable inputs and show sending state
                sendBtn.disabled = true;
                promptInput.disabled = true;
                
                // Add user message to chat
                addMessage('user', userMessage);
                
                // Reset and focus the input
                promptInput.value = '';
                promptInput.style.height = 'auto';
                
                // Show typing indicator
                typingIndicator.style.display = 'block';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin' // Include cookies
                })
                .then(response => response.json())
                .then(data => {
                    // Hide typing indicator
                    typingIndicator.style.display = 'none';
                    
                    // Log the full response to console for debugging
                    console.log('API Response:', data);
                    
                    if (data.status === 'success') {
                        // Log the response text
                        console.log('Response Text:', data.text);
                        // Try to parse it as JSON if it's a string representation of JSON
                        try {
                            const jsonContent = JSON.parse(data.text);
                            console.log('Parsed JSON content:', jsonContent);
                        } catch(e) {
                            console.log('Text is not valid JSON');
                        }
                        
                        // Format and display AI response
                        addMessage('ai', formatResponse(data.text), data.model);
                    } else {
                        addMessage('error', data.message);
                    }
                })
                .catch(error => {
                    // Hide typing indicator
                    typingIndicator.style.display = 'none';
                    addMessage('error', 'Network error: ' + error.message);
                })
                .finally(() => {
                    // Re-enable inputs
                    sendBtn.disabled = false;
                    promptInput.disabled = false;
                    promptInput.focus();
                    
                    // Scroll to the bottom of chat
                    setTimeout(() => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 100);
                });
            });

            // Function to add a message to the chat
            function addMessage(type, text, model = '') {
                const messageDiv = document.createElement('div');
                const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                if (type === 'user') {
                    messageDiv.className = 'user-message';
                    messageDiv.innerHTML = `
                        <div>${escapeHtml(text)}</div>
                        <div class="message-time">${time}</div>
                    `;
                } else if (type === 'ai') {
                    messageDiv.className = 'ai-message';
                    
                    // Try to parse the response as JSON for better display
                    let displayText = text;
                    try {
                        const jsonObj = JSON.parse(text);
                        // If it's valid JSON, we'll add a collapsible section to show it
                        const jsonFormatted = JSON.stringify(jsonObj, null, 2);
                        displayText = `
                            <div>${text}</div>
                            <details class="mt-2">
                                <summary>View as JSON</summary>
                                <pre class="bg-light p-2 mt-1 rounded" style="max-height: 200px; overflow: auto;">${escapeHtml(jsonFormatted)}</pre>
                            </details>
                        `;
                    } catch (e) {
                        // Not JSON, use the formatted text as-is
                    }
                    
                    messageDiv.innerHTML = `
                        <div><strong>${model}</strong>: ${displayText}</div>
                        <div class="message-time">${time}</div>
                    `;
                } else if (type === 'error') {
                    messageDiv.className = 'error-message';
                    messageDiv.innerHTML = `
                        <div><i class="fas fa-exclamation-triangle me-2"></i>${escapeHtml(text)}</div>
                        <div class="message-time">${time}</div>
                    `;
                }
                
                // Insert before typing indicator
                chatMessages.insertBefore(messageDiv, typingIndicator);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Animate message appearance
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateY(20px)';
                messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    messageDiv.style.opacity = '1';
                    messageDiv.style.transform = 'translateY(0)';
                }, 10);
            }
            
            // Format code blocks and URLs in responses
            function formatResponse(text) {
                // Basic security: escape HTML
                text = escapeHtml(text);
                
                // Format code blocks (```code```)
                text = text.replace(/```([\s\S]*?)```/g, '<pre class="bg-dark text-white p-2 mt-2 mb-2 rounded"><code>$1</code></pre>');
                
                // Format inline code (`code`)
                text = text.replace(/`([^`]+)`/g, '<code class="bg-light px-1 rounded">$1</code>');
                
                // Convert URLs to links
                text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
                
                // Convert line breaks to <br>
                text = text.replace(/\n/g, '<br>');
                
                return text;
            }
            
            // Helper function to escape HTML
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</body>
</html>