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
$log_directory = __DIR__ . '/logs';
if (!file_exists($log_directory)) {
    mkdir($log_directory, 0777, true);
}

$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

$username   = $_SESSION['username'] ?? '不明ユーザー';
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | PDFアクセス: {$pdf}"
    . " | IP: {$ip_address}"
    . " | ブラウザ: {$user_agent}\n";

file_put_contents($log_file, $log, FILE_APPEND);

/* ===== PDF 出力 ===== */
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($target) . '"');
header('Content-Length: ' . filesize($target));
readfile($target);
exit;
