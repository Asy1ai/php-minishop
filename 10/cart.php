<?php
require_once __DIR__ . '/includes/init.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ---------- POST: себетті өзгерту (add / update / remove / clear) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        flash('Қауіпсіздік тексеруі өтпеді. Бетті жаңартып (Ctrl+F5), қайта көріңіз.', 'error');
        redirect('cart.php');
    }

    $action = post('action');
    $id     = (int)post('id');
    $qty    = (int)post('qty');
    if ($qty < 1) {
        $qty = 1;
    }
    if ($qty > 10) {
        $qty = 10;
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        flash('Себет тазаланды.');
        redirect('cart.php');
    }

    $product = find_product($id);
    if ($product === null) {
        flash('Мұндай тауар табылмады.', 'error');
        redirect('products.php');
    }

    if ($action === 'add') {
        $current = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id] : 0;
        $_SESSION['cart'][$id] = min(10, $current + $qty);
        flash('«' . $product['name'] . '» себетке қосылды.');
        redirect(safe_back(post('back')));   // сүзгі тұрған бетке қайтамыз
    }

    if ($action === 'update') {
        $_SESSION['cart'][$id] = $qty;
        flash('Саны жаңартылды.');
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
        flash('Тауар себеттен алынды.');
    } else {
        flash('Белгісіз әрекет.', 'error');
    }
    redirect('cart.php');
}

/* ---------- GET: себетті көрсету ---------- */
$items = [];
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $product = find_product((int)$id);
    if ($product === null) {
        continue;
    }
    $sum    = $product['price'] * $qty;
    $total += $sum;
    $items[] = ['product' => $product, 'qty' => $qty, 'sum' => $sum];
}

$pageTitle   = 'Себет';
$currentPage = 'cart';
require __DIR__ . '/includes/header.php';
?>
<div class="container section-space">
    <h1>Менің себетім 🛒</h1>

    <?php if (!$items): ?>
        <div class="empty">
            <p>Себетіңіз бос. Каталогтан тауар таңдаңыз.</p>
            <a class="btn" href="products.php">Тауарларға өту</a>
        </div>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($items as $row): $p = $row['product']; ?>
                <div class="cart-row">
                    <div class="cart-icon"><?= e($p['icon']) ?></div>
                    <div class="cart-info">
                        <a href="product.php?id=<?= (int)$p['id'] ?>"><b><?= e($p['name']) ?></b></a>
                        <p class="muted"><?= price($p['price']) ?></p>
                    </div>

                    <form method="post" action="cart.php" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <input type="number" name="qty" value="<?= (int)$row['qty'] ?>" min="1" max="10" aria-label="Саны">
                        <button class="btn small secondary" type="submit">Жаңарту</button>
                    </form>

                    <b class="cart-sum"><?= price($row['sum']) ?></b>

                    <form method="post" action="cart.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn small danger" type="submit">Жою</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <p>Жалпы сома: <b class="total"><?= price($total) ?></b></p>
            <form method="post" action="cart.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="clear">
                <button class="btn secondary" type="submit">Себетті тазалау</button>
            </form>
            <?php if (is_logged_in()): ?>
                <p class="muted">Тапсырыс беру 14-аптада қосылады.</p>
            <?php else: ?>
                <p class="muted">Тапсырыс беру үшін алдымен <a href="login.php">жүйеге кіріңіз</a>.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
