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
    $organizer_password = $_POST['organizer_password'];
    $date = $_POST['date'];
    $start_time = $_POST['startHour'] . ':' . $_POST['startMinute'];
    $end_time = $_POST['endHour'] . ':' . $_POST['endMinute'];

    var_dump($_POST);
    //exit;

    // 候補日リストの受け取り
    $candidates = isset($_POST['candidates']) ? json_decode($_POST['candidates'], true) : [];

    // イベント作成のためのSQLクエリ
    $sql = "INSERT INTO events (name, detail, date, start_time, end_time, created_at, updated_at) 
    VALUES (:title, :memo, :date, :start_time, :end_time, NOW(), NOW())";


    // PDOを使用して実行
    $stmt = $db->prepare($sql);
    
    // プレースホルダと変数をバインド
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':memo', $memo);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    
   // SQL実行
   if ($stmt->execute()) {
    // イベント作成後にイベントIDを取得
    $event_id = $db->lastInsertId();

    // 参加者用URLを作成
    $event_url = "event_url.php?event_id=" . $event_id;

    // イベント作成後にURLを表示するページにリダイレクト
    header("Location: $event_url");
    exit();  // リダイレクト後に処理を終了
} else {
    // エラー時に詳細を表示
    $errorInfo = $stmt->errorInfo();
    echo "イベントの作成に失敗しました。エラー情報: " . print_r($errorInfo, true);
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

    <label>
        日付を選択してください
        <input type='date' id="inputDate" name="date" oninput='dateResult.textContent = this.value'>
    </label>
    <p>
        選択された日付: <span id='dateResult'></span>
    </p>

    <label for="start-time-of-day">時間帯:</label>
    <select id="start-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
    </select><br><br>
    
    <label for="startHour">開始時間:</label>
    <select id="startHour" name="startHour"></select>
    <select id="startMinute" name="startMinute"></select>
    <br><br>

    <label for="end-time-of-day">時間帯:</label>
    <select id="end-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
    </select><br><br>

    <label for="endHour">終了時間:</label>
    <select id="endHour" name="endHour"></select>
    <select id="endMinute" name="endMinute"></select>
    <br><br>

    <button id="btn" type="button">候補日を追加</button>

    <section class="available" id="available-column">
        <h3>選択された候補日</h3>
        <ul id="candidates-list">
            <!-- 候補日がここにリスト表示される -->  
        </ul>
        
    </section>

    <label for="organizer_password">主催者パスワード:</label>
    <input type="password" id="organizer_password" name="organizer_password" required><br><br>
    
    <button type="submit">イベントを作成</button>                 
</form>

</body>
</html>
