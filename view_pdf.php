<?php
session_start();

/* ===== 認証チェック ===== */
if (
    !isset($_SESSION['loggedin']) ||
    $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['allow_pdf_access']) ||
    $_SESSION['allow_pdf_access'] !== true
) {
    header('Location: login.html');
    exit();
}

/* ===== ファイル取得 ===== */
$pdf = $_GET['file'] ?? '';

/* ===== 危険文字チェック ===== */
if ($pdf === '' || strpos($pdf, '..') !== false) {
    exit('不正なファイル名です。');
}

/* ===== PDF 保存ディレクトリ ===== */
$baseDir = realpath(__DIR__ . '/assets/pdf');
$target  = realpath($baseDir . '/' . $pdf);

/* ===== パス検証 ===== */
if (
    $target === false ||
    strpos($target, $baseDir) !== 0 ||
    strtolower(pathinfo($target, PATHINFO_EXTENSION)) !== 'pdf'
) {
    exit('不正なファイル名です。');
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

/* ===== PDF 出力 ===== */
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($target) . '"');
header('Content-Length: ' . filesize($target));
readfile($target);
exit;
