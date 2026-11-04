<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$user        = $_SESSION['user'];
$pageTitle   = 'Профиль';
$currentPage = 'profile';
require __DIR__ . '/includes/header.php';
?>
<div class="container section-space">
    <div class="profile-card">
        <div class="auth-icon">👤</div>
        <h1>Менің профилім</h1>

        <p><b>Аты-жөні:</b> <?= e($user['name']) ?></p>
        <p><b>Email:</b> <?= e($user['email']) ?></p>
        <p><b>Себеттегі тауар:</b> <?= cart_count() ?> дана</p>

        <div class="btn-row">
            <a class="btn" href="cart.php">Себетімді көру</a>
            <a class="btn secondary" href="products.php">Сауданы жалғастыру</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
