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
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Pollinations.AI Chat</h1>
        <div id="chat" class="mb-4" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f8f9fa;"></div>
        <form id="chatForm" class="mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <label for="model" class="form-label">Select Model:</label>
                <select id="model" name="model" class="form-select">
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
            <div class="mb-3">
                <label for="prompt" class="form-label">Enter your message:</label>
                <textarea id="prompt" name="prompt" rows="3" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="sendBtn">Send</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';

            // Add user message to chat
            const userMessage = formData.get('prompt');
            const model = formData.get('model');
            addMessage('user', userMessage, model);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    addMessage('ai', data.text, data.model);
                } else {
                    addMessage('error', data.message);
                }
            })
            .catch(error => {
                addMessage('error', 'Network error: ' + error.message);
            })
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send';
                document.getElementById('prompt').value = '';
                document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
            });
        });

        function addMessage(type, text, model = '') {
            const chat = document.getElementById('chat');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mb-2';
            const time = new Date().toLocaleTimeString();
            if (type === 'user') {
                messageDiv.innerHTML = `<div class="d-flex justify-content-end"><div class="bg-primary text-white p-2 rounded"><strong>You:</strong> ${text}<br><small>${time}</small></div></div>`;
            } else if (type === 'ai') {
                messageDiv.innerHTML = `<div class="d-flex justify-content-start"><div class="bg-light p-2 rounded"><strong>AI (${model}):</strong> ${text}<br><small>${time}</small></div></div>`;
            } else if (type === 'error') {
                messageDiv.innerHTML = `<div class="d-flex justify-content-center"><div class="bg-danger text-white p-2 rounded"><strong>Error:</strong> ${text}<br><small>${time}</small></div></div>`;
            }
            chat.appendChild(messageDiv);
        }
    </script>
</body>
</html>