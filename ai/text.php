<?php
// Pollinations.AI Text Generation Script
// Using the OpenAI-compatible POST endpoint with authentication

$token = '8x5QP4YGfNKsu8j-'; // Your provided token

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

    // Prepare the request data
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 1,
        'max_tokens' => 300
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

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $generatedText = $result['choices'][0]['message']['content'];
            if ($isAjax) {
                echo json_encode(['status' => 'success', 'text' => $generatedText, 'model' => $model]);
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pollinations.AI Text Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --chat-primary: #4A55A2;
            --chat-secondary: #7895CB;
            --chat-light: #A0BFE0;
            --chat-bg: #EEF5FF;
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
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .chat-header {
            background: var(--chat-primary);
            color: white;
            padding: 15px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .user-message {
            background-color: var(--chat-primary);
            color: white;
            border-radius: 18px 18px 0 18px;
            padding: 12px 15px;
            max-width: 80%;
            margin-left: auto;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .ai-message {
            background-color: white;
            border: 1px solid #e9ecef;
            border-radius: 18px 18px 18px 0;
            padding: 12px 15px;
            max-width: 80%;
            margin-right: auto;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .error-message {
            background-color: #dc3545;
            color: white;
            border-radius: 18px;
            padding: 12px 15px;
            max-width: 90%;
            margin: 0 auto 15px auto;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .message-time {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.7;
        }
        .chat-footer {
            padding: 15px;
            border-top: 1px solid #e9ecef;
            background-color: white;
        }
        .model-selector {
            background-color: var(--chat-light);
            border: none;
            border-radius: 20px;
        }
        .message-input {
            border-radius: 20px;
            resize: none;
            transition: all 0.3s ease;
        }
        .message-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(74, 85, 162, 0.25);
            border-color: var(--chat-secondary);
        }
        .send-button {
            background-color: var(--chat-primary);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .send-button:hover {
            background-color: var(--chat-secondary);
            transform: translateY(-2px);
        }
        .typing-indicator {
            display: none;
            align-items: center;
            margin-bottom: 15px;
        }
        .typing-indicator span {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            background-color: var(--chat-secondary);
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
            .chat-container {
                border-radius: 0;
                height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .chat-messages {
                flex-grow: 1;
                height: auto;
            }
            body {
                padding: 0;
                margin: 0;
                background-color: white;
            }
            .container {
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-3 mt-md-5">
        <div class="chat-container">
            <div class="chat-header">
                <h2 class="m-0"><i class="fas fa-robot me-2"></i>Pollinations.AI Chat</h2>
                <small>Powered by Pollinations.AI API</small>
            </div>
            <div class="chat-messages" id="chat">
                <div class="typing-indicator" id="typingIndicator">
                    <div class="ai-message" style="padding: 10px 15px;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <form id="chatForm">
                    <div class="mb-3">
                        <select id="model" name="model" class="form-select model-selector">
                            <?php 
                            if (is_array($models) && isset($models[0]) && is_array($models[0])) {
                                // API returned objects
                                foreach ($models as $modelObj): 
                                    $name = $modelObj['name'] ?? 'unknown';
                                    $desc = $modelObj['description'] ?? $name;
                            ?>
                                <option value="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($desc); ?></option>
                            <?php 
                                endforeach;
                            } elseif (is_array($models)) {
                                // Fallback or simple array of strings
                                foreach ($models as $model): 
                            ?>
                                <option value="<?php echo htmlspecialchars($model); ?>"><?php echo htmlspecialchars($model); ?></option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <textarea id="prompt" name="prompt" class="form-control message-input" placeholder="Type your message here..." rows="1" required></textarea>
                        <button type="submit" class="btn send-button" id="sendBtn">
                            <i class="fas fa-paper-plane me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat');
            const chatForm = document.getElementById('chatForm');
            const promptInput = document.getElementById('prompt');
            const sendBtn = document.getElementById('sendBtn');
            const typingIndicator = document.getElementById('typingIndicator');

            // Auto-resize textarea as user types
            promptInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Focus the input field when the page loads
            promptInput.focus();

            // Handle form submission
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const userMessage = formData.get('prompt').trim();
                
                if (!userMessage) return;
                
                const model = formData.get('model');
                
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

                fetch('', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide typing indicator
                    typingIndicator.style.display = 'none';
                    
                    if (data.status === 'success') {
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
                    messageDiv.innerHTML = `
                        <div><strong>${model}</strong>: ${text}</div>
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