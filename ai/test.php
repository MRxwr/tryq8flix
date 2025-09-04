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

    echo "<h2>Test 4: Text-to-Speech (TTS)</h2>";
    $ttsText = "Hello from the Pollinations AI test script.";
    $audioData = $ai->textToSpeech($ttsText);
    if (strpos($audioData, 'Error') === false && strlen($audioData) > 1000) {
        echo "<p><strong>Result:</strong> Successfully received audio data (" . strlen($audioData) . " bytes). Cannot play here, but the API call was successful.</p>";
        // Optionally save the file to test it
        // file_put_contents('test_speech.mp3', $audioData);
    } else {
        echo "<p style='color: red;'><strong>Error:</strong> Failed to get valid audio data. Response: " . htmlspecialchars(substr($audioData, 0, 500)) . "</p>";
    }

    echo "<h2>Test 5: Vision Analysis</h2>";
    $imageUrl = "https://upload.wikimedia.org/wikipedia/commons/thumb/d/dd/Gfp-wisconsin-madison-the-nature-boardwalk.jpg/640px-Gfp-wisconsin-madison-the-nature-boardwalk.jpg";
    $visionResult = $ai->analyzeImage($imageUrl, "Describe this image in one sentence.");
    echo "<p><strong>Result:</strong> " . htmlspecialchars(json_encode($visionResult, JSON_PRETTY_PRINT)) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
