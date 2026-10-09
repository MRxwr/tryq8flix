<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#101014">
<meta name="csrf-token" content="<?= e($user['csrf'] ?? '') ?>">
<title><?= e($view) ?> · Q8Flix V2</title>
<link rel="manifest" href="manifest.json">
<link rel="icon" href="logos/icon-192x192.png">
<link rel="apple-touch-icon" href="logos/icon-192x192.png">
<link rel="stylesheet" href="assets/app.css">
<script src="assets/app.js" defer></script>
</head>
<body data-view="<?= e($view) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header"><a class="brand" href="index.php">Q8<span>FLIX</span><small>V2</small></a>
<nav aria-label="Main navigation">
<?php if ($user): ?>
<a href="index.php" <?= $view==='Home' ? 'aria-current="page"' : '' ?>>Discover</a>
<a href="index.php?v=Search" <?= $view==='Search' ? 'aria-current="page"' : '' ?>>Search</a>
<a href="index.php?v=Favorites" <?= $view==='Favorites' ? 'aria-current="page"' : '' ?>>My List</a>
<a href="index.php?v=History" <?= $view==='History' ? 'aria-current="page"' : '' ?>>History</a>
<a href="index.php?v=Settings" <?= $view==='Settings' ? 'aria-current="page"' : '' ?>>Account</a>
<?php if ($user['role']==='admin'): ?><a href="index.php?v=Health">Source Health</a><?php endif; ?>
<?php else: ?><a href="index.php?v=Login">Sign in</a><a href="index.php?v=Register">Create account</a><?php endif; ?>
</nav></header>
<main id="main">
