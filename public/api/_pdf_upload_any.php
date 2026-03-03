<?php
$token = $_POST['token'] ?? '';
if ($token !== 'UPLOAD_SECRET_2025') { http_response_code(403); exit('forbidden'); }

$target = $_POST['target'] ?? '';
$allowed = [
    'sokuho_2025.pdf',
    'graph2025.pdf',
    'revenue.pdf',
    'basic_1.pdf',
    'ranking2025.pdf',
    'chao_yoyaku.pdf',
    'SE_result.pdf',
    'SE_zangyo.pdf',
    'ei_zangyo.pdf',
    'service_rieki.pdf',
    'service_add.pdf',
    'shinki.pdf',
    'jishiritu2.pdf',
    'jishiritu.pdf',
    'hoken.pdf',
    'attack_6m.pdf',
    'chao_mijishi.pdf',

  // ... このリストに20個入れる
];

if (!in_array($target, $allowed, true)) { http_response_code(400); exit('invalid target'); }
if (!isset($_FILES['pdf'])) { http_response_code(400); exit('no file'); }

$saveDir  = __DIR__ . '/../../assets/img/';
$saveFile = $saveDir . $target;

if (!is_dir($saveDir)) mkdir($saveDir, 0755, true);

if (move_uploaded_file($_FILES['pdf']['tmp_name'], $saveFile)) {
  echo json_encode(['status'=>'ok','path'=>"/work5/assets/img/$target"]);
} else {
  http_response_code(500); echo 'upload failed';
}