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
    <link rel="stylesheet" href="./assets/css/style5.css">
    <title>中古車業務ガイドライン</title>
</head>
<body>
    <div class="container">
        <h1>中古車業務ガイドライン</h1>
    </div>
    <div class="video-container">
        <div class="video-item">
            <video controls>
                <source src="../videos/chuko_1.mp4" type="video/mp4">
                このブラウザは動画再生に対応していません。
            </video>
        </div>
    </div>
</body>
</html>
