<?php

require_once('/var/www/html/arrange/database/db.php');

if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

    try {
        $stmt = $pdo->prepare('SELECT name, edit_password FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }
        $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('
        SELECT 
            users.id AS user_id, 
            users.name AS name, 
            responses.availability_id, 
            responses.response, 
            users.comment 
        FROM responses
        JOIN users ON responses.user_id = users.id 
        WHERE responses.availability_id IN (
            SELECT id FROM availabilities WHERE event_id = :event_id
        )
        ');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'SQLエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}
$participantsResponses = [];
foreach ($responses as $response) {
    $userName = $response['name']; // ユーザー名を取得
    $participantsResponses[$userName][$response['availability_id']] = [
        'response' => $response['response'],
        'comment' => $response['comment']
    ];
}
//POST送信したものを受け取れたら
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $user_comment = $_POST['comment'];
    $response = $_POST['response'];

    try {
        // コメントと回答をデータベースに保存
        $stmt = $pdo->prepare('UPDATE responses SET response = :response, comment = :comment WHERE user_id = :user_id');
        $stmt->bindValue(':response', $response);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':comment', $user_comment);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "データベースが更新されました";
        } else {
            echo "変更なし";
        }
        header("Location: submit_response.php");
    } catch (PDOException $e) {
        echo "更新エラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント管理</title>
    <link rel='stylesheet' href='style.css'>
  
    <script>

function showPasswordPopupForParticipant(url, eventId) {
    console.log("showPasswordPopupForParticipant called with: ", url, eventId);
    document.getElementById('popup').style.display = 'flex';
    document.getElementById('editForm').action = url;
    document.getElementById('event_id_field').value = eventId;
}

function submitPasswordForm() {
    const password = document.getElementById('response_edit_password').value;
    if (password.trim() !== "") {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('editForm').submit();
    } else {
        alert("パスワードを入力してください");
    }
}
    
    </script>
</head>
<body>
    <h1>イベント: <?php echo htmlspecialchars($event['name']); ?></h1>

    <button onclick="showPasswordPopupForParticipant('participant_edit.php', <?php echo $eventId; ?>)">回答を編集</button>

<div id="popup" class="popup">
    <div class="popup-content">
        <h2>回答を編集</h2>
        <form id="editForm" method="POST">
            <input type="hidden" id="event_id_field" name="event_id" value=""> <!-- イベントIDを保持 -->
            <label for="name">ユーザー名:</label>
            <input type="text" id="name" name="name" placeholder="ユーザー名" required>
            <label for="response_edit_password">編集パスワード:</label>
            <input type="password" id="response_edit_password" name="response_edit_password" placeholder="回答編集用パスワード" required>
            <br><br>
            <button type="button" onclick="submitPasswordForm()">送信</button>
            <button type="button" onclick="document.getElementById('popup').style.display='none'">キャンセル</button>
        </form>
    </div>
</div>

    <table border="1">
        <thead>
            <tr>
                <th>日程</th>
                <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                    <th><?php echo htmlspecialchars($userName); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($availabilities as $availability) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($availability['start_time']) . ' - ' . htmlspecialchars($availability['end_time']); ?></td>
                    <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                        <td>
                            <?php
                            if (isset($responsesForUser[$availability['id']])) {
                                $response = $responsesForUser[$availability['id']]['response'];
                                if (isset($responsesForUser[$availability['id']])) {
                                    $comment = $responsesForUser[$availability['id']]['comment'];
                                }
                                switch ($response) {
                                    case 1:
                                        echo "〇";
                                        break;
                                    case 2:
                                        echo "×";
                                        break;
                                    case 3:
                                        echo "△";
                                        break;
                                    default:
                                        echo "未回答";
                                }
                            } else {
                                echo "未回答";
                            }
                            ?>
                    </td>
                    <?php endforeach; ?>
            </tr>
            
            <?php endforeach; ?>

        <tr>
            <td>コメント</td>
            <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                <td>
                    <?php
                    if (!empty($responsesForUser)) {
                        $firstResponse = reset($responsesForUser);
                        $comment = $firstResponse['comment'] ?? null;
                        if (!empty($comment)) {
                            echo htmlspecialchars($comment);
                        } else {
                            echo "なし";
                        }
                    }
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
    <a href="index.php">戻る</a>

</body>
</html>
