<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('profile.php');
}

$errors = [];
$old    = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = post('name');
    $email     = mb_strtolower(post('email'));
    $password  = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');
    $old       = ['name' => $name, 'email' => $email];

    // ---- Серверлік валидация ----
    if (!csrf_ok()) {
        $errors[] = 'Қауіпсіздік тексеруі өтпеді. Бетті жаңартып, қайталаңыз.';
    }
    if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
        $errors[] = 'Аты-жөні 2-ден 50 таңбаға дейін болуы керек.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Электрондық пошта дұрыс емес.';
    } elseif (find_user_by_email($email) !== null) {
        $errors[] = 'Бұл пошта бұрын тіркелген.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Құпиясөз кемінде 6 таңбадан тұруы керек.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Құпиясөзде кемінде бір әріп және бір сан болуы керек.';
    }
    if ($password !== $password2) {
        $errors[] = 'Құпиясөздер сәйкес келмейді.';
    }

    // ---- Қате болмаса — сақтаймыз ----
    if (!$errors) {
        $users   = load_users();
        $users[] = [
            'id'         => count($users) + 1,
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT), // ашық құпиясөз сақталмайды
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if (save_users($users)) {
            flash('Тіркеу сәтті аяқталды! Енді жүйеге кіріңіз.');
            redirect('login.php');
        }
        $errors[] = 'Деректерді сақтау мүмкін болмады (data/ қалтасына жазу рұқсатын тексеріңіз).';
    }
}

$pageTitle   = 'Тіркелу';
$currentPage = 'register';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-background">
    <div class="auth-card">
        <div class="auth-icon">✨</div>
        <h1>Тіркелу</h1>
        <p class="muted">Жаңа аккаунт ашу үшін форманы толтырыңыз.</p>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <?= csrf_field() ?>

            <label for="name">Аты-жөніңіз</label>
            <input id="name" name="name" type="text" required minlength="2" maxlength="50"
                   value="<?= e($old['name']) ?>" placeholder="Аты-жөніңіз">

            <label for="email">Электрондық пошта</label>
            <input id="email" name="email" type="email" required
                   value="<?= e($old['email']) ?>" placeholder="name@example.com">

            <label for="password">Құпиясөз</label>
            <input id="password" name="password" type="password" required minlength="6"
                   placeholder="Кемінде 6 таңба, әріп және сан">

            <label for="password2">Құпиясөзді қайталау</label>
            <input id="password2" name="password2" type="password" required minlength="6"
                   placeholder="Құпиясөзді қайталаңыз">

            <button class="btn full-width" type="submit">Аккаунт ашу</button>
        </form>

        <p class="switch-text">Аккаунтыңыз бар ма? <a href="login.php">Кіру</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
