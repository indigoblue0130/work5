<?php
session_start();

$csvFile = 'users.csv';
$users = array();
if (($handle = fopen($csvFile, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $users[$data[0]] = $data[1];
    }
    fclose($handle);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$login_success = false;

// ユーザー認証チェック
if (isset($users[$username]) && password_verify($password, $users[$username])) {
    $_SESSION['username'] = $username;
    $_SESSION['loggedin'] = true;
    $_SESSION['allow_pdf_access'] = true; // PDFアクセス許可
    $login_success = true;
} else {
    $_SESSION['loggedin'] = false;
}

// --- ログイン履歴記録処理（ログを日付別ファイルに保存） ---
$log_directory = 'logs';
$today = date('Y-m-d'); // 例: 2025-04-29
$log_file = $log_directory . "/login_history_{$today}.txt";

// logsフォルダがなければ作成
if (!file_exists($log_directory)) {
    mkdir($log_directory, 0777, true);
}

// ログの中身を作成
$status = $login_success ? '成功' : '失敗';
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | ログイン: {$status}"
    . " | IP: {$ip_address}"
    . " | ブラウザ: {$user_agent}\n";

// ログファイルに追記
file_put_contents($log_file, $log, FILE_APPEND);
// --- ログ記録ここまで ---

// 最後にリダイレクト
if ($login_success) {
    header('Location: index.php');
} else {
    header('Location: login.html');
}
exit();
?>