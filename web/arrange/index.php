<?php
// MySQL接続情報
$servername = "127.0.0.1"; 
$username = "root";
$password = "";  // パスワードがない
$dbname = "arrange";
$port = 3306; // MySQLのポートを3306に設定

// MySQL接続の作成
try {
    $db = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $memo = $_POST['memo'];
    $datetime = $_POST['datetime'];
    $organizer_password = $_POST['organizer_password'];

    // イベント作成のためのSQLクエリ
    $sql = "INSERT INTO events (name, detail, created_at, updated_at) 
            VALUES (:title, :memo, NOW(), NOW())";

    // PDOを使用して実行
    $stmt = $db->prepare($sql);
    
    // プレースホルダと変数をバインド
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':memo', $memo);

    // SQL実行
    if ($stmt->execute()) {
        // イベント作成後にイベントIDを取得
        $event_id = $db->lastInsertId();

        // 参加者用URLを作成
        $event_url = "event_url.php?event_id=" . $event_id;

        // イベント作成後にURLを表示するページにリダイレクト
        header("Location: event_url.php?event_id=$event_id");
        exit();  // リダイレクト後に処理を終了
    } else {
        echo "イベントの作成に失敗しました。";
    }
}

// データの取得
$sql = "SELECT * FROM events";
$events = $db->prepare($sql);
$events->execute();

// データの表示
if ($events->rowCount() > 0) {
    // 結果を出力
    while ($row = $events->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row["id"] . " - Name: " . $row["name"] . " - Detail: " . $row["detail"] . "<br>";
    }
} else {
    echo "0 results";
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント作成</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>

<h1>イベント作成</h1>

<form action="index.php" method="POST">
    <label for="title">イベントタイトル:</label>
    <input type="text" id="title" name="title" required><br><br>
    
    <label for="memo">詳細:</label>
    <textarea id="memo" name="memo" required></textarea><br><br>

    <!-- 開始時間選択 -->
    <label for="start-time-of-day">時間帯:</label>
    <select id="start-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
    </select><br><br>
    
    <label for="startHour">開始時間:</label>
    <select id="startHour"></select>
    <select id="startMinute"></select><br><br>

    <!-- 終了時間帯選択 -->
    <label for="end-time-of-day">時間帯:</label>
    <select id="end-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
    </select><br><br>

    <!-- 終了時間選択 -->
    <label for="endHour">終了時間:</label>
    <select id="endHour"></select>
    <select id="endMinute"></select><br><br>

    <!-- 追加ボタン -->
    <button id="btn">追加</button>

    <!-- 選択した候補日エリア -->
    <section class="available" id="available-column">
        <!-- 選択された候補日がここに表示される -->
        <div id="available-column"></div>
    </section>

    <label for="organizer_password">主催者パスワード:</label>
    <input type="password" id="organizer_password" name="organizer_password" required><br><br>
    
    <button type="submit">イベントを作成</button>                 

</form>

</body>
</html>
