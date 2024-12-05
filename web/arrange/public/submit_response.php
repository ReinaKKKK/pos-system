<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

    // 参加者編集用パスワード、主催者編集用パスワード
    $participantPassword = $_POST['edit_password'] ?? ''; // 参加者のパスワード
    $hostPassword = $_POST['edit_password'] ?? ''; // 主催者のパスワード

    try {
        // イベント名を取得
        $stmt = $pdo->prepare('SELECT name, edit_password FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }

        // 参加者認証（edit_passwordが正しい場合）
        $isParticipantAuthenticated = false;
        if ($participantPassword) {
            $stmt = $pdo->prepare('SELECT id FROM responses WHERE event_id = :event_id AND edit_password = :edit_password');
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindValue(':edit_password', $participantPassword, PDO::PARAM_STR);
            $stmt->execute();
            $isParticipantAuthenticated = $stmt->fetchColumn() !== false;
        }

        // 主催者認証（edit_passwordが正しい場合）
        $isHostAuthenticated = false;
        if ($hostPassword && $hostPassword === $event['edit_password']) {
            $isHostAuthenticated = true;
        }

        // イベントの日程を取得
        $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 参加者とその回答を取得
        $stmt = $pdo->prepare('SELECT users.id AS user_id, users.name AS user_name, responses.availability_id, responses.response, responses.comment FROM responses JOIN users ON responses.user_id = users.id WHERE responses.availability_id IN (
            SELECT id FROM availabilities WHERE event_id = :event_id)');
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

// 参加者の回答を整理
$participantsResponses = [];
foreach ($responses as $response) {
    $participantsResponses[$response['user_name']][$response['availability_id']] = [
        'response' => $response['response'],
        'comment' => $response['comment']
    ];
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント管理</title>
    <style>
        /* ポップアップのスタイル */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
    <script>
    // ポップアップを表示
    function showPasswordPopup(url, eventId) {
        console.log("showPasswordPopup called with: ", url, eventId); // デバッグ: URLとeventIdを確認
        document.getElementById('popup').style.display = 'flex';
        document.getElementById('editForm').action = url;
        document.getElementById('event_id_field').value = eventId; // イベントIDをフォームに設定
        console.log("event_id_field value set to: ", eventId); // デバッグ: フィールドに設定された値を確認
    }

    // パスワード確認後にポップアップを非表示にする
    function submitPasswordForm() {
        const password = document.getElementById('edit_password').value;
        if (password.trim() !== "") {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('editForm').submit(); // フォーム送信
        } else {
            alert("パスワードを入力してください");
        }
    }
    </script>
</head>
<body>
    <h1>イベント: <?php echo htmlspecialchars($event['name']); ?></h1>

    <!-- 編集ボタン（参加者用） -->
    <button onclick="showPasswordPopup('participant_edit.php', <?php echo $eventId; ?>)">回答を編集</button>

    <!-- 編集ボタン（主催者用） -->
    <button onclick="showPasswordPopup('organizer_edit.php', <?php echo $eventId; ?>)">イベントを編集</button>

    <!-- ポップアップ -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <h2>編集用パスワードを入力してください</h2>
            <form id="editForm" method="POST">
                <input type="hidden" id="event_id_field" name="event_id" value=""> <!-- イベントIDを保持 -->
                <input type="name" id="name" name="name" placeholder="ユーザー名" required>
                <input type="edit_password" id="edit_password" name="edit_password" placeholder="パスワード" required>
                <br><br>
                <button type="button" onclick="submitPasswordForm()">送信</button>
                <button type="button" onclick="document.getElementById('popup').style.display='none'">キャンセル</button>
            </form>
        </div>
    </div>

    <!-- 回答一覧 -->
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
                                $comment = $responsesForUser[$availability['id']]['comment'];
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
                                // コメントがあれば表示
                                if ($comment) {
                                    echo "<br>" . htmlspecialchars($comment);
                                }
                            } else {
                                echo "未回答";
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
