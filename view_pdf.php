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

/* ===== 閲覧ログ ===== */
$_GET['content'] = basename($target);
require_once __DIR__ . '/log_access.php';

/* ===== PDF 出力 ===== */
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($target) . '"');
header('Content-Length: ' . filesize($target));
readfile($target);
exit;