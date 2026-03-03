<?php
// ========= 簡易認証 =========
$token = $_POST['token'] ?? '';
if ($token !== 'UPLOAD_SECRET_2025') {
    http_response_code(403);
    exit('forbidden');
}

// ========= ファイルチェック =========
if (!isset($_FILES['pdf'])) {
    http_response_code(400);
    exit('no file');
}

// ========= 保存先 =========
$saveDir = __DIR__ . '/../../assets/img/';
$saveFile = $saveDir . 'sokuho_2025.pdf';

// ========= ディレクトリ作成 =========
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

// ========= 保存 =========
if (move_uploaded_file($_FILES['pdf']['tmp_name'], $saveFile)) {
    echo json_encode([
        'status' => 'ok',
        'path'   => '/work5/assets/img/sokuho_2025.pdf'
    ]);
} else {
    http_response_code(500);
    echo 'upload failed';
}