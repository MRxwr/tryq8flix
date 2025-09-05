<?php
// Get available image generation models from Pollinations API
// Based on https://github.com/pollinations/pollinations/blob/master/APIDOCS.md

// Fetch the current models from Pollinations API
$modelsUrl = 'https://image.pollinations.ai/models';
$modelsList = @file_get_contents($modelsUrl);

if ($modelsList !== false) {
    $fetchedModels = json_decode($modelsList, true);
    
    // Initialize the image models array
    $imageModels = [];
    
    // If we successfully fetched models, format them
    if (is_array($fetchedModels)) {
        foreach ($fetchedModels as $model) {
            // Add detailed information for each model
            $imageModels[] = [
                'name' => $model,
                'description' => ucfirst($model),
                'type' => 'text-to-image',
                'apiEndpoint' => 'https://image.pollinations.ai/prompt/'
            ];
        }
    }
} 

// If fetching failed or returned empty, provide fallback models
if (empty($imageModels)) {
    $imageModels = [
        [
            'name' => 'flux',
            'description' => 'Flux',
            'type' => 'text-to-image',
            'apiEndpoint' => 'https://image.pollinations.ai/prompt/'
        ],
        [
            'name' => 'kontext',
            'description' => 'Kontext',
            'type' => 'text-to-image',
            'apiEndpoint' => 'https://image.pollinations.ai/prompt/'
        ],
        [
            'name' => 'turbo',
            'description' => 'Turbo',
            'type' => 'text-to-image',
            'apiEndpoint' => 'https://image.pollinations.ai/prompt/'
        ]
    ];
}

// Function to generate image from the Pollinations API
function generateImage($model, $prompt, $params = []) {
    // Base URL for the Pollinations API
    $baseUrl = 'https://image.pollinations.ai/prompt/';
    
    // Build parameters string
    $paramString = '';
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $paramString .= $key . '=' . urlencode($value) . '&';
        }
        $paramString = rtrim($paramString, '&');
        $paramString = '?' . $paramString;
    }
    
    // Add model as a parameter if specified
    if (!empty($model) && $model !== 'default') {
        $modelParam = empty($paramString) ? '?model=' . urlencode($model) : '&model=' . urlencode($model);
        $paramString .= $modelParam;
    }
    
    // URL encode the prompt
    $encodedPrompt = urlencode($prompt);
    
    // Combine the URL parts
    $imageUrl = $baseUrl . $encodedPrompt . $paramString . "&width=1024&height=1024&nologo=true";
    
    // Return the image URL
    // In production, you may want to make an actual API request and handle responses
    return [
        'success' => true,
        'image_url' => $imageUrl,
        'prompt' => $prompt,
        'model' => $model
    ];
}

// Handle image generation API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    header('Content-Type: application/json');
    
    // Get POST parameters
    $model = $_POST['model'] ?? '';
    $prompt = $_POST['prompt'] ?? '';
    
    // Additional parameters
    $params = [];
    if (isset($_POST['width'])) $params['width'] = $_POST['width'];
    if (isset($_POST['height'])) $params['height'] = $_POST['height'];
    if (isset($_POST['steps'])) $params['steps'] = $_POST['steps'];
    if (isset($_POST['guidance_scale'])) $params['guidance_scale'] = $_POST['guidance_scale'];
    
    // Generate the image
    $result = generateImage($model, $prompt, $params);
    
    // Return the result as JSON
    echo json_encode($result);
    exit;
}
?>
