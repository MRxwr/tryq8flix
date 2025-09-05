<?php
function fetchModels($token) {
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
    return $models;
}
?>
