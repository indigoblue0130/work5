<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===== ログインしていない場合は何もしない ===== */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    return;
}

/* ===== ログフォルダ ===== */
$log_directory = '/home/xs300844/triple3.online/log';

if (!is_dir($log_directory)) {
    mkdir($log_directory, 0755, true);
}

$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

/* ===== 基本情報 ===== */
$username = $_SESSION['username'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

/* ===== DEVICE 判定 ===== */
$device = 'PC';

if (stripos($ua, 'ipad') !== false) {
    $device = 'iPad';
} elseif (stripos($ua, 'iphone') !== false || stripos($ua, 'android') !== false) {
    $device = 'Mobile';
}

/* ===================================================
   CONTENTログ（PDF・動画など閲覧）
=================================================== */

if (isset($_GET['content']) && $_GET['content'] !== '') {

    $content = basename($_GET['content']);

    $log = date("Y-m-d H:i:s")
        . " | ID: {$username}"
        . " | CONTENT: {$content}"
        . " | DEVICE: {$device}\n";

    file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);

    return;
}

/* ===================================================
   PAGEログ（通常アクセス）
=================================================== */

/* クエリ削除 */
$page = strtok($_SERVER['REQUEST_URI'] ?? '-', '?');

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | PAGE: {$page}"
    . " | DEVICE: {$device}\n";

file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);