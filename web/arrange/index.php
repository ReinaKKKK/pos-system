<?php
try {
    $db = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // エラーを例外として処理
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
    exit();
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';  // 空欄でもOK
    $edit_password = isset($_POST['edit_password']) ? $_POST['edit_password'] : '';  // 空欄でもOK

    // nameが空でないか確認
    if (empty($name)) {
        echo "イベント名は必須です。";
        exit;
    }

    
// イベント作成のためのSQLクエリ
$sql = "INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
VALUES (:name, :detail, :edit_password, NOW(), NOW())";

// PDOを使用して実行
$stmt = $db->prepare($sql);
// プレースホルダと変数をバインド
$stmt->bindParam(':name', $name);
$stmt->bindParam(':detail', $detail);
$stmt->bindParam(':edit_password', $edit_password);

// SQL実行
if ($stmt->execute()) {
// イベント作成後にイベントIDを取得
$event_id = $db->lastInsertId();

// イベントURLを作成（イベント詳細ページ）
// $event_url = "save_event.php?event_id=" . $event_id;

// イベント作成後にURLを表示するページにリダイレクト
// header("Location: $event_url");
header("Location: save_event.php?event_id=" . $event_id);
exit();  // リダイレクト後に処理を終了
} else {
// エラー時に詳細を表示
$errorInfo = $stmt->errorInfo();
echo "イベントの作成に失敗しました。エラー情報: " . print_r($errorInfo, true);
}
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント作成</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルをリンク -->
    <script src="script.js"></script><!-- 外部CSSファイルをリンク -->
</head>
<body>
    <h2>新しいイベントを作成</h2>
    <form action="index.php" method="POST">
        <label>イベント名:</label>
        <input type="text" name="name" required><br><br>

        <label>イベント詳細:</label>
        <textarea name="detail"></textarea><br><br>

        <label>編集パスワード:</label>
        <input type="password" name="edit_password"><br><br>

        <label>候補日時:</label>
        <div id="timeSlots">
            <!-- 最初の日時入力欄 -->
            <div class="time-slot">
                <label>日付:</label>
                <input type="date" name="dates[]" required>
                <label>From:</label>
                <input type="time" name="start_times[]" required>
                <label>To:</label>
                <input type="time" name="end_times[]" required><br>
                <button type="button" class="remove-button" onclick="removeTimeSlot(this)">削除</button><br>
            </div>
        </div>
        <button type="button" onclick="addTimeSlot()">時間帯を追加</button><br><br>

        <input type="submit" value="イベントを作成">
    </form>
</body>
</html>
