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

// Function to store image history in session
function storeImageHistory($model, $prompt, $imageUrl, $params = []) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Initialize image history array if not exists
    if (!isset($_SESSION['image_history'])) {
        $_SESSION['image_history'] = [];
    }
    
    // Initialize model history array if not exists
    if (!isset($_SESSION['image_history'][$model])) {
        $_SESSION['image_history'][$model] = [];
    }
    
    // Create image entry
    $imageEntry = [
        'prompt' => $prompt,
        'imageUrl' => $imageUrl,
        'params' => $params,
        'timestamp' => time()
    ];
    
    // Add to model's history
    $_SESSION['image_history'][$model][] = $imageEntry;
    
    // Limit history size to prevent session bloat (keep last 20 images)
    $maxHistorySize = 20;
    if (count($_SESSION['image_history'][$model]) > $maxHistorySize) {
        $_SESSION['image_history'][$model] = array_slice($_SESSION['image_history'][$model], -$maxHistorySize);
    }
}

// Function to get image history for a model
function getImageHistory($model) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['image_history'][$model])) {
        return $_SESSION['image_history'][$model];
    }
    
    return [];
}

// Function to clear image history for a model
function clearImageHistory($model) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['image_history'][$model])) {
        $_SESSION['image_history'][$model] = [];
        return true;
    }
    
    return false;
}

// Handle API requests
$isGet = $_SERVER['REQUEST_METHOD'] === 'GET';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

// Handle debug session requests
if ($isGet && isset($_GET['debug_image_session'])) {
    header('Content-Type: application/json');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Return session info for debugging
    echo json_encode([
        'session_id' => session_id(),
        'has_image_history' => isset($_SESSION['image_history']),
        'models' => isset($_SESSION['image_history']) ? array_keys($_SESSION['image_history']) : [],
        'history_counts' => isset($_SESSION['image_history']) ? array_map('count', $_SESSION['image_history']) : []
    ]);
    exit;
}

// Handle image generation and history API requests
if ($isPost) {
    header('Content-Type: application/json');
    
    // Handle different actions
    $action = $_POST['action'] ?? '';
    
    // Generate new image
    if ($action === 'generate') {
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
        
        // Store in session history
        if ($result['success']) {
            storeImageHistory($model, $prompt, $result['image_url'], $params);
        }
        
        // Return the result as JSON
        echo json_encode($result);
        exit;
    }
    // Get history for a model
    else if ($action === 'get_history') {
        $model = $_POST['model'] ?? '';
        if (empty($model)) {
            echo json_encode(['status' => 'error', 'message' => 'Model name is required']);
            exit;
        }
        
        $history = getImageHistory($model);
        echo json_encode([
            'status' => 'success',
            'model' => $model,
            'history' => $history
        ]);
        exit;
    }
    // Clear history for a model
    else if ($action === 'clear_history') {
        $model = $_POST['model'] ?? '';
        if (empty($model)) {
            echo json_encode(['status' => 'error', 'message' => 'Model name is required']);
            exit;
        }
        
        $success = clearImageHistory($model);
        echo json_encode([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'History cleared successfully' : 'Failed to clear history'
        ]);
        exit;
    }
}
?>
