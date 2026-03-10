<?php
// ================================
// API: CSVアップロード → UPDATE（テーブル固定）
// 条件: 車台番号 + 対象月(文字列) + 作業種別
// 更新: サービス予約日 (+ arrived)
// ================================

// ローカル用
require_once __DIR__ . '/../../config/config2.php';

// 変更後(本番用)
// require_once '/home/xs300844/triple3.online/config/config2.php';

// ---- 設定 ----
const API_KEY = 'a9F$3KxP1vR7WmZ2NQ8J@6cE!H0sY4B*D5';
const TARGET_TABLE = 'data_output_202603';

// ---- JSONレスポンス ----
header('Content-Type: application/json; charset=utf-8');

// ---- メソッド制限 ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

// ---- APIキー認証 ----
if (!isset($_POST['api_key']) || $_POST['api_key'] !== API_KEY) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// ---- CSVチェック ----
if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'CSV upload failed']);
    exit;
}

$path = $_FILES['csv']['tmp_name'];

// ---- DB接続 ----
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connect error']);
    exit;
}
$mysqli->set_charset('utf8mb4');

// ---- CSV open ----
$fp = fopen($path, 'r');
if (!$fp) {
    http_response_code(400);
    echo json_encode(['error' => 'CSV open error']);
    exit;
}

/**
 * 区切り文字 自動判定
 */
$firstLine = fgets($fp);
$firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

$delims = [",", "\t", ";"];
$delimiter = ",";
$max = -1;

foreach ($delims as $d) {
    $c = substr_count($firstLine, $d);
    if ($c > $max) {
        $max = $c;
        $delimiter = $d;
    }
}

// ---- ヘッダー ----
$header = array_map('trim', str_getcsv($firstLine, $delimiter));
$cols = array_flip($header);

// ---- 必須列 ----
$required = ['車台番号', '対象月', '作業種別', 'サービス予約日'];
foreach ($required as $r) {
    if (!isset($cols[$r])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing column: {$r}"]);
        exit;
    }
}

// ---- UPDATE準備 ----
$sql = "
    UPDATE " . TARGET_TABLE . "
    SET サービス予約日 = ?,
        arrived = 1
    WHERE 車台番号 = ?
      AND 対象月 = ?
      AND 作業種別 = ?
";
$stmt = $mysqli->prepare($sql);

$updated = 0;
$ignored = 0;

// ---- データ処理 ----
while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {

    $car   = trim($row[$cols['車台番号']] ?? '');
    $month = trim($row[$cols['対象月']] ?? '');
    $work  = trim($row[$cols['作業種別']] ?? '');
    $date  = trim($row[$cols['サービス予約日']] ?? '');

    if ($car === '' || $month === '' || $work === '') {
        $ignored++;
        continue;
    }

    $dateParam = ($date === '') ? null : $date;

    $stmt->bind_param('ssss', $dateParam, $car, $month, $work);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $updated += $stmt->affected_rows;
    } else {
        $ignored++;
    }
}

// ---- 後処理 ----
fclose($fp);
$stmt->close();
$mysqli->close();

// ---- 完了 ----
echo json_encode([
    'status'  => 'ok',
    'updated' => $updated,
    'ignored' => $ignored
]);