<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// イベントIDをGETから取得
$eventId = isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id'], ENT_QUOTES, 'UTF-8') : null;

if (!$eventId) {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}

// データベースからイベント情報を取得
try {
    $stmt = $pdo->prepare('SELECT name FROM events WHERE id = :event_id');
    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo '<p>指定されたイベントが見つかりません。</p>';
        exit;
    }

    // 候補日を取得
    $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id ORDER BY start_time ASC');
    $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
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

// POSTリクエストで回答を処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['name']) || empty($_POST['user_id'])) {
        echo '<p>名前または編集用パスワードが入力されていません。</p>';
        exit;
    }

    $userName = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $participantPassword = htmlspecialchars($_POST['user_id'], ENT_QUOTES, 'UTF-8');

    try {
        // ユーザーを作成
        $stmt = $pdo->prepare('INSERT INTO users (name, event_id) VALUES (:name, :event_id)');
        $stmt->execute([
            ':name' => $userName,
            ':event_id' => $eventId,
        ]);
        $userId = $pdo->lastInsertId();  // 新しく作成したユーザーのIDを取得

        // 各回答を保存し、レスポンスIDを取得
        foreach ($availabilities as $availability) {
            $response = $_POST['availabilities'][$availability['id']] ?? null;
            if ($response !== null) {
                // レスポンスをresponsesテーブルに挿入
                $stmt = $pdo->prepare('INSERT INTO responses (user_id, availability_id, response, created_at, updated_at)
                                       VALUES (:user_id, :availability_id, :response, NOW(), NOW())');
                $stmt->execute([
                    ':user_id' => $userId,  // usersテーブルのID
                    ':availability_id' => $availability['id'],
                    ':response' => $response,
                ]);
                $responseId = $pdo->lastInsertId();  // 新しく作成したレスポンスのIDを取得

                // usersテーブルにresponse_idを更新
                $stmt = $pdo->prepare('UPDATE users SET response_id = :response_id WHERE id = :user_id');
                $stmt->execute([
                    ':response_id' => $responseId,  // レスポンスID
                    ':user_id' => $userId,  // usersテーブルのID
                ]);
            }
        }

        // 回答後のリダイレクト
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
    <form method="POST" action="">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="name">名前:</label>
        <input type="text" name="name" id="name" required>
        <?php foreach ($availabilities as $availability) : ?>
            <div>
                <label for="availabilities_<?php echo $availability['id']; ?>">
                    <?php
                        $startTime = new DateTime($availability['start_time'], new DateTimeZone('Asia/Tokyo'));
                        $endTime = new DateTime($availability['end_time'], new DateTimeZone('Asia/Ho_Chi_Minh'));
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
        <label for="user_id">参加者用編集パスワード:</label>
        <input type="text" name="user_id" id="user_id" required>
        <button type="submit">送信</button>
    </form>
</body>
</html>
