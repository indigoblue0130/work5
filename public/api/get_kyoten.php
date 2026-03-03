<?php
/**
 * 拠点一覧取得API（mysqli版・本番用）
 * ・config/table.php 使用
 * ・空白 / 全角スペースのみの拠点名は除外
 * ・JSONのみを返す（エラー時もJSON）
 */

require_once __DIR__ . '/../../config/table.php';
require_once __DIR__ . '/db.php';

$sql = "
    SELECT DISTINCT `拠点名`
    FROM {$DATA_OUTPUT_TABLE}
    WHERE `拠点名` IS NOT NULL
      AND REPLACE(`拠点名`, '　', '') <> ''
    ORDER BY `拠点名`
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => mysqli_error($conn),
        'api'   => 'get_kyoten.php'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$kyoten = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kyoten[] = $row['拠点名'];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($kyoten, JSON_UNESCAPED_UNICODE);
exit;
