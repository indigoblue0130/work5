<?php
/**
 * 点検対象 検索API（完成版）
 * ・config/table.php によるテーブル名集中管理
 * ・プリペアドステートメント使用
 * ・SQLエラー時も JSON を返却（Fatal防止）
 * ・show_done 制御対応
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../config/table.php';

/* =========================
   パラメータ取得
========================= */
$kyoten = $_GET['kyoten'] ?? '';
$tanto  = $_GET['tanto'] ?? '';
$month  = $_GET['month'] ?? '';
$work   = $_GET['work_type'] ?? '';
$show_done = ($_GET['show_done'] ?? '0') === '1' ? '1' : '0';

/* =========================
   SQL構築
========================= */
$sql = "
SELECT *
FROM {$DATA_OUTPUT_TABLE}
WHERE 拠点名 = ?
  AND 担当者名 = ?
  AND 対象月 = ?
  AND 作業種別 = ?
";

if ($show_done === '0') {
    $sql .= " AND arrived = 0";
}

// 取得上限（将来変更しやすい）
$LIMIT = 50;
$sql .= " LIMIT {$LIMIT}";

/* =========================
   prepare（失敗時もJSON）
========================= */
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'api'    => 'shaken_fetch.php',
        'error'  => $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* =========================
   bind & execute
========================= */
$stmt->bind_param('ssss', $kyoten, $tanto, $month, $work);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'api'    => 'shaken_fetch.php',
        'error'  => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* =========================
   結果取得
========================= */
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

/* =========================
   JSON出力
========================= */
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'status' => 'ok',
    'count'  => count($data),
    'data'   => $data
], JSON_UNESCAPED_UNICODE);
exit;
