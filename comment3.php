<?php
session_start();

// ログインチェック
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// アクセスログ記録処理
$log_directory = 'logs';
$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

if (!file_exists($log_directory)) {
    mkdir($log_directory, 0777, true);
}

$username = $_SESSION['username'] ?? '不明ユーザー';
$access_page = basename($_SERVER['PHP_SELF']);
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | アクセス: {$access_page}"
    . " | IP: {$ip_address}"
    . " | ブラウザ: {$user_agent}\n";

file_put_contents($log_file, $log, FILE_APPEND);

// config.php を読み込む
// require_once 'config2.php'
require_once './config/config2.php';

// データベースに接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// 接続エラー確認
if ($conn->connect_error) {
    die("データベース接続エラー: " . $conn->connect_error);
}

// 拠点名リストを取得 ファイル名を毎月変更
$location_query = "SELECT DISTINCT `拠点名` FROM spika_comment202602";
$location_result = $conn->query($location_query);

// フィルタ用の変数を設定
$selected_location = isset($_GET['location']) ? $_GET['location'] : '';

// SQLクエリ（フィルタ適用）　ファイル名を毎月変更
$sql = "SELECT * FROM spika_comment202602";
if (!empty($selected_location)) {
    $sql .= " WHERE `拠点名` = '" . $conn->real_escape_string($selected_location) . "'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お客様コメント</title>
    <link rel="stylesheet" href="./assets/css/style3.css"> <!-- 外部CSSファイルを読み込む -->
</head>

<body>

<h1>お客様コメント</h1>

<!-- フィルタ用フォーム -->
<form method="GET">
    <label for="location">拠点名で絞り込む:</label>
    <select name="location" id="location">
        <option value="">全て表示</option>
        <?php
        if ($location_result->num_rows > 0) {
            while ($row = $location_result->fetch_assoc()) {
                $selected = ($row['拠点名'] == $selected_location) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($row['拠点名']) . "' $selected>" . htmlspecialchars($row['拠点名']) . "</option>";
            }
        }
        ?>
    </select>
    <button type="submit">検索</button>
</form>

<!-- テーブルの表示 -->
<table>
    <tr>
        <th>No.</th>
        <th>拠点名</th>
        <th>長期お付合い意向</th>
        <th>ユーザーコメント</th>
        <th>店舗コメント</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['No.']) . "</td>";
            echo "<td>" . htmlspecialchars($row['拠点名']) . "</td>";
            echo "<td>" . htmlspecialchars($row['長期お付合い意向']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ユーザーコメント']) . "</td>";
            echo "<td>" . htmlspecialchars($row['店舗コメント']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>データがありません</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php
$conn->close();
?>
