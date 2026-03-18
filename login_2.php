<?php
session_start();
require_once __DIR__ . '/config/config2.php';

// 本番用
// require_once '/home/xs300844/triple3.online/config/config2.php';

/* DB接続 */
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset('utf8mb4');

/* POST以外は拒否 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.html');
    exit;
}

/* ユーザー取得 */
$sql = '
    SELECT username, password_hash, password_initialized
    FROM users
    WHERE username = ?
       AND is_active = 1 
';

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ID不一致 */
if (!$user) {
    header('Location: login.html');
    exit;
}

/* パスワード照合（bcrypt） */
if (!password_verify($password, $user['password_hash'])) {
    header('Location: login.html');
    exit;
}

/* ログイン成功 */
$_SESSION['username'] = $user['username'];
$_SESSION['loggedin'] = true;

/* 初回ログイン判定 */
if ((int)$user['password_initialized'] === 0) {
    $_SESSION['force_pw_setup'] = 1;
    header('Location: password_setup_2.php');
    exit;
}

/* 通常ログイン */
unset($_SESSION['force_pw_setup']);
header('Location: index.php');
exit;
