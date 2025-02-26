<?php

require_once('/var/www/html/arrange/database/db.php');

if (isset($_POST['event_id'], $_POST['name'], $_POST['response_edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $name = $_POST['name'];
    $participantPassword = $_POST['response_edit_password'];

    try {
        $stmt = $pdo->prepare('SELECT id, edit_password FROM users WHERE event_id = :event_id AND name = :name');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR); // name をstring型でバインド
        $stmt->execute(); // クエリ実行
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($participantPassword, $user['edit_password'])) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        }
        $userId = $user['id'];
        $stmt = $pdo->prepare('

            SELECT 
                responses.id, 
                responses.availability_id, 
                responses.user_id, 
                responses.response, 
                users.comment,  -- users.comment を参照
                availabilities.start_time, 
                availabilities.end_time
            FROM 
                responses 
            JOIN 
                availabilities 
            ON 
                responses.availability_id = availabilities.id 
            JOIN 
                users 
            ON 
                responses.user_id = users.id  -- usersテーブルをJOIN
            WHERE 
                responses.user_id = :user_id
        ');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDまたはパスワードが無効です。</p>';
    exit;
}
if (headers_sent($file, $line)) {
    echo "Headers already sent in $file on line $line";
    exit;
}

if (isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM responses WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :user_id');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: submit_response.php?event_id=' . $eventId);
        exit;
    } catch (PDOException $e) {
        echo '削除エラー: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response']) && isset($_POST['response_id'])) {
    try {
        foreach ($_POST['response'] as $responseId => $responseValue) {
            $stmt = $pdo->prepare('UPDATE responses SET response = :response WHERE id = :response_id');
            $stmt->bindValue(':response', $responseValue);
            $stmt->bindValue(':response_id', $responseId, PDO::PARAM_INT);
            $stmt->execute();

            $comment = $_POST['comment'][$responseId] ?? null;
            $stmt = $pdo->prepare('UPDATE users SET comment = :comment WHERE id = :user_id');
            $stmt->bindValue(':comment', $comment, $comment !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        }

        header('Location: submit_response.php?event_id=' . $eventId);
        exit;
    } catch (PDOException $e) {
        echo "更新エラー: " . htmlspecialchars($e->getMessage());
    }
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>参加者の回答編集</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>
    <h1>回答編集画面</h1>
    <form action="participant_edit.php" method="POST">
     <!-- 送るデータを正しく隠しフィールドで送信 -->
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input type="hidden" name="response_edit_password" value="<?php echo htmlspecialchars($_POST['response_edit_password']); ?>">

        <table border="1">
            <tr>
                <th>日程</th>
                <th>回答</th>
            </tr>
            <?php foreach ($responses as $response) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($response['start_time']) . ' - ' . htmlspecialchars($response['end_time']); ?></td>
                    <td>
                        <select name="response[<?php echo htmlspecialchars($response['id']); ?>]">
                            <option value="1" <?php echo $response['response'] == 1 ? 'selected' : ''; ?>>〇</option>
                            <option value="2" <?php echo $response['response'] == 2 ? 'selected' : ''; ?>>×</option>
                            <option value="3" <?php echo $response['response'] == 3 ? 'selected' : ''; ?>>△</option>
                        </select>
                        <!-- response_id を隠しフィールドとして追加 -->
                        <input type="hidden" name="response_id[<?php echo htmlspecialchars($response['id']); ?>]" value="<?php echo htmlspecialchars($response['id']); ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
        <td>コメント</td>
        <td>
            <textarea name="comment[<?php echo htmlspecialchars($response['id']); ?>]" rows="4" cols="50"><?php echo isset($response['comment']) ? htmlspecialchars($response['comment']) : ''; ?></textarea>
        </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="更新">
        <input type="submit" name="delete" value="削除" onclick="return confirm('本当に削除しますか？');">
    </form>
    <a href="submit_response.php?event_id=<?php echo htmlspecialchars($eventId, ENT_QUOTES); ?>" class="button-link">
    回答一覧に戻る
    </a>


</body>
</html>
