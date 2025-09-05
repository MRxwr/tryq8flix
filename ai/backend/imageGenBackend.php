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

// Track seeds for prompts to ensure different results for repeated prompts
$promptSeeds = [];

// Function to get a seed for a prompt, incrementing if it's been used before
function getSeedForPrompt($prompt, $model) {
    global $promptSeeds;
    
    // Create a unique key for this prompt+model combination
    $key = $model . ':' . $prompt;
    
    // Initialize if this is the first time
    if (!isset($promptSeeds[$key])) {
        $promptSeeds[$key] = 1;
    } else {
        // Increment the seed for repeated prompts
        $promptSeeds[$key]++;
    }
    
    return $promptSeeds[$key];
}

// Function to generate image from the Pollinations API
function generateImage($model, $prompt, $params = []) {
    // Base URL for the Pollinations API
    $baseUrl = 'https://image.pollinations.ai/prompt/';
    
    // Get width and height from params or use defaults
    $width = $params['width'] ?? 512;
    $height = $params['height'] ?? 512;
    
    // Get a seed for this prompt, incrementing if it's been used before
    $seed = getSeedForPrompt($prompt, $model);
    
    // URL encode the prompt
    $encodedPrompt = urlencode($prompt);
    
    // Format model parameter
    $modelParam = '';
    if (!empty($model) && $model !== 'default') {
        $modelParam = '&model=' . urlencode($model);
    }
    
    // Format seed parameter
    $seedParam = '&seed=' . $seed;
    
    // Build the complete URL with all parameters
    $imageUrl = $baseUrl . $encodedPrompt . '?width=' . $width . '&height=' . $height . '&nologo=true' . $modelParam . $seedParam;
    
    // Add any additional parameters
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            // Skip width and height as they're already included
            if ($key !== 'width' && $key !== 'height') {
                $imageUrl .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }
    }
    
    // Return the image URL and additional information
    return [
        'success' => true,
        'image_url' => $imageUrl,
        'prompt' => $prompt,
        'model' => $model,
        'seed' => $seed,
        'width' => $width,
        'height' => $height
    ];
}

// Initialize or load prompt seed data from session
session_start();
if (!isset($_SESSION['prompt_seeds'])) {
    $_SESSION['prompt_seeds'] = [];
} else {
    $promptSeeds = $_SESSION['prompt_seeds'];
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
    
    // Save updated prompt seeds to session
    $_SESSION['prompt_seeds'] = $promptSeeds;
    
    // Return the result as JSON
    echo json_encode($result);
    exit;
}
?>
