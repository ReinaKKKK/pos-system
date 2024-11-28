<?php

require_once('/var/www/html/arrange/database/db.php');

// POST から participant_password を取得
$participantPassword = isset($_POST['participant_password']) ? $_POST['participant_password'] : null;
var_dump($_POST);

if (empty($participantPassword)) {
    echo '<p>参加者用編集パスワードが必要です。</p>';
    exit;
    // POSTデータをチェック
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // フォームデータの取得
        $eventId = $_POST['event_id'];
        $userId = $_POST['participant_password']; // ここでパスワードをユーザーIDとして取得

        // availabilities の選択を取得
        $availabilities = $_POST['availabilities'] ?? [];

        try {
            // トランザクション開始
            $databaseConnection->beginTransaction();

            // user_id（パスワード）の確認
            if (empty($userId)) {
                echo '<p>参加者用編集パスワードが必要です。</p>';
                exit;
            }

            // 参加者IDがデータベースに存在するか、パスワードが一致するかを確認
            $stmt = $databaseConnection->prepare('
                SELECT id FROM users WHERE id = :user_id
            ');
            $stmt->execute([':user_id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo '<p>無効な参加者用編集パスワードです。</p>';
                exit;
            }

            // availabilities の選択肢に対してループ
            foreach ($availabilities as $availabilityId => $response) {
                // レスポンスデータを保存する処理
                $stmt = $databaseConnection->prepare('
                    INSERT INTO responses (user_id, availability_id, response) 
                    VALUES (:user_id, :availability_id, :response)
                ');
                $stmt->execute([
                    ':user_id' => $userId,
                    ':availability_id' => $availabilityId,
                    ':response' => $response
                ]);
            }

            // トランザクションをコミット
            $databaseConnection->commit();

            echo '<p>回答が送信されました。</p>';
        } catch (PDOException $e) {
            // エラーが発生した場合はロールバック
            $databaseConnection->rollBack();
            echo '<p>エラーが発生しました。もう一度お試しください。</p>';
            echo '<p>エラーメッセージ: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集パスワード入力</title>
</head>
<body>
    <h1>イベントの編集または回答の編集</h1>

    <!-- 主催者用と参加者用のパスワードを入力するフォーム -->
    <form method="POST" action="submit_response.php">
        <div>
            <!-- 主催者用パスワード -->
            <label for="organizer_password">主催者用編集パスワード:</label>
            <input type="text" name="organizer_password" id="organizer_password">
        </div>

        <div>
            <!-- 参加者用パスワード -->
            <label for="participant_password">参加者用編集パスワード:</label>
            <input type="text" name="participant_password" id="participant_password">
        </div>

        <button type="submit">パスワード送信</button>
    </form>

</body>
</html>

