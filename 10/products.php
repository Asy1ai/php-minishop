<?php
$pageTitle   = 'Тауарлар';
$currentPage = 'products';
require __DIR__ . '/includes/header.php';

// $_GET арқылы іздеу және санат бойынша сүзу
$search   = trim((string)($_GET['q'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));

// Санат тек бар мәндердің бірі болуы керек
if ($category !== '' && !in_array($category, categories(), true)) {
    $category = '';
}

$sort = (string)($_GET['sort'] ?? '');
if (!in_array($sort, ['price_asc', 'price_desc', 'name'], true)) {
    $sort = '';
}

$products = array_values(array_filter(all_products(), function (array $p) use ($search, $category): bool {
    if ($category !== '' && $p['category'] !== $category) {
        return false;
    }
    // Іздеу: атауынан да, сипаттамасынан да, әріп регистріне қарамай
    if ($search !== '' && mb_stripos($p['name'], $search) === false
        && mb_stripos($p['description'], $search) === false) {
        return false;
    }
    return true;
}));

if ($sort === 'price_asc') {
    usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sort === 'price_desc') {
    usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
} elseif ($sort === 'name') {
    usort($products, fn($a, $b) => strcmp($a['name'], $b['name']));
}
?>
<div class="container section-space">
    <h1>Барлық тауарлар</h1>
    <p class="muted">Өзіңізге ұнаған тауарды іздеп, себетке қосыңыз.</p>

    <form class="catalog-tools" method="get" action="products.php">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Тауар атауын іздеу...">
        <input type="hidden" name="category" value="<?= e($category) ?>">
        <select name="sort" onchange="this.form.submit()" aria-label="Сұрыптау">
            <option value="">Сұрыптау</option>
            <option value="price_asc"<?= $sort === 'price_asc' ? ' selected' : '' ?>>Бағасы: арзаннан қымбатқа</option>
            <option value="price_desc"<?= $sort === 'price_desc' ? ' selected' : '' ?>>Бағасы: қымбаттан арзанға</option>
            <option value="name"<?= $sort === 'name' ? ' selected' : '' ?>>Атауы бойынша</option>
        </select>
        <button class="btn" type="submit">Іздеу</button>
    </form>

    <!-- Санат батырмалары: басқанда бірден сүзіледі (іздеу мәтіні мен сұрыптау сақталады) -->
    <div class="chips">
        <?php
        $chipLink = function (string $cat) use ($search, $sort): string {
            $params = array_filter(['q' => $search, 'category' => $cat, 'sort' => $sort], fn($v) => $v !== '');
            return 'products.php' . ($params ? '?' . http_build_query($params) : '');
        };
        ?>
        <a class="chip<?= $category === '' ? ' active' : '' ?>" href="<?= e($chipLink('')) ?>">Барлығы</a>
        <?php foreach (categories() as $cat): ?>
            <a class="chip<?= $cat === $category ? ' active' : '' ?>" href="<?= e($chipLink($cat)) ?>"><?= e($cat) ?></a>
        <?php endforeach; ?>
    </div>

    <p class="muted">Табылған тауар саны: <b><?= count($products) ?></b>
        <?php if ($search !== '' || $category !== '' || $sort !== ''): ?>
            · <a href="products.php">Сүзгіні тазалау</a>
        <?php endif; ?>
    </p>

    <?php if (!$products): ?>
        <div class="empty">
            <p>Ештеңе табылмады. Басқа атау немесе санат таңдап көріңіз.</p>
            <a class="btn secondary" href="products.php">Сүзгіні тазалау</a>
        </div>
    <?php else: ?>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/includes/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
