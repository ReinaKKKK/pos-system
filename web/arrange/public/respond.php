<?php

require_once('/var/www/html/arrange/database/db.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$eventId = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $eventId = htmlspecialchars($_POST['event_id'], ENT_QUOTES, 'UTF-8');
} elseif (isset($_GET['event_id'])) {
    $eventId = htmlspecialchars($_GET['event_id'], ENT_QUOTES, 'UTF-8');
}

if (!$eventId) {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}

try {
    // イベント名を取得
    $stmt = $databaseConnection->prepare('SELECT name FROM events WHERE id = :event_id');
    $stmt->bindValue(':event_id', (int)$eventId, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

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
} catch (PDOException $e) {
    echo 'データベースエラー: ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['name']) || empty($_POST['participant_password'])) {
        echo '名前または編集用パスワードが入力されていません。';
        exit;
    }

    $userName = $_POST['name'];
    $participantPassword = $_POST['participant_password'];

    try {
        // 新しいユーザーを作成（event_id を追加）
        $stmt = $databaseConnection->prepare('INSERT INTO users (name, event_id) VALUES (:name, :event_id)');
        $stmt->execute([
            ':name' => $userName,
            ':event_id' => (int)$eventId // イベントIDを挿入
        ]);
        $userId = $databaseConnection->lastInsertId();

        // user_id を participant_password に設定
        $stmt = $databaseConnection->prepare('UPDATE users SET participant_password = :user_id WHERE id = :user_id');
        $stmt->execute([ ':user_id' => (int)$userId ]);

        // 各回答を保存
        foreach ($availabilities as $availability) {
            $response = $_POST['availabilities'][$availability['id']] ?? null;
            $stmt = $databaseConnection->prepare('INSERT INTO responses (user_id, availability_id, response, created_at, updated_at)
                                                  VALUES (:user_id, :availability_id, :response, NOW(), NOW())');
            $stmt->execute([
                ':user_id' => (int)$userId,
                ':availability_id' => (int)$availability['id'],
                ':response' => (int)$response,
            ]);

            // 各回答の response_id を取得してユーザーに設定
            $responseId = $databaseConnection->lastInsertId();
            $stmt = $databaseConnection->prepare('UPDATE users SET response_id = :response_id WHERE id = :user_id');
            $stmt->execute([ ':response_id' => (int)$responseId, ':user_id' => (int)$userId ]);
        }

        header('Location: submit_response.php?event_id=' . $eventId);
        exit;
    } catch (PDOException $e) {
        echo 'エラー: ' . $e->getMessage();
        exit;
    }
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
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId, ENT_QUOTES, 'UTF-8'); ?>">
      <label for="name">名前:</label>
      <input type="text" name="name" id="name" required>
      <?php foreach ($availabilities as $availability) : ?>
          <div>
              <label for="availabilities_<?php echo $availability['id']; ?>">
                  <?php
                      $startTime = new DateTime($availability['start_time']);
                      $endTime = new DateTime($availability['end_time']);
                      echo '開始: ' . $startTime->format('Y-m-d H:i') . ' - 終了: ' . $endTime->format('Y-m-d H:i');
                    ?>:
              </label>
              <select name="availabilities[<?php echo $availability['id']; ?>]" id="availabilities_<?php echo $availability['id']; ?>" required>
                  <option value="1">〇</option>
                  <option value="2">×</option>
                  <option value="3">△</option>
              </select>
          </div>
      <?php endforeach; ?>
      <label for="participant_password">参加者用編集パスワード:</label>
      <input type="text" name="participant_password" id="participant_password" required>
      <button type="submit">送信</button>
  </form>
</body>
</html>
