<?php
// includes/init.php — барлық беттерге ортақ баптаулар мен функциялар
// Оқу кезеңінде қателер экранда көрінсін (хостингке шығарғанда өшіреміз)
ini_set('display_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('USERS_FILE', __DIR__ . '/../data/users.json');

/* ---------- Көмекші функциялар ---------- */

// XSS-тен қорғау: пайдаланушы деректерін экранға шығарғанда ӘРҚАШАН қолданамыз
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Ағымдағы бет (сүзгі параметрлерімен): себетке қосқаннан кейін сол бетке қайту үшін
function current_url(): string
{
    $query = (string)($_SERVER['QUERY_STRING'] ?? '');
    return basename((string)($_SERVER['SCRIPT_NAME'] ?? 'index.php')) . ($query !== '' ? '?' . $query : '');
}

// Қайту мекенжайы тек өз сайтымыздың белгілі беттерінің бірі болуы керек
function safe_back(string $url): string
{
    return preg_match('/^(index|products|product)\.php(\?[A-Za-z0-9_%=&.+\-]*)?$/', $url) ? $url : 'cart.php';
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('Алдымен жүйеге кіріңіз.', 'error');
        redirect('login.php');
    }
}

// Бір реттік хабарлама (flash)
function flash(string $text, string $type = 'success'): void
{
    $_SESSION['flash'] = ['text' => $text, 'type' => $type];
}

function take_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// CSRF-қорғау: әр форма жасырын токен жібереді
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_ok(): bool
{
    return isset($_POST['csrf'])
        && hash_equals((string)($_SESSION['csrf'] ?? ''), (string)$_POST['csrf']);
}

// POST өрісін қауіпсіз алу (бос жолдарды қиып тастайды)
function post(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

function price(int $value): string
{
    return number_format($value, 0, '', ' ') . ' ₸';
}

/* ---------- Тауарлар (12-аптада MySQL-ге көшеді) ---------- */

function all_products(): array
{
    return [
        ['id' => 1, 'name' => 'Сымсыз құлаққап',         'category' => 'Электроника',   'price' => 18900, 'icon' => '🎧', 'description' => 'Шуды басатын, 20 сағатқа дейін жұмыс істейтін сымсыз құлаққап.'],
        ['id' => 2, 'name' => 'Смарт-сағат',              'category' => 'Электроника',   'price' => 32500, 'icon' => '⌚', 'description' => 'Қадам санайды, пульсті өлшейді, хабарламаларды көрсетеді.'],
        ['id' => 3, 'name' => 'Портативті колонка',       'category' => 'Электроника',   'price' => 14900, 'icon' => '🔊', 'description' => 'Шағын, бірақ дыбысы қуатты Bluetooth колонка.'],
        ['id' => 4, 'name' => 'Теріден жасалған әмиян',   'category' => 'Аксессуарлар',  'price' => 7500,  'icon' => '👛', 'description' => 'Табиғи теріден тігілген, карта салатын бөлімдері бар әмиян.'],
        ['id' => 5, 'name' => 'Күннен қорғайтын көзілдірік','category' => 'Аксессуарлар','price' => 5900,  'icon' => '🕶️', 'description' => 'UV400 қорғанысы бар жеңіл көзілдірік.'],
        ['id' => 6, 'name' => 'Мақта футболка',           'category' => 'Киім',          'price' => 4500,  'icon' => '👕', 'description' => '100% мақтадан жасалған жұмсақ футболка.'],
        ['id' => 7, 'name' => 'Қысқы күртеше',            'category' => 'Киім',          'price' => 28900, 'icon' => '🧥', 'description' => 'Суық ауа райына арналған жылы әрі жеңіл күртеше.'],
        ['id' => 8, 'name' => 'Үстел шамы',               'category' => 'Үйге арналған',  'price' => 6200,  'icon' => '💡', 'description' => 'Жарықтығы реттелетін LED үстел шамы.'],
        ['id' => 9, 'name' => 'Керамикалық кружка',       'category' => 'Үйге арналған',  'price' => 2300,  'icon' => '☕', 'description' => 'Қолмен боялған 350 мл кружка.'],
    ];
}

function categories(): array
{
    return array_values(array_unique(array_column(all_products(), 'category')));
}

function find_product(int $id): ?array
{
    foreach (all_products() as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

function cart_count(): int
{
    return (int)array_sum($_SESSION['cart'] ?? []);
}

/* ---------- Пайдаланушылар (11-аптада MySQL-ге ауысады) ---------- */

function load_users(): array
{
    if (!is_file(USERS_FILE)) {
        return [];
    }
    $data = json_decode((string)file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): bool
{
    return file_put_contents(
        USERS_FILE,
        json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    ) !== false;
}

function find_user_by_email(string $email): ?array
{
    foreach (load_users() as $u) {
        if (strcasecmp($u['email'], $email) === 0) {
            return $u;
        }
    }
    return null;
}
