<?php

require_once('/var/www/html/arrange/database/db.php');

// デバッグモード有効化
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['event_id'])) {
    $eventId = htmlspecialchars($_GET['event_id'], ENT_QUOTES, 'UTF-8');
    echo "取得したイベントID: $eventId<br>";

    try {
        // イベント名を取得
        $stmt = $databaseConnection->prepare('SELECT name FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', (int)$eventId, PDO::PARAM_INT); // 型を明示的に指定
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        echo '実行クエリ: SELECT name FROM events WHERE id = :event_id<br>';
        echo 'バインド値: event_id = ' . $eventId . '<br>';

        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }

        // 候補日を取得
        $stmt = $databaseConnection->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', (int)$eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$availabilities) {
            echo '<p>候補日が見つかりません。</p>';
            exit;
        }

        // トランザクションを開始
        $databaseConnection->beginTransaction();

        // フォームから名前とパスワードを取得
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        // $participantPassword = isset($_POST['participant_password']) ? $_POST['participant_password'] : null;
        // $userId = null;

        // 名前が入力されている場合は、usersテーブルに新しい参加者を追加
        // if ($name) {
        //     $sql = 'INSERT INTO users (event_id, name, response_id, created_at, updated_at)
        //             VALUES (:event_id, :name, 1, NOW(), NOW())';  // response_id は仮の値として1を設定

        //     $stmt = $databaseConnection->prepare($sql);
        //     $stmt->execute([
        //         ':event_id' => (int)$eventId,  // イベントID
        //         ':name' => $name,              // 参加者の名前
        //     ]);

        //     // 新しく追加したユーザーのIDを取得
        //     // $userId = $databaseConnection->lastInsertId();
        //     echo "ユーザー $name が参加者リストに追加されました。<br>";
        // }

        // ユーザーの回答をresponsesテーブルに挿入
        foreach ($availabilities as $availability) {
            $response = isset($_POST['availabilities'][$availability['id']]) ? $_POST['availabilities'][$availability['id']] : null;
            $comment = isset($_POST['comment'][$availability['id']]) ? $_POST['comment'][$availability['id']] : null;

            // // ユーザーが存在しない場合はスキップ
            // if (!$userId) {
            //     echo '<p>ユーザーIDが取得できませんでした。</p>';
            //     exit;
            // }

            // responsesテーブルへの挿入
            $sql = 'INSERT INTO responses (user_id, availability_id, response, comment, created_at, updated_at)
                    VALUES (:user_id, :availability_id, :response, :comment, NOW(), NOW())';
            $stmt = $databaseConnection->prepare($sql);
            $stmt->execute([
                ':user_id' => (int)$userId,                // ユーザーID
                ':availability_id' => (int)$availability['id'], // 候補日ID
                ':response' => (int)$response,             // ユーザーの選択
                ':comment' => $comment,                    // コメント
            ]);
        }

        // トランザクションをコミット
        $databaseConnection->commit();

        echo '<p>回答が正常に保存されました。</p>';
    } catch (PDOException $e) {
        // エラーが発生した場合はロールバック
        $databaseConnection->rollBack();
        echo 'SQLエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>イベント回答</title>
</head>
<body>
  <h1><?php echo htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
  <form method="POST" action="respond.php">
      <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId, ENT_QUOTES, 'UTF-8'); ?>">
      <label for="name">名前:</label>
      <input type="text" name="name" id="name" required>
      
      <!-- 候補日ごとに出欠を選べるようにする -->
      <?php foreach ($availabilities as $availability) : ?>
          <div>
              <label for="availabilities_<?php echo htmlspecialchars($availability['id'], ENT_QUOTES, 'UTF-8'); ?>">
              <?php
                  $startTime = new DateTime($availability['start_time']);
                  $endTime = new DateTime($availability['end_time']);
                  echo '開始: ' . $startTime->format('Y-m-d H:i') . ' - 終了: ' . $endTime->format('Y-m-d H:i');
                ?>:
              </label>
              <select name="availabilities[<?php echo htmlspecialchars($availability['id'], ENT_QUOTES, 'UTF-8'); ?>]" id="availabilities_<?php echo htmlspecialchars($availability['id'], ENT_QUOTES, 'UTF-8'); ?>" required>
                  <option value="1">〇</option>
                  <option value="2">×</option>
                  <option value="3">△</option>
              </select>
          </div>
      <?php endforeach; ?>

      <label for="comment">コメント:</label>
      <textarea name="comment" id="comment"></textarea>

      <label for="participant_password">参加者用編集パスワード:</label>
      <input type="text" name="participant_password" id="participant_password" required>

      <button type="submit">送信</button>
  </form>
</body>
</html>
