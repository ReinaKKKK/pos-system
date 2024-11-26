<?php

require_once('/var/www/html/arrange/database/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // イベントIDと名前を取得
    $eventId = $_POST['event_id'];
    $name = $_POST['name'];
    $comment = $_POST['comment'];

        // availabilities の各選択を取得
    if (isset($_POST['availabilities'])) {
            // 各候補日の選択（〇×△）
            $availabilities = $_POST['availabilities'];

        try {
              // トランザクション開始
              $databaseConnection->beginTransaction();

              // 名前とコメントをイベント参加者のテーブルに挿入
              $stmt = $databaseConnection->prepare('
                  INSERT INTO availabilities (event_id, name, comment, responses, availabilities_id) 
                  VALUES (:event_id, :name, :comment, :responses, :availabilities_id)
              ');
              $stmt->execute([
                  ':event_id' => $eventId,
                  ':name' => $name,
                  ':comment' => $comment,
                  ':responses' => $responses,
                  ':availabilities_id' => $availabilitiesId
              ]);
              $responseId = $databaseConnection->lastInsertId(); // 挿入されたレスポンスのID

              // 各候補日について出欠（〇×△）を挿入
            foreach ($availabilities as $availabilitiesId => $responses) {
                  $stmt = $databaseConnection->prepare('
                      INSERT INTO availabilities (event_id, start_time, end_time, updated_at, created_at) 
                      VALUES (:response_id, :availabilities_id, :responses, NOW(), NOW())
                  ');
                  $stmt->execute([
                      ':response_id' => $responseId,
                      ':availabilities_id' => $availabilitiesId,
                      ':responses' => $responses
                  ]);
            }

              // トランザクションをコミット
              $databaseConnection->commit();

              // 成功メッセージ
              echo '<p>回答が送信されました。</p>';
        } catch (PDOException $e) {
              // エラーが発生した場合はロールバック
              $databaseConnection->rollBack();
              echo '<p>エラーが発生しました。もう一度お試しください。</p>';
              echo '<p>エラーメッセージ: ' . $e->getMessage() . '</p>';
        }
    }
}
