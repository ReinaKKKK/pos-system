<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDが指定されているか確認
if (isset($_GET['event_id'])) {
    $eventId = htmlspecialchars($_GET['event_id'], ENT_QUOTES, 'UTF-8');

    // イベント名を取得
    $stmt = $databaseConnection->prepare('SELECT name FROM events WHERE id = :event_id');
    $stmt->execute([':event_id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo '<p>指定されたイベントが見つかりません。</p>';
        exit;
    }

    // 候補日（availabilities）を取得
    $stmt = $databaseConnection->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
    $stmt->execute([':event_id' => $eventId]);
    $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$availabilities) {
        echo '<p>候補日が見つかりません。</p>';
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
  <form method="POST" action="submit_response.php">
      <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
      <label for="name">名前:</label>
      <input type="text" name="name" id="name" required>
       <!-- 候補日ごとに出欠を選べるようにする -->
       <?php foreach ($availabilities as $availabilities) : ?>
          <div>
              <label for="availabilities_<?php echo $availabilities['id']; ?>">
              <?php
                  // start_time と end_time を表示
                  $startTime = new DateTime(availabilities['start_time']);
                  $endTime = new DateTime($availabilities['end_time']);
                  echo '開始: ' . $startTime->format('Y-m-d H:i') . ' - 終了: ' . $endTime->format('Y-m-d H:i');
                ?>:
              </label>
              <select name="availabilities[<?php echo $availabilities['id']; ?>]" id="availabilities_<?php echo $availabilities['id']; ?>" required>
                  <option value="1">〇</option>
                  <option value="2">×</option>
                  <option value="3">△</option>
              </select>
          </div>
       <?php endforeach; ?>

      <label for="comment">コメント:</label>
      <textarea name="comment" id="comment"></textarea>
      <button type="submit">送信</button>
  </form>
</body>
</html>
