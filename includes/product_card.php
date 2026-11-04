<?php
// Бір тауардың карточкасы. Қолдану: $product айнымалысын дайындап, include жасау
?>
<article class="product-card">
    <a href="product.php?id=<?= (int)$product['id'] ?>" class="product-img"><?= e($product['icon']) ?></a>
    <div class="product-body">
        <span class="tag"><?= e($product['category']) ?></span>
        <h3><a href="product.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a></h3>
        <p class="product-price"><?= price($product['price']) ?></p>
        <form method="post" action="cart.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="back" value="<?= e(current_url()) ?>">
            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
            <button class="btn small" type="submit">Себетке қосу</button>
        </form>
    </div>
</article>
