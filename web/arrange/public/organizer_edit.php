<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_POST['event_id']) && isset($_POST['password'])) {
    $eventId = (int)$_POST['event_id'];
    $password = $_POST['password'];

    // 主催者パスワード認証
    $stmt = $pdo->prepare('SELECT edit_password FROM events WHERE id = :event_id');
    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event && $password === $event['edit_password']) {
        // イベント編集
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 新しい日程追加
            if (isset($_POST['start_time']) && isset($_POST['end_time'])) {
                $start_time = $_POST['start_time'];
                $end_time = $_POST['end_time'];

                // 日程を追加
                $stmt = $pdo->prepare('INSERT INTO availabilities (event_id, start_time, end_time) VALUES (:event_id, :start_time, :end_time)');
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
                $stmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
                $stmt->execute();
                echo "新しい日程が追加されました。";
            }
        } else {
            // 日程追加フォーム
            echo "<form method='POST'>
                    <label for='start_time'>新しい日程:</label>
                    <input type='datetime-local' name='start_time' required>
                    <input type='datetime-local' name='end_time' required><br>
                    <button type='submit'>日程を追加</button>
                  </form>";
        }
    } else {
        echo "パスワードが正しくありません。";
    }
} else {
    echo "イベントIDとパスワードが必要です。";
}
