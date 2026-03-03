<?php
session_start();

// ログイン状態を確認
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お客様コメント</title>
    <link rel="stylesheet" href="./assets/style3.css"> <!-- 外部CSSファイルを読み込む -->
</head>

<body>
    <?php
    // データベース接続情報
    require_once 'config.php'; // 設定ファイルを読み込み

    try {
        // PDOを使用してデータベースに接続
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL文を準備して実行
        // $sql = "SELECT * FROM comment4";
        $sql = "SELECT * FROM spika_comment202501";
        $stmt = $pdo->query($sql);

        // データをHTMLテーブルとして表示
        echo "<table>";
        echo "<tr><th>No.</th><th>拠点名</th><th>長期お付合い意向</th><th>ユーザーコメント</th><th>店舗コメント</th></tr>";

        // データ行をループで表示
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['No.']) . "</td>";
            echo "<td>" . htmlspecialchars($row['拠点名']) . "</td>";
            echo "<td>" . htmlspecialchars($row['長期お付合い意向']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ユーザーコメント']) . "</td>";
            echo "<td>" . htmlspecialchars($row['店舗コメント']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
    ?>
</body>
</html>
