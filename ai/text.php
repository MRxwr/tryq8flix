<?php
// Pollinations.AI Text Generation Script
// Using the OpenAI-compatible POST endpoint with authentication

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {
    $prompt = trim($_POST['prompt']);
    if (empty($prompt)) {
        echo "Please enter a prompt.";
        exit;
    }

    // API details
    $url = 'https://text.pollinations.ai/openai';
    $token = '8x5QP4YGfNKsu8j-'; // Your provided token

    // Prepare the request data
    $data = [
        'model' => 'openai',
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7,
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
            <label for="prompt">Enter your prompt:</label><br>
            <textarea id="prompt" name="prompt" rows="4" cols="50" required></textarea><br><br>
            <button type="submit">Generate</button>
        </form>
    </body>
    </html>
    <?php
}
?>