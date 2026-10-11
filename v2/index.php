<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rtrim($path, '/') === $path ? $path : rtrim($path, '/');

if (!str_starts_with($path, basePath() . '/')) {
    http_response_code(404);
    echo 'Not found.';
    exit;
}

if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/index.php')) {
    require __DIR__ . '/api/index.php';
    exit;
}

if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/assets/')) {
    $asset = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_file($asset)) {
        readfile($asset);
        exit;
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require __DIR__ . '/app/auth.php';
    $action = input('endpoint', '');
    if ($action === 'User') {
        $action = input('action', '');
        try {
            $result = authAction($action);
            jsonResponse($result);
        } catch (AppError $e) {
            jsonResponse(null, $e->status, $e);
        }
    }
    jsonResponse(['msg' => 'Unknown action.'], 400);
    exit;
}

if (str_starts_with($path, basePath() . '/')) {
    $view = input('v', 'Home');
    $views = [
        'Login',
        'Register',
        'Forget',
        'Reset',
        'Home',
        'Search',
        'Category',
        'More',
        'Favorites',
        'History',
        'Settings',
        'About',
        'Policy',
        'Terms',
    ];
    if (!in_array($view, $views, true)) {
        $view = 'Home';
    }
    $file = __DIR__ . '/views/blade' . $view . '.php';
    if (is_file($file)) {
        require $file;
        exit;
    }
}

http_response_code(404);
echo 'Page not found.';
