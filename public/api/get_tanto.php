<?php
/**
 * 担当者一覧取得API
 * ・config/table.php 使用
 * ・拠点指定必須
 * ・エラー時も JSON を返す（安全仕様）
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../config/table.php';

$kyoten = $_GET['kyoten'] ?? '';
if ($kyoten === '') {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT DISTINCT `担当者名`
    FROM {$DATA_OUTPUT_TABLE}
    WHERE `拠点名` = ?
      AND `担当者名` IS NOT NULL
      AND REPLACE(`担当者名`, '　', '') <> ''
    ORDER BY `担当者名`
";

/* ===== prepare ===== */
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'error' => mysqli_error($conn),
        'api'   => 'get_tanto.php'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===== bind / execute ===== */
mysqli_stmt_bind_param($stmt, 's', $kyoten);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode([
        'error' => mysqli_stmt_error($stmt),
        'api'   => 'get_tanto.php'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===== fetch ===== */
$result = mysqli_stmt_get_result($stmt);
$list = [];

while ($row = mysqli_fetch_row($result)) {
    $list[] = $row[0];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($list, JSON_UNESCAPED_UNICODE);
exit;
