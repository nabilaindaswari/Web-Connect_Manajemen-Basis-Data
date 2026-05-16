<?php
session_start();

// // Redirect to HTTPS if this request is not secure
// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
//     $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//     header('Location: ' . $redirectUrl);
//     exit;
// }

const MAX_LOGIN_ATTEMPTS = 10000;
const LOCKOUT_SECONDS = 0; // 5 minutes

function redirectWithError(string $error): void
{
    header('Location: ../public/login.php?error=' . urlencode($error));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('auth');
}

$username = trim((string)($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    redirectWithError('empty');
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = 0;
}

if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS && time() - $_SESSION['last_login_attempt'] < LOCKOUT_SECONDS) {
    redirectWithError('auth');
}

$_SESSION['last_login_attempt'] = time();

$dsn = 'mysql:host=localhost;dbname=tokosembako;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    redirectWithError('auth');
}

$sql = 'SELECT id_user, username, password_hash FROM users WHERE username = :username LIMIT 1';

$stmt = $pdo->prepare($sql);
$stmt->execute([':username' => $username]);

$user = $stmt->fetch();

$loginValid = false;

if ($user && password_verify($password, $user['password_hash'])) {
    $loginValid = true;

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {

        $newHash = password_hash($password, PASSWORD_DEFAULT);

        $update = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id_user = :id');

        $update->execute([
            ':hash' => $newHash,
            ':id' => $user['id_user']
        ]);
    }
}

if (!$loginValid) {
    $_SESSION['login_attempts'] += 1;
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $_SESSION['lockout_expires'] = time() + LOCKOUT_SECONDS;
    }
    redirectWithError('auth');
}

$_SESSION['login_attempts'] = 0;
unset($_SESSION['lockout_expires']);

session_regenerate_id(true);
$_SESSION['user_id'] = $user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['authenticated'] = true;
$_SESSION['last_activity'] = time();

header('Location: ../public/home.php');
exit;
