<?php
session_start();

/* セッション変数をすべて削除 */
$_SESSION = [];

/* セッションCookieを削除 */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* セッションを完全破棄 */
session_destroy();

/* 新しいセッションIDを強制発行（再利用防止） */
session_regenerate_id(true);

/* ログイン画面へ */
header("Location: login.html");
exit;
