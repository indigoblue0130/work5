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
    <link rel="stylesheet" href="./assets/css/style4.css">
    <title>TOPS2024</title>
</head>
<body>
    <div class="container">
        <h1>TOPS2024予選 優秀賞スタッフ 動画</h1>
    </div>
    <div class="video-container">
        <div class="video-item">
            <h2>動画1: 営業スタッフ(樋口さん)</h2>
            <video controls>
                <source src="../videos/higuchi.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画2: 営業スタッフ(岡村さん)</h2>
            <video controls>
                <source src="../videos/okamura.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画3: CAスタッフ(野川さん)</h2>
            <video controls>
                <source src="../videos/nogawa.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画4: CAスタッフ(岸本さん/音量に注意:雑音あり)</h2>
            <video controls>
                <source src="../videos/kisimoto.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
    </div>
</body>
</html>
