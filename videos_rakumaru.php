<?php
session_start();

// ログインチェック
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// ===== ログ
require_once __DIR__ . '/log_access.php';


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style4.css">
    <title>楽まる完全攻略</title>
</head>
<body>
    <div class="container2">
        <h1>楽まる 商談スクリプト動画</h1>
        <p>受注をプラス１する『武器』にしょう！</p>
    </div>
    <div class="video-container">
        <div class="video-item">
            <h2>動画1: 山田さん</h2>
            <video controls>
                <source src="../videos/yamada.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画2: 堤さん</h2>
            <video controls>
                <source src="../videos/tsutsumi.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
        <div class="video-item">
            <h2>動画3: 北川さん</h2>
            <video controls>
                <source src="../videos/kitagawa.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
    </div>
</body>
</html>
