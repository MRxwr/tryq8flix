<?php
// Simple test for the Pollinations AI API
require_once 'text.php';

echo "<h1>Testing Pollinations AI API</h1>";

try {
    $ai = new PollinationsAI();
    
    echo "<h2>Test 1: Simple Text Generation</h2>";
    $result = $ai->generateText("Tell me a short joke about programming");
    echo "<p><strong>Result:</strong> " . htmlspecialchars($result) . "</p>";
    
    echo "<h2>Test 2: Chat Completion</h2>";
    $messages = [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => 'What is 2+2?']
    ];
    $result = $ai->chatCompletion($messages);
    echo "<p><strong>Result:</strong> " . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</p>";
    
    echo "<h2>Test 3: Available Models</h2>";
    $models = $ai->getModels();
    echo "<p><strong>Models:</strong> " . htmlspecialchars(json_encode($models, JSON_PRETTY_PRINT)) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
