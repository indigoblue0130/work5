<?php
session_start();
// require_once __DIR__ . '/config/config2.php';

// 変更後(本番用)
require_once '/home/xs300844/triple3.online/config/config2.php';

/* 未ログイン */
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

/* 初回以外は通さない */
if (empty($_SESSION['force_pw_setup'])) {
    header('Location: index.php');
    exit;
}

/* DB接続 */
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset('utf8mb4');

$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pw1 = $_POST['new_password'] ?? '';
    $pw2 = $_POST['new_password_confirm'] ?? '';

    if ($pw1 === '' || $pw2 === '') {
        $error = 'パスワードを入力してください。';
    } elseif ($pw1 !== $pw2) {
        $error = '確認用パスワードが一致しません。';
    } elseif (mb_strlen($pw1) < 8) {
        $error = 'パスワードは8文字以上にしてください。';
    } elseif (!preg_match('/[a-zA-Z]/', $pw1) || !preg_match('/[0-9]/', $pw1)) {
        $error = 'パスワードは英字と数字をそれぞれ1文字以上含めてください。';
    } else {

        $hash = password_hash($pw1, PASSWORD_BCRYPT);

        $sql = '
            UPDATE users
            SET password_hash = ?, password_initialized = 1
            WHERE username = ?
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $hash, $_SESSION['username']);
        $stmt->execute();

        unset($_SESSION['force_pw_setup']);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>初回パスワード設定</title>
<link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<h2>初回パスワード設定</h2>

<?php if ($done): ?>
  <p>設定が完了しました。トップページへ移動します。</p>
  <script>
    setTimeout(() => location.href = 'index.php', 800);
  </script>
<?php else: ?>

  <p>初回ログインのため、パスワードを設定してください。</p>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>新しいパスワード（8文字以上）</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>新しいパスワード（確認）</label><br>
    <input type="password" name="new_password_confirm" required><br><br>

    <button type="submit">設定する</button>
  </form>

<?php endif; ?>

</body>
</html>
