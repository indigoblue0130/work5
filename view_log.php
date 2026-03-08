<?php
session_start();

// 指定されたユーザーID以外はアクセス拒否
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hdn0136656') {
    echo "アクセス権限がありません。";
    exit();
}

// ログフォルダと表示するファイルのパス
$log_dir = '/home/xs300844/triple3.online/log';
$file = $_GET['file'] ?? '';
$filepath = $log_dir . '/' . basename($file);

// ファイル存在チェック
if (!file_exists($filepath)) {
    echo "ログファイルが見つかりません。";
    exit();
}

// ログ内容を1行ずつ読み込む
$log_lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログ表示 - <?php echo htmlspecialchars($file); ?></title>
    <style>
        body {
            font-family: monospace;
            background: #f4f4f4;
            padding: 20px;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        pre {
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            white-space: pre-wrap;
            word-break: break-all;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($file); ?> のログ内容</h1>
    <pre>
<?php
foreach ($log_lines as $line) {
    echo htmlspecialchars($line) . "\n";
}
?>
    </pre>
    <a href="admin_logs.php">← ログ一覧に戻る</a>
</body>

<a href="download_log.php?file=<?php echo urlencode($file); ?>" target="_blank">
    📥 このログをCSVでダウンロード
</a>

</html>
