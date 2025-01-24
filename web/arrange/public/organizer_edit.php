<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_POST['event_id'], $_POST['name'], $_POST['edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $name = $_POST['name'];
    $EventPassword = $_POST['edit_password'];
    try {
        // データベースからイベントデータを取得してパスワードを検証
        $stmt = $pdo->prepare('SELECT id, edit_password FROM events WHERE event_id = :event_id AND name = :name');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT); // event_id をバインド。プレースホルダに実際の値が置き換えられる
        $stmt->bindValue(':name', $name, PDO::PARAM_STR); // name をstring型でバインド
        $stmt->execute(); // クエリ実行
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // 結果を取得
        // ユーザーが見つからない、またはパスワードが一致しない場合
        if (!$user || !password_verify($participantPassword, $user['edit_password'])) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        }
        // 名前とパスワードがOKなら、特定の参加者の回答を取得
        $userId = $user['id']; // users テーブルから取得した id が user_id に相当
        $stmt = $pdo->prepare('

        ');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT); // 特定の user_id をバインド
        $stmt->execute(); // クエリ実行
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC); // 特定の参加者の回答を取得
    } catch (PDOException $e) {
        // データベースエラーの処理
        echo 'データベースエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDまたはパスワードが無効です。</p>';
    exit;
}
//     // 主催者パスワード認証
//     $stmt = $pdo->prepare('SELECT edit_password FROM events WHERE id = :event_id');
//     $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
//     $stmt->execute();
//     $event = $stmt->fetch(PDO::FETCH_ASSOC);

//     if ($event && $password === $event['edit_password']) {
//         // イベント編集
//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             // 新しい日程追加
//             if (isset($_POST['start_time']) && isset($_POST['end_time'])) {
//                 $start_time = $_POST['start_time'];
//                 $end_time = $_POST['end_time'];

//                 // 日程を追加
//                 $stmt = $pdo->prepare('INSERT INTO availabilities (event_id, start_time, end_time) VALUES (:event_id, :start_time, :end_time)');
//                 $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
//                 $stmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
//                 $stmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
//                 $stmt->execute();
//                 echo "新しい日程が追加されました。";
//             }
//         } else {
//             // 日程追加フォーム
//             echo "<form method='POST'>
//                     <label for='start_time'>新しい日程:</label>
//                     <input type='datetime-local' name='start_time' required>
//                     <input type='datetime-local' name='end_time' required><br>
//                     <button type='submit'>日程を追加</button>
//                   </form>";
//         }
//     } else {
//         echo "パスワードが正しくありません。";
//     }
// } else {
//     echo "イベントIDとパスワードが必要です。";
// }
