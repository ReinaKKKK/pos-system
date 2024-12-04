<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_POST['event_id']) && isset($_POST['user_id'])) {
    $eventId = (int)$_POST['event_id'];
    $user_id = $_POST['user_id'];

    // パスワード認証
    $stmt = $pdo->prepare('SELECT id FROM responses WHERE event_id = :event_id AND user_id = :user_id');
    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $isAuthenticated = $stmt->fetchColumn() !== false;

    if ($isAuthenticated) {
        // 参加者の回答を編集
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = $_POST['response'];
            $comment = $_POST['comment'];

            // 回答を更新
            $stmt = $pdo->prepare('UPDATE responses SET response = :response, comment = :comment WHERE event_id = :event_id AND user_id = :user_id');
            $stmt->bindValue(':response', $response, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->execute();
            echo "回答が更新されました。";
        } else {
            // 回答フォームを表示
            echo "<form method='POST'>
                    <label for='response'>回答: </label>
                    <input type='radio' name='response' value='1'> 〇
                    <input type='radio' name='response' value='2'> ×
                    <input type='radio' name='response' value='3'> △<br>
                    <textarea name='comment' placeholder='コメント'></textarea><br>
                    <button type='submit'>更新</button>
                  </form>";
        }
    } else {
        echo "パスワードが正しくありません。";
    }
} else {
    echo "イベントIDとパスワードが必要です。";
}
