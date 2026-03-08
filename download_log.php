<?php
session_start();

// 管理者のみ
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hdn0136656') {
    exit("アクセス権限がありません。");
}

$log_dir = '/home/xs300844/triple3.online/log';

$file = $_GET['file'] ?? '';

if ($file === '') {
    exit("ファイル指定なし");
}

// ファイル名の安全化
$filename = basename($file);

$filepath = $log_dir . '/' . $filename;

if (!file_exists($filepath)) {
    exit("ログファイルが存在しません");
}

// ダウンロードヘッダー
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
exit;
