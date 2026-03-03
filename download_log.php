<?php
session_start();

// アクセス制限（必要に応じて）
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hdn0136656') {
    echo "アクセス権限がありません。";
    exit();
}

// ファイル取得
$log_dir = 'logs/';
$file = $_GET['file'] ?? '';
$filepath = $log_dir . basename($file);

if (!file_exists($filepath)) {
    echo "ログファイルが見つかりません。";
    exit();
}

// ダウンロードヘッダー（Shift-JISに変換してExcel対応）
header('Content-Type: text/csv; charset=Shift_JIS');
header('Content-Disposition: attachment; filename="' . $file . '.csv"');

// 内容を1行ずつ出力
$lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    echo mb_convert_encoding($line . "\r\n", 'SJIS-win', 'UTF-8');
}
exit();
