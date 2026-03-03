<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../config/table.php';

$no = $_POST['no'] ?? '';
if ($no === '') {
    echo json_encode(['status' => 'ng', 'msg' => 'no is empty'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "
    UPDATE {$DATA_OUTPUT_TABLE}
    SET arrived = 1,
        arrived_at = NOW()
    WHERE `No.` = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $no);

if (!$stmt->execute()) {
    echo json_encode([
        'status' => 'ng',
        'msg'    => 'db update failed',
        'error'  => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
exit;

