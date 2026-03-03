<?php
/**
 * 作業種別一覧取得API
 * ・config/table.php 使用
 * ・空 / 全角スペースのみの値は除外
 * ・エラー時も JSON を返す（安全仕様）
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../config/table.php';

// 列名（実テーブルに合わせる）
$col = '作業種別';

$sql = "
    SELECT DISTINCT `$col` AS v
    FROM {$DATA_OUTPUT_TABLE}
    WHERE `$col` IS NOT NULL
      AND REPLACE(`$col`, '　', '') <> ''
    ORDER BY v
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => mysqli_error($conn),
        'api'   => 'get_work_type.php'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $list[] = $row['v'];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($list, JSON_UNESCAPED_UNICODE);
exit;
