<?php
// includes/header.php — барлық беттің жоғарғы бөлігі (меню)
require_once __DIR__ . '/init.php';

$pageTitle   = $pageTitle   ?? 'MiniShop';
$currentPage = $currentPage ?? '';
$flash       = take_flash();

function nav_class(string $page, string $current): string
{
    return $page === $current ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — MiniShop</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>

<header>
    <div class="container navbar">
        <a href="index.php" class="logo">Mini<span>Shop</span></a>

        <button class="menu-btn" id="menuBtn" aria-label="Мәзірді ашу">☰</button>

        <nav id="nav">
            <a href="index.php"<?= nav_class('home', $currentPage) ?>>Басты бет</a>
            <a href="products.php"<?= nav_class('products', $currentPage) ?>>Тауарлар</a>
            <a href="cart.php"<?= nav_class('cart', $currentPage) ?>>Себет 🛒 <span class="badge"><?= cart_count() ?></span></a>
            <?php if (is_logged_in()): ?>
                <a href="profile.php"<?= nav_class('profile', $currentPage) ?>>Профиль</a>
                <a href="logout.php" class="nav-button">Шығу</a>
            <?php else: ?>
                <a href="login.php"<?= nav_class('login', $currentPage) ?>>Кіру</a>
                <a href="register.php" class="nav-button">Тіркелу</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
<?php if ($flash): ?>
    <div class="container">
        <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['text']) ?></div>
    </div>
<?php endif; ?>
