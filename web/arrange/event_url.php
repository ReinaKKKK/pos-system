<?php
// MySQL接続情報
$servername = "127.0.0.1"; // ソケットの問題を避けるために localhost の代わりに IP を使用
$username = "root";
$password = "";  // パスワードがない場合
$dbname = "arrange";
$port = 3306; // MySQLのポートを3306に設定

// MySQL接続の作成
try {
    $db = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーを例外として処理
} catch (PDOException $e) {
    // エラーが発生した場合はログに記録
    error_log('DB接続エラー: ' . $e->getMessage());
    echo 'データベース接続に失敗しました。しばらくしてから再度お試しください。';
    exit;
}

// イベントIDをURLパラメータから取得
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // イベント情報をデータベースから取得
    $sql = "SELECT * FROM events WHERE id = :event_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['event_id' => $event_id]);

    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        $event_url = "https://yourdomain.com/event_response.php?event_id=" . $event['id'];  // 参加者が入力するURL
    } else {
        echo "指定されたイベントが見つかりません。";
        exit;
    }
} else {
    echo "イベントIDが指定されていません。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベントURL</title>
</head>
<body>

<h1>イベントURLの確認</h1>

<p>イベント作成が完了しました！参加者に送るURLはこちらです：</p>
<p><a href="<?php echo htmlspecialchars($event_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($event_url, ENT_QUOTES, 'UTF-8'); ?></a></p>

</body>
</html>
