<?php

require_once('/var/www/html/arrange/database/db.php');

// デバッグ用コード: POSTデータとGETデータを確認
echo "<pre>";
echo "POSTデータ:\n";
print_r($_POST);
echo "</pre>";


// イベントIDと編集パスワードを取得
if (isset($_POST['event_id']) && isset($_POST['edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $edit_password = $_POST['edit_password'];
    // デバッグ: 受け取ったパスワードを表示
    echo "受け取ったパスワード: " . htmlspecialchars($edit_password) . "<br>";

    // `users` テーブルで参加者用のパスワード認証を確認
    $stmt = $pdo->prepare('SELECT edit_password FROM users WHERE event_id = :event_id');
    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $storedPassword = $stmt->fetchColumn();
    // $samplehash = password_hash('12345678',PASSWORD_DEFAULT);
    // var_dump(password_verify('12345678', $samplehash));
    // パスワードが正しい場合
    // var_dump($storedPassword);
    // var_dump($edit_password);
    // var_dump(password_verify($edit_password, $storedPassword));
    if ($storedPassword && password_verify($edit_password, $storedPassword)) {

        // パスワードが正しい場合
        var_dump(100);
        if (isset($_POST['response'])) {
            var_dump(200);
            $response = (int)$_POST['response'];
            // コメントが空の場合でも処理を行う
            $comment = isset($_POST['comment']) ? $_POST['comment'] : ''; // 空文字を許可

            // データベース更新処理
            $stmt = $pdo->prepare('
            UPDATE responses
            SET response = :response, comment = IFNULL(:comment, "")
            WHERE availability_id IN (
                SELECT id FROM availabilities WHERE event_id = :event_id
            )
        ');
            $stmt->bindValue(':response', $response, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            echo "回答が更新されました。";
        } else {
            echo "無効な入力です。";
        }
    } else {
        echo "パスワードが正しくありません。";
    }
} else {
    echo "イベントIDとパスワードが必要です。";
}
?>

<!-- 編集フォーム -->
<h2>回答を編集</h2>
<form method="POST" action="">
    <label for="event_id">イベントID:</label><br>
    <input type="number" name="event_id" id="event_id" required><br><br>

    <label for="edit_password">編集パスワード:</label><br>
    <input type="password" name="edit_password" id="edit_password" required><br><br>

    <label for="response">回答:</label><br>
    <select name="response" id="response" required>
        <option value="1">参加</option>
        <option value="0">不参加</option>
    </select><br><br>

    <label for="comment">コメント:</label><br>
    <textarea name="comment" id="comment"></textarea><br><br>

    <input type="submit" value="回答を更新">
</form>
