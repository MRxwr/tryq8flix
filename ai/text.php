<?php
// Pollinations.AI Text Generation Script
// Using the OpenAI-compatible POST endpoint with authentication

$token = '8x5QP4YGfNKsu8j-'; // Your provided token

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {
    $prompt = trim($_POST['prompt']);
    if (empty($prompt)) {
        echo "Please enter a prompt.";
        exit;
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
            echo "<h2>Generated Text:</h2><p>" . htmlspecialchars($generatedText) . "</p>";
        } else {
            echo "Error: Unexpected response format.";
        }
    } else {
        echo "Error: HTTP $httpCode - " . htmlspecialchars($response);
    }
} else {
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

    // Display the form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Pollinations.AI Text Generator</title>
    </head>
    <body>
        <h1>Generate Text with Pollinations.AI</h1>
        <form method="post">
            <label for="model">Select Model:</label><br>
            <select id="model" name="model">
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
            </select><br><br>
            <label for="prompt">Enter your prompt:</label><br>
            <textarea id="prompt" name="prompt" rows="4" cols="50" required></textarea><br><br>
            <button type="submit">Generate</button>
        </form>
    </body>
    </html>
    <?php
}
?>