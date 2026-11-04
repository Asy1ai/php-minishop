<?php
// check.php — орта дұрыс бапталғанын тексереді. Ашу: http://localhost/shop/check.php
session_start();
$_SESSION['check_counter'] = ($_SESSION['check_counter'] ?? 0) + 1;

$checks = [
    'PHP нұсқасы 8.0 немесе жоғары (қазір: ' . PHP_VERSION . ')' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'mbstring қосылған (іздеу үшін керек)'                         => function_exists('mb_stripos'),
    'Сессия жұмыс істейді (бетті жаңартқанда сан өседі: ' . $_SESSION['check_counter'] . ')' => session_status() === PHP_SESSION_ACTIVE,
    'data/ қалтасына жазуға болады (тіркелу үшін керек)'           => is_writable(__DIR__ . '/data'),
    'includes/init.php бар'                                         => is_file(__DIR__ . '/includes/init.php'),
    'Файл localhost арқылы ашылып тұр'                              => isset($_SERVER['HTTP_HOST']),
];
?>
<!DOCTYPE html><html lang="kk"><head><meta charset="UTF-8"><title>Тексеру</title>
<style>body{font-family:sans-serif;max-width:640px;margin:2rem auto;padding:0 1rem}li{margin:.5rem 0}</style></head><body>
<h1>MiniShop: орта тексеруі</h1>
<ul>
<?php foreach ($checks as $label => $ok): ?>
    <li><?= $ok ? '✅' : '❌' ?> <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<p>Бетті бірнеше рет жаңартыңыз: сессия жұмыс істесе, жақшадағы сан 1, 2, 3 болып өсіп отырады. Егер ол әрқашан 1 болып қалса, себет жұмыс істемейді (сессия сақталмай тұр).</p>
<p><a href="index.php">← Басты бет</a></p>
</body></html>
