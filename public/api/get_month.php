<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../config/table.php';

$col = '対象月';

$sql = "
  SELECT DISTINCT `$col` AS v
  FROM {$DATA_OUTPUT_TABLE}
  WHERE `$col` IS NOT NULL
    AND TRIM(`$col`) <> ''
  ORDER BY v
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => mysqli_error($conn),
        'sql'   => $sql
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
