<?php
session_start();

// ログインチェック
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

/* ===== アクセスログ ===== */
$log_directory = '/home/xs300844/triple3.online/log';

if (!file_exists($log_directory)) {
    mkdir($log_directory, 0755, true);
}

$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

$username = $_SESSION['username'] ?? '不明ユーザー';
$access_page = $_SERVER['REQUEST_URI'];   // ← この行を追加
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$referer = $_SERVER['HTTP_REFERER'] ?? '-';
$session_id = session_id();

$log = date("Y-m-d H:i:s")
    . " | SID: {$session_id}"
    . " | ID: {$username}"
    . " | PAGE: {$access_page}"
    . " | REF: {$referer}"
    . " | IP: {$ip_address}"
    . " | UA: {$user_agent}\n";

file_put_contents($log_file, $log, FILE_APPEND);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style4.css">
    <title>即決営業・乾さん動画</title>
</head>
<body>
    <div class="container">
        <h1>即決営業・乾さん動画</h1>
    </div>
        <div class="video-container">
        <div class="video-item">
            <h2>動画8:高いと言われた時編</h2>
            <video controls>
                <source src="../videos/inui_10.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画9:即決をトル営業編</h2>
            <video controls>
                <source src="../videos/inui_9.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画10:考えますを封じ込める編</h2>
            <video controls>
                <source src="../videos/inui_8.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画5:売れる営業になる編1</h2>
            <video controls>
                <source src="../videos/inui_ureru1.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画6:売れる営業になる編2</h2>
            <video controls>
                <source src="../videos/inui_ureru2.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画7:売れる営業になる編3</h2>
            <video controls>
                <source src="../videos/inui_ureru3.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画1: 訴求 編</h2>
            <video controls>
                <source src="../videos/inui_1.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画2: バンドワゴン効果 編</h2>
            <video controls>
                <source src="../videos/inui_2.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画3: 一貫性 編</h2>
            <video controls>
                <source src="../videos/inui_3.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画4: ウインザー効果 編</h2>
            <video controls>
                <source src="../videos/inui_4.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
    </div>
</body>
</html>
