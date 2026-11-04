<?php
require_once __DIR__ . '/includes/init.php';

// $_GET['id'] — бүтін санға айналдырып тексереміз
$id      = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = $id ? find_product($id) : null;

if ($product === null) {
    http_response_code(404);
    $pageTitle = 'Тауар табылмады';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section-space"><div class="empty"><h1>Тауар табылмады</h1>'
       . '<p>Мұндай тауар жоқ немесе жойылған.</p>'
       . '<a class="btn" href="products.php">Каталогқа оралу</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle   = $product['name'];
$currentPage = 'products';
require __DIR__ . '/includes/header.php';
?>
<div class="container section-space">
    <p><a class="text-link" href="products.php">← Каталогқа оралу</a></p>
    <div class="product-detail">
        <div class="product-detail-img"><?= e($product['icon']) ?></div>
        <div>
            <span class="tag"><?= e($product['category']) ?></span>
            <h1><?= e($product['name']) ?></h1>
            <p class="muted"><?= e($product['description']) ?></p>
            <p class="product-price big"><?= price($product['price']) ?></p>

            <form method="post" action="cart.php" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
            <input type="hidden" name="back" value="<?= e(current_url()) ?>">
                <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                <label for="qty">Саны</label>
                <input id="qty" type="number" name="qty" value="1" min="1" max="10">
                <button class="btn" type="submit">Себетке қосу</button>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
