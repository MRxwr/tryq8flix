<?php
/**
 * Pollinations AI Text Generation API Integration
 * Supports various AI text generation features including chat, streaming, and advanced options
 */

class PollinationsAI {
    private $token;
    private $baseUrl;
    private $referrer;
    
    public function __construct($token = null, $referrer = 'tryq8flix') {
        $this->token = $token ?: '8x5QP4YGfNKsu8j-';
        $this->baseUrl = 'https://text.pollinations.ai';
        $this->referrer = $referrer;
    }
    
    /**
     * Simple text generation using GET method
     */
    public function generateText($prompt, $options = []) {
        $defaultOptions = [
            'model' => 'openai',
            'temperature' => 0.7,
            'json' => false,
            'stream' => false,
            'private' => false
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        // URL encode the prompt
        $encodedPrompt = urlencode($prompt);
        $url = $this->baseUrl . '/' . $encodedPrompt;
        
        // Add query parameters
        $queryParams = array_filter([
            'model' => $options['model'],
            'temperature' => $options['temperature'],
            'seed' => $options['seed'] ?? null,
            'top_p' => $options['top_p'] ?? null,
            'presence_penalty' => $options['presence_penalty'] ?? null,
            'frequency_penalty' => $options['frequency_penalty'] ?? null,
            'json' => $options['json'] ? 'true' : 'false',
            'system' => isset($options['system']) ? urlencode($options['system']) : null,
            'stream' => $options['stream'] ? 'true' : 'false',
            'private' => $options['private'] ? 'true' : 'false',
            'referrer' => $this->referrer,
            'token' => $this->token
        ]);
        
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query(array_filter($queryParams));
        }
        
        return $this->makeRequest($url, 'GET');
    }
    
