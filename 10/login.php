<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('profile.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mb_strtolower(post('email'));
    $password = (string)($_POST['password'] ?? '');

    if (!csrf_ok()) {
        $error = 'Қауіпсіздік тексеруі өтпеді. Бетті жаңартып, қайталаңыз.';
    } elseif ($email === '' || $password === '') {
        $error = 'Пошта мен құпиясөзді толтырыңыз.';
    } else {
        $user = find_user_by_email($email);
        // Қай бөлігі қате екенін айтпаймыз — қауіпсіздік үшін
        if ($user === null || !password_verify($password, $user['password'])) {
            $error = 'Пошта немесе құпиясөз дұрыс емес.';
        } else {
            session_regenerate_id(true); // session fixation-нан қорғау
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
            ];
            flash('Қош келдіңіз, ' . $user['name'] . '!');
            redirect('profile.php');
        }
    }
}

$pageTitle   = 'Кіру';
$currentPage = 'login';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-background">
    <div class="auth-card">
        <div class="auth-icon">👋</div>
        <h1>Жүйеге кіру</h1>
        <p class="muted">Аккаунтыңызға кіріңіз.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" novalidate>
            <?= csrf_field() ?>

            <label for="email">Электрондық пошта</label>
            <input id="email" name="email" type="email" required
                   value="<?= e($email) ?>" placeholder="name@example.com">

            <label for="password">Құпиясөз</label>
            <input id="password" name="password" type="password" required placeholder="Құпиясөзіңіз">

            <button class="btn full-width" type="submit">Кіру</button>
        </form>

        <p class="switch-text">Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
