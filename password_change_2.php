<?php
session_start();
require_once __DIR__ . '/config/config2.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset('utf8mb4');

$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new1    = $_POST['new_password'] ?? '';
    $new2    = $_POST['new_password_confirm'] ?? '';

    if ($current === '' || $new1 === '' || $new2 === '') {
        $error = 'すべて入力してください。';
    } elseif ($new1 !== $new2) {
        $error = '新しいパスワードが一致しません。';
    } elseif (mb_strlen($new1) < 8 ||
             !preg_match('/[a-zA-Z]/', $new1) ||
             !preg_match('/[0-9]/', $new1)) {
        $error = '新しいパスワードは8文字以上で英字と数字を含めてください。';
    } else {
        // 現在のPW確認
        $stmt = $conn->prepare(
            'SELECT password_hash FROM users WHERE username = ?'
        );
        $stmt->bind_param('s', $_SESSION['username']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($current, $user['password_hash'])) {
            $error = '現在のパスワードが正しくありません。';
        } else {
            $new_hash = password_hash($new1, PASSWORD_BCRYPT);
            $stmt = $conn->prepare(
                'UPDATE users SET password_hash = ? WHERE username = ?'
            );
            $stmt->bind_param('ss', $new_hash, $_SESSION['username']);
            $stmt->execute();
            $done = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>パスワード変更</title>
<link rel="stylesheet" href="./assets/css/common.css">
</head>
<body>

<h2>パスワード変更</h2>

<?php if ($done): ?>
  <p>パスワードを変更しました。</p>
<?php else: ?>
  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post">
    <input type="password" name="current_password" placeholder="現在のパスワード" required><br><br>
    <input type="password" name="new_password" placeholder="新しいパスワード" required><br><br>
    <input type="password" name="new_password_confirm" placeholder="新しいパスワード（確認）" required><br><br>
    <button type="submit">変更する</button>
  </form>
<?php endif; ?>

</body>
</html>