    /**
     * Advanced chat completion using POST method (OpenAI compatible)
     */
    public function chatCompletion($messages, $options = []) {
        $defaultOptions = [
            'model' => 'openai',
            'temperature' => 0.7,
            'stream' => false,
            'private' => false
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $payload = [
            'model' => $options['model'],
            'messages' => $messages,
            'token' => $this->token,
            'referrer' => $this->referrer
        ];
        
        // Add optional parameters
        if (isset($options['temperature'])) $payload['temperature'] = $options['temperature'];
        if (isset($options['top_p'])) $payload['top_p'] = $options['top_p'];
        if (isset($options['presence_penalty'])) $payload['presence_penalty'] = $options['presence_penalty'];
        if (isset($options['frequency_penalty'])) $payload['frequency_penalty'] = $options['frequency_penalty'];
        if (isset($options['seed'])) $payload['seed'] = $options['seed'];
        if (isset($options['stream'])) $payload['stream'] = $options['stream'];
        if (isset($options['private'])) $payload['private'] = $options['private'];
        if (isset($options['tools'])) $payload['tools'] = $options['tools'];
        if (isset($options['tool_choice'])) $payload['tool_choice'] = $options['tool_choice'];
        if (isset($options['response_format'])) $payload['response_format'] = $options['response_format'];
        
        return $this->makeRequest($this->baseUrl . '/openai', 'POST', $payload);
    }
    
    /**
     * Text-to-Speech generation
     */
    public function textToSpeech($text, $voice = 'alloy') {
        $encodedText = urlencode($text);
        $url = $this->baseUrl . '/' . $encodedText;
        
        $queryParams = [
            'model' => 'openai-audio',
            'voice' => $voice,
            'referrer' => $this->referrer,
            'token' => $this->token
        ];
        
        $url .= '?' . http_build_query($queryParams);
        
        return $this->makeRequest($url, 'GET', null, true); // true for binary response
    }
    
    /**
     * Vision analysis (image input)
     */
    public function analyzeImage($imageUrl, $question = "What's in this image?", $options = []) {
        $messages = [
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $question],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]]
                ]
            ]
        ];
        
        $defaultOptions = [
            'model' => 'openai',
            'max_tokens' => 500
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        return $this->chatCompletion($messages, $options);
    }
    
    /**
     * Get available models
     */
    public function getModels() {
        $url = $this->baseUrl . '/models';
        return $this->makeRequest($url, 'GET');
    }
    
    /**
     * Make HTTP request
     */
    private function makeRequest($url, $method = 'GET', $data = null, $binary = false) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'TryQ8Flix/1.0'
        ]);
        
        if ($method === 'POST' && $data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->token
            ]);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: " . $httpCode . " - " . $response);
        }
        
        // Return binary data for audio
        if ($binary) {
            return $response;
        }
        
        // Try to decode JSON response
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Return raw response if not JSON
        return $response;
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    try {
        $ai = new PollinationsAI();
        $action = $_REQUEST['action'] ?? 'generate';
        
        switch ($action) {
            case 'generate':
                $prompt = $_REQUEST['prompt'] ?? '';
                if (empty($prompt)) {
                    throw new Exception('Prompt is required');
                }
                
                $options = [
                    'model' => $_REQUEST['model'] ?? 'openai',
                    'temperature' => floatval($_REQUEST['temperature'] ?? 0.7),
                    'json' => filter_var($_REQUEST['json'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'stream' => filter_var($_REQUEST['stream'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'private' => filter_var($_REQUEST['private'] ?? false, FILTER_VALIDATE_BOOLEAN)
                ];
                
                if (isset($_REQUEST['system'])) $options['system'] = $_REQUEST['system'];
                if (isset($_REQUEST['seed'])) $options['seed'] = intval($_REQUEST['seed']);
                if (isset($_REQUEST['top_p'])) $options['top_p'] = floatval($_REQUEST['top_p']);
                if (isset($_REQUEST['presence_penalty'])) $options['presence_penalty'] = floatval($_REQUEST['presence_penalty']);
                if (isset($_REQUEST['frequency_penalty'])) $options['frequency_penalty'] = floatval($_REQUEST['frequency_penalty']);
                
                $result = $ai->generateText($prompt, $options);
                echo json_encode(['success' => true, 'data' => $result]);
                break;
                
            case 'chat':
                $messages = json_decode($_REQUEST['messages'] ?? '[]', true);
                if (empty($messages)) {
                    throw new Exception('Messages are required for chat');
                }
                
                $options = [
                    'model' => $_REQUEST['model'] ?? 'openai',
                    'temperature' => floatval($_REQUEST['temperature'] ?? 0.7),
                    'stream' => filter_var($_REQUEST['stream'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'private' => filter_var($_REQUEST['private'] ?? false, FILTER_VALIDATE_BOOLEAN)
                ];
                
                if (isset($_REQUEST['seed'])) $options['seed'] = intval($_REQUEST['seed']);
                if (isset($_REQUEST['top_p'])) $options['top_p'] = floatval($_REQUEST['top_p']);
                if (isset($_REQUEST['presence_penalty'])) $options['presence_penalty'] = floatval($_REQUEST['presence_penalty']);
                if (isset($_REQUEST['frequency_penalty'])) $options['frequency_penalty'] = floatval($_REQUEST['frequency_penalty']);
                
                $result = $ai->chatCompletion($messages, $options);
                echo json_encode(['success' => true, 'data' => $result]);
                break;
                
            case 'tts':
                $text = $_REQUEST['text'] ?? '';
                $voice = $_REQUEST['voice'] ?? 'alloy';
                
                if (empty($text)) {
                    throw new Exception('Text is required for TTS');
                }
                
                header('Content-Type: audio/mpeg');
                header('Content-Disposition: attachment; filename="speech.mp3"');
                
                $audioData = $ai->textToSpeech($text, $voice);
                echo $audioData;
                exit;
                
            case 'vision':
                $imageUrl = $_REQUEST['image_url'] ?? '';
                $question = $_REQUEST['question'] ?? "What's in this image?";
                
                if (empty($imageUrl)) {
                    throw new Exception('Image URL is required for vision analysis');
                }
                
                $options = [
                    'model' => $_REQUEST['model'] ?? 'openai',
                    'max_tokens' => intval($_REQUEST['max_tokens'] ?? 500)
                ];
                
                $result = $ai->analyzeImage($imageUrl, $question, $options);
                echo json_encode(['success' => true, 'data' => $result]);
                break;
                
            case 'models':
                $result = $ai->getModels();
                echo json_encode(['success' => true, 'data' => $result]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Show usage examples if accessed directly
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pollinations AI - Text Generation API</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .example { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .result { background: #e8f5e8; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffe8e8; padding: 10px; margin: 10px 0; border-radius: 5px; }
        button { background: #007cba; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        textarea { width: 100%; height: 100px; margin: 10px 0; }
        input[type="text"], select { width: 100%; padding: 8px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>🤖 Pollinations AI - Text Generation API</h1>
    
    <div class="example">
        <h3>📝 Simple Text Generation</h3>
        <textarea id="prompt" placeholder="Enter your prompt here...">Write a short story about AI and creativity</textarea>
        <select id="model">
            <option value="openai">OpenAI</option>
            <option value="mistral">Mistral</option>
        </select>
        <input type="range" id="temperature" min="0" max="2" step="0.1" value="0.7">
        <label>Temperature: <span id="tempValue">0.7</span></label>
        <br>
        <button onclick="generateText()">Generate Text</button>
        <div id="textResult" class="result" style="display:none;"></div>
    </div>
    
    <div class="example">
        <h3>💬 Chat Completion</h3>
        <textarea id="userMessage" placeholder="Ask me anything...">What are the benefits of renewable energy?</textarea>
        <button onclick="chatCompletion()">Send Message</button>
        <div id="chatResult" class="result" style="display:none;"></div>
    </div>
    
    <div class="example">
        <h3>🎤 Text-to-Speech</h3>
        <textarea id="ttsText" placeholder="Text to speak...">Hello, this is a test of the text-to-speech feature.</textarea>
        <select id="voice">
            <option value="alloy">Alloy</option>
            <option value="echo">Echo</option>
            <option value="fable">Fable</option>
            <option value="onyx">Onyx</option>
            <option value="nova">Nova</option>
            <option value="shimmer">Shimmer</option>
        </select>
        <button onclick="textToSpeech()">Generate Speech</button>
        <div id="ttsResult" class="result" style="display:none;"></div>
    </div>
    
    <div class="example">
        <h3>👁️ Vision Analysis</h3>
        <input type="text" id="imageUrl" placeholder="Image URL..." value="https://upload.wikimedia.org/wikipedia/commons/thumb/d/dd/Gfp-wisconsin-madison-the-nature-boardwalk.jpg/640px-Gfp-wisconsin-madison-the-nature-boardwalk.jpg">
        <input type="text" id="question" placeholder="Question about the image..." value="What's in this image?">
        <button onclick="analyzeImage()">Analyze Image</button>
        <div id="visionResult" class="result" style="display:none;"></div>
    </div>
    
    <div class="example">
        <h3>📊 Available Models</h3>
        <button onclick="getModels()">Get Models</button>
        <div id="modelsResult" class="result" style="display:none;"></div>
    </div>

    <script>
        // Update temperature display
        document.getElementById('temperature').addEventListener('input', function() {
            document.getElementById('tempValue').textContent = this.value;
        });
        
        function generateText() {
            const prompt = document.getElementById('prompt').value;
            const model = document.getElementById('model').value;
            const temperature = document.getElementById('temperature').value;
            
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=generate&prompt=${encodeURIComponent(prompt)}&model=${model}&temperature=${temperature}`
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('textResult');
                if (data.success) {
                    resultDiv.innerHTML = `<strong>Result:</strong><br>${data.data}`;
                    resultDiv.className = 'result';
                } else {
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.error}`;
                    resultDiv.className = 'error';
                }
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function chatCompletion() {
            const userMessage = document.getElementById('userMessage').value;
            const messages = [
                { role: "system", content: "You are a helpful assistant." },
                { role: "user", content: userMessage }
            ];
            
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=chat&messages=${encodeURIComponent(JSON.stringify(messages))}`
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('chatResult');
                if (data.success && data.data.choices) {
                    resultDiv.innerHTML = `<strong>Assistant:</strong><br>${data.data.choices[0].message.content}`;
                    resultDiv.className = 'result';
                } else {
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.error || 'Unknown error'}`;
                    resultDiv.className = 'error';
                }
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function textToSpeech() {
            const text = document.getElementById('ttsText').value;
            const voice = document.getElementById('voice').value;
            
            window.open(`?action=tts&text=${encodeURIComponent(text)}&voice=${voice}`, '_blank');
            
            const resultDiv = document.getElementById('ttsResult');
            resultDiv.innerHTML = '<strong>Audio generated!</strong> Check your downloads or the new tab.';
            resultDiv.className = 'result';
            resultDiv.style.display = 'block';
        }
        
        function analyzeImage() {
            const imageUrl = document.getElementById('imageUrl').value;
            const question = document.getElementById('question').value;
            
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=vision&image_url=${encodeURIComponent(imageUrl)}&question=${encodeURIComponent(question)}`
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('visionResult');
                if (data.success && data.data.choices) {
                    resultDiv.innerHTML = `<strong>Analysis:</strong><br>${data.data.choices[0].message.content}`;
                    resultDiv.className = 'result';
                } else {
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.error || 'Unknown error'}`;
                    resultDiv.className = 'error';
                }
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function getModels() {
            fetch('?action=models')
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('modelsResult');
                if (data.success) {
                    resultDiv.innerHTML = `<strong>Available Models:</strong><br><pre>${JSON.stringify(data.data, null, 2)}</pre>`;
                    resultDiv.className = 'result';
                } else {
                    resultDiv.innerHTML = `<strong>Error:</strong> ${data.error}`;
                    resultDiv.className = 'error';
                }
                resultDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>