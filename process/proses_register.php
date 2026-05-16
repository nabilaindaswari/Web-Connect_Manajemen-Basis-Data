<?php

session_start();

/*
|--------------------------------------------------------------------------
| SECURITY CONFIG
|--------------------------------------------------------------------------
*/

const MAX_REGISTER_ATTEMPTS = 5;
const LOCKOUT_SECONDS = 300; // 5 menit

/*
|--------------------------------------------------------------------------
| FUNCTION
|--------------------------------------------------------------------------
*/

function redirectWithError(string $error): void
{
    header('Location: ../public/register.php?error=' . urlencode($error));
    exit;
}

function redirectWithSuccess(): void
{
    header('Location: ../public/register.php?success=true');
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDATE REQUEST METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('auth');
}

/*
|--------------------------------------------------------------------------
| BRUTE FORCE PROTECTION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['register_attempts'])) {
    $_SESSION['register_attempts'] = 0;
    $_SESSION['last_register_attempt'] = 0;
}

if (
    $_SESSION['register_attempts'] >= MAX_REGISTER_ATTEMPTS &&
    time() - $_SESSION['last_register_attempt'] < LOCKOUT_SECONDS
) {
    redirectWithError('locked');
}

$_SESSION['last_register_attempt'] = time();

/*
|--------------------------------------------------------------------------
| GET INPUT
|--------------------------------------------------------------------------
*/

$nama = trim((string)($_POST['nama'] ?? ''));
$username = trim((string)($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATE EMPTY INPUT
|--------------------------------------------------------------------------
*/

if (
    $nama === '' ||
    $username === '' ||
    $password === '' ||
    $confirmPassword === ''
) {
    redirectWithError('empty');
}

/*
|--------------------------------------------------------------------------
| INPUT LENGTH VALIDATION
|--------------------------------------------------------------------------
*/

if (
    strlen($nama) > 100 ||
    strlen($username) > 50
) {
    redirectWithError('toolong');
}

/*
|--------------------------------------------------------------------------
| USERNAME VALIDATION
|--------------------------------------------------------------------------
| hanya huruf, angka, underscore
*/

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    redirectWithError('username');
}

/*
|--------------------------------------------------------------------------
| PASSWORD CONFIRMATION
|--------------------------------------------------------------------------
*/

if ($password !== $confirmPassword) {
    redirectWithError('mismatch');
}

/*
|--------------------------------------------------------------------------
| PASSWORD STRENGTH
|--------------------------------------------------------------------------
| minimal:
| - 8 karakter
| - huruf besar
| - huruf kecil
| - angka
| - simbol
*/

$passwordStrong =
    strlen($password) >= 8 &&
    preg_match('/[A-Z]/', $password) &&
    preg_match('/[a-z]/', $password) &&
    preg_match('/[0-9]/', $password) &&
    preg_match('/[\W]/', $password);

if (!$passwordStrong) {
    redirectWithError('weak');
}

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

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

    error_log('Database Connection Failed: ' . $e->getMessage());

    redirectWithError('server');
}

/*
|--------------------------------------------------------------------------
| CHECK USERNAME EXISTS
|--------------------------------------------------------------------------
*/

$sqlCheck = '
    SELECT id_user
    FROM users
    WHERE username = :username
    LIMIT 1
';

$stmtCheck = $pdo->prepare($sqlCheck);

$stmtCheck->execute([
    ':username' => $username
]);

$userExists = $stmtCheck->fetch();

if ($userExists) {

    $_SESSION['register_attempts'] += 1;

    redirectWithError('exists');
}

/*
|--------------------------------------------------------------------------
| HASH PASSWORD
|--------------------------------------------------------------------------
*/

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

/*
|--------------------------------------------------------------------------
| INSERT USER
|--------------------------------------------------------------------------
*/

$sqlInsert = '
    INSERT INTO users (
        nama_lengkap,
        username,
        password_hash,
        access_level
    )
    VALUES (
        :nama,
        :username,
        :password_hash,
        0
    )
';

$stmtInsert = $pdo->prepare($sqlInsert);

try {

    $stmtInsert->execute([
        ':nama' => htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'),
        ':username' => $username,
        ':password_hash' => $passwordHash
    ]);

} catch (PDOException $e) {

    error_log('Register Failed: ' . $e->getMessage());

    redirectWithError('server');
}

/*
|--------------------------------------------------------------------------
| RESET REGISTER ATTEMPTS
|--------------------------------------------------------------------------
*/

$_SESSION['register_attempts'] = 0;

/*
|--------------------------------------------------------------------------
| REGENERATE SESSION
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

redirectWithSuccess();