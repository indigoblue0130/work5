<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   ログインしているユーザーのみ記録
========================= */
if (isset($_SESSION['username'])) {

    /* ===== ログ保存先 ===== */
    $log_directory = '/home/xs300844/triple3.online/log';

    /* ===== 本日の日付 ===== */
    $today = date('Y-m-d');

    /* ===== ログファイル ===== */
    $log_file = $log_directory . "/access_log_{$today}.txt";

    /* ===== ユーザー情報 ===== */
    $username = $_SESSION['username'];
    $access_page = $_SERVER['REQUEST_URI'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    /* ===== 参照元 ===== */
    $referer = $_SERVER['HTTP_REFERER'] ?? '-';

    /* ===== ログ作成 ===== */
    $log = date("Y-m-d H:i:s")
        . " | ID: {$username}"
        . " | PAGE: {$access_page}"
        . " | IP: {$ip_address}"
        . " | REF: {$referer}"
        . " | UA: {$user_agent}\n";

    /* ===== ログ書き込み ===== */
    file_put_contents($log_file, $log, FILE_APPEND);
}
?>