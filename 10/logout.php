<?php
require_once __DIR__ . '/includes/init.php';

// Сессияны толық тазалау
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Жаңа сессия ашып, хабарлама қалдырамыз
session_start();
flash('Сіз жүйеден шықтыңыз.');
redirect('index.php');
