<?php
// ===== 直アクセス防止（他ページ同様）
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
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


// 経理Tips本編
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>経理Tips 一覧</title>
<link rel="stylesheet" href="./assets/css/style7.css">
</head>
<body>

<section class="wrapper">
  <!-- ★ 見出し用ラッパーを追加 -->
  <div class="pdf-header">
    <h2 class="sec-title">【 経理Tips 一覧 】</h2>
    <h3 class="sec-sub">★クリックするとそのトピックが拡大されます★</h3>
  </div>

  <div class="pdf-grid">
    
    <a href="view_pdf.php?file=keiri/keiriTips_08.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.8</p>
      <p class="pdf-desc">下取の自動車税を領収したとき、どうする？</p>
      <img src="./assets/thumb/keiri/keiriTips_08.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_07.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.7</p>
      <p class="pdf-desc">業務依頼を作成していたときに電話が!?</p> 
    <img src="./assets/thumb/keiri/keiriTips_07.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_06.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.6</p>
      <p class="pdf-desc">登録費用受払表の反映のタイミングはいつ？</p>
      <img src="./assets/thumb/keiri/keiriTips_06.png" alt="">
    </a>    
    
    <a href="view_pdf.php?file=keiri/keiriTips_05.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.5</p>
      <p class="pdf-desc">登録費用出金の一般と車検の違いって何なの？</p>
      <img src="./assets/thumb/keiri/keiriTips_05.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_04.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.4</p>
      <p class="pdf-desc">ＭｙＨｏｎｄａ決済はいつ使えるの？</p>
      <img src="./assets/thumb/keiri/keiriTips_04.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_03.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.3</p>
      <p class="pdf-desc">紙領収証を発行したのに印刷されなかった!?</p>
      <img src="./assets/thumb/keiri/keiriTips_03.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_02.pdf" target="_blank" class="pdf-card">
        <p class="pdf-title">経理Tips Vol.2</p>
      <p class="pdf-desc">領収証の発行区分は何になる？</p>  
      <img src="./assets/thumb/keiri/keiriTips_02.png" alt="">
    </a>

    <a href="view_pdf.php?file=keiri/keiriTips_01.pdf" target="_blank" class="pdf-card">
      <p class="pdf-title">経理Tips Vol.1</p>
      <p class="pdf-desc">レンタカー費用の領収証の発行区分は？</p>
      <img src="./assets/thumb/keiri/keiriTips_01.png" alt="">
    </a>
  </div>
</section>

</body>
</html>