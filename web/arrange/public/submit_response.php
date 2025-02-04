<?php

require_once('/var/www/html/arrange/database/db.php');
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php'; // イベントの保存処理


// イベントIDを取得
if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

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

        // イベントの日程を取得
        $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 参加者とその回答を取得
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

// 参加者の回答を整理
$participantsResponses = [];
foreach ($responses as $response) {
    $userName = $response['name']; // ユーザー名を取得
    $participantsResponses[$userName][$response['availability_id']] = [
        'response' => $response['response'],
        'comment' => $response['comment']
    ];
}

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
        // デバッグ用：変更が成功したかどうか
        if ($stmt->rowCount() > 0) {
            echo "データベースが更新されました";
        } else {
            echo "変更なし";
        }
        // POST送信後、リダイレクトすることで最新のデータを表示
        header("Location: submit_response.php"); // 一覧ページへリダイレクト
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
    <style>
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
        /* ポップアップのスタイル */
        .popup_event {
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
        .popup-content_event {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }

    </style>
    <script>
// 参加者用ポップアップを表示
function showPasswordPopupForParticipant(url, eventId) {
    console.log("showPasswordPopupForParticipant called with: ", url, eventId);
    document.getElementById('popup').style.display = 'flex';
    document.getElementById('editForm').action = url;
    document.getElementById('event_id_field').value = eventId; // イベントIDをフォームに設定
}

// 主催者用ポップアップを表示
function showPasswordPopupForOrganizer(url, eventId) {
    console.log("showPasswordPopupForOrganizer called with: ", url, eventId);
    document.getElementById('popup_event').style.display = 'flex';
    document.getElementById('editForm_event').action = url;
    document.getElementById('event_id_field_event').value = eventId; // イベントIDをフォームに設定
}

// 参加者用パスワード確認後にポップアップを非表示にする
function submitPasswordForm() {
    const password = document.getElementById('response_edit_password').value;
    if (password.trim() !== "") {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('editForm').submit(); // フォーム送信
    } else {
        alert("パスワードを入力してください");
    }
}

// 主催者用パスワード確認後にポップアップを非表示にする
function submitPasswordFormEvent() {
    const password = document.getElementById('event_edit_password').value;
    
    if (password.trim() !== "") {
        document.getElementById('popup_event').style.display = 'none';
        document.getElementById('editForm_event').submit(); // フォーム送信
    } else {
        alert("パスワードを入力してください");
    }
}

    
    </script>
</head>
<body>
    <h1>イベント: <?php echo htmlspecialchars($event['name']); ?></h1>

    <!-- 編集ボタン（参加者用） -->
    <button onclick="showPasswordPopupForParticipant('participant_edit.php', <?php echo $eventId; ?>)">回答を編集</button>

    <!-- 編集ボタン（主催者用） -->
    <button onclick="showPasswordPopupForOrganizer('organizer_edit.php', <?php echo $eventId; ?>)">イベントを編集</button>

<!-- 参加者用ポップアップ -->
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

<!-- 主催者用ポップアップ -->
<div id="popup_event" class="popup_event">
    <div class="popup-content_event">
        <h2>イベントを編集</h2>
        <form id="editForm_event" method="POST">
            <input type="hidden" id="event_id_field_event" name="event_id" value=""> <!-- イベントIDを保持 -->
            <label for="name">イベント名:</label>
            <input type="text" id="name" name="name" placeholder="イベント名" required>
            <label for="event_edit_password">イベント編集パスワード:</label>
            <input type="password" id="event_edit_password" name="event_edit_password" placeholder="イベント編集用パスワード" required>
            <br><br>
            <button type="button" onclick="submitPasswordFormEvent()">送信</button>
            <button type="button" onclick="document.getElementById('popup_event').style.display='none'">キャンセル</button>
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

        <!-- コメント行 -->
        <tr>
            <td>コメント</td> <!-- コメント列の見出し -->
            <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                <td>
                    <?php
                    // 各ユーザーのコメントを表示
                    if (!empty($responsesForUser)) {
                        // ユーザーの最初の候補日のコメントだけを取得
                        $firstResponse = reset($responsesForUser);
                        $comment = $firstResponse['comment'] ?? null; // 最初の候補日のコメント
                        if (!empty($comment)) {
                            echo htmlspecialchars($comment); // コメントがあればエスケープして表示
                        } else {
                            echo "なし"; // コメントがない場合は「なし」と表示
                        }
                    }
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</body>
</html>
