<?php
session_start();

// アクセス制限：特定ユーザーのみ
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hdn0136656') {
    echo "アクセス権限がありません。";
    exit();
}

$log_dir = '/home/xs300844/triple3.online/log';
$log_files = glob($log_dir . 'access_log_*.txt'); // 日付別ログのみ取得
usort($log_files, function ($a, $b) {
    return filemtime($b) - filemtime($a); // 更新日時で降順ソート
});
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログファイル一覧</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        h1 { font-size: 20px; }
        ul { padding-left: 20px; }
        li { margin: 5px 0; }
        a { text-decoration: none; color: #0066cc; }
    </style>
</head>
<body>
    <h1>ログファイル一覧（日付順）</h1>
    <ul>
        <?php foreach ($log_files as $file): ?>
            <?php $filename = basename($file); ?>
            <li>
                <a href="view_log.php?file=<?php echo urlencode($filename); ?>">
                    <?php echo htmlspecialchars($filename); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
