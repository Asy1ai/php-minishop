<?php
$pageTitle   = 'Басты бет';
$currentPage = 'home';
require __DIR__ . '/includes/header.php';

$featured = array_slice(all_products(), 0, 3);
?>
<div class="container">
    <section class="hero">
        <div class="hero-text">
            <p class="eyebrow">MiniShop онлайн дүкені</p>
            <h1>Сүйікті тауарларың бір жерде</h1>
            <p>Өзіңізге ұнайтын тауарларды таңдап, себетке қосыңыз және сатып алуды бастаңыз.</p>
            <a class="btn" href="products.php">Каталогқа өту</a>
        </div>
        <div class="hero-picture" aria-hidden="true">🛍️</div>
    </section>

    <section class="advantages">
        <div class="advantage"><span>🚚</span><div><b>Жылдам жеткізу</b><p>Ыңғайлы сауда</p></div></div>
        <div class="advantage"><span>🛡️</span><div><b>Сапалы тауарлар</b><p>Таңдаулы өнімдер</p></div></div>
        <div class="advantage"><span>💜</span><div><b>Қолдау қызметі</b><p>Сізге көмектесеміз</p></div></div>
    </section>

    <div class="section-title">
        <h2>Танымал тауарлар</h2>
        <a class="text-link" href="products.php">Барлығын көру</a>
    </div>

    <div class="products">
        <?php foreach ($featured as $product): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
