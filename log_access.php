<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ログイン中ユーザーのみログ記録
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $log_directory = '/home/xs300844/triple3.online/log';
    $today = date('Y-m-d');
    $log_file = $log_directory . "/access_log_{$today}.txt";

    if (!file_exists($log_directory)) {
        mkdir($log_directory, 0777, true);
    }

    $username = $_SESSION['username'] ?? '不明ユーザー';
    $access_page = $_SERVER['REQUEST_URI'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $log = date("Y-m-d H:i:s")
        . " | ID: {$username}"
        . " | アクセス: {$access_page}"
        . " | IP: {$ip_address}"
        . " | ブラウザ: {$user_agent}\n";

    file_put_contents($log_file, $log, FILE_APPEND);
}
?>
