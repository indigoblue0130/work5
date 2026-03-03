<?php
session_start();

// ログインチェック
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// アクセスログ記録処理
$log_directory = 'logs';
$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

if (!file_exists($log_directory)) {
    mkdir($log_directory, 0777, true);
}

$username = $_SESSION['username'] ?? '不明ユーザー';
$access_page = basename($_SERVER['PHP_SELF']);
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | アクセス: {$access_page}"
    . " | IP: {$ip_address}"
    . " | ブラウザ: {$user_agent}\n";

file_put_contents($log_file, $log, FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/style6.css">
    <title>自己点検シートの着眼点</title>
</head>
<body>
    <div class="container">
        <h1>自己点検シートの着眼点</h1>
    </div>
    <div class="video-container">
        <div class="video-item">
            <a href="https://t-tube.stream.co.jp/?key=12e358e9f27f5c9295df5ed7ba33fa84" target="_blank" rel="noopener noreferrer">
                <img src="./assets/img/jikotenken3.png" alt="動画を見る">
            </a>
        </div>
    </div>
</body>
</html>
