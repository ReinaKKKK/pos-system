<?php

// データベース接続
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';

// イベント一覧を取得
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();

// パスワード検証の処理
if (isset($_POST['event_edit_password']) && isset($_POST['event_id'])) {
    $inputPassword = $_POST['event_edit_password'];
    $eventId = $_POST['event_id'];

    // イベント情報をデータベースから取得
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id");
    $stmt->execute(['event_id' => $eventId]);
    $event = $stmt->fetch();

    // イベントが見つかり、パスワードが一致する場合
    if ($event && password_verify($inputPassword, $event['edit_password'])) {
        // パスワードが正しい場合、イベント編集ページにリダイレクト
        header('Location: organizer_edit.php?event_id=' . $eventId);
        exit;
    } else {
        // パスワードが違う場合
        $errorMessage = "編集用パスワードが違います";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント一覧</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <script>
        // 主催者用ポップアップを表示
        function showPasswordPopupForOrganizer(eventId) {
            // ポップアップを表示
            document.getElementById('popup_event').style.display = 'flex';
            // イベントIDをフォームに設定
            document.getElementById('event_id_field_event').value = eventId;
        }

        // 主催者用パスワード確認後にポップアップを非表示にする
        function submitPasswordFormEvent() {
            const password = document.getElementById('event_edit_password').value;
            const eventId = document.getElementById('event_id_field_event').value;
            if (password.trim() !== "") {
                // パスワードが正しい場合、index.phpでパスワード検証を行う
                document.getElementById('editForm_event').submit();
            } else {
                alert("パスワードを入力してください");
            }
        }
    </script>
</head>
<body>
    <h1>イベント一覧</h1>

    <!-- イベント一覧をテーブル形式で表示 -->
    <table>
        <thead>
            <tr>
                <th>イベント名</th>
                <th>作成日</th>
                <th>変更日</th>
                <th>参加URL</th>
                <th>イベント編集</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event) : ?>
                <tr>
                    <td>
                        <?php
                        // イベントの参加URLを作成
                        $eventUrl = "submit_response.php?event_id=" . urlencode($event['id']);
                        ?>
                        <a href="<?php echo $eventUrl; ?>" target="_blank">
                            <?php echo htmlspecialchars($event['name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($event['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($event['updated_at']); ?></td>
                    <td>
                        <?php
                        // イベントの参加URLを作成
                        $eventUrl = "respond.php?event_id=" . urlencode($event['id']);
                        ?>
                        <a href="<?php echo $eventUrl; ?>" target="_blank">参加ページ</a>
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="showPasswordPopupForOrganizer(<?php echo $event['id']; ?>)">
                           編集
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="create_event.php">新しいイベントを作成</a>

    <!-- 主催者用ポップアップ -->
    <div id="popup_event" style="display:none;">
        <div class="popup_content">
            <h2>主催者パスワードを入力してください</h2>
            <form id="editForm_event" method="POST">
                <input type="hidden" id="event_id_field_event" name="event_id">
                <label for="event_edit_password">パスワード:</label>
                <input type="password" id="event_edit_password" name="event_edit_password" required>
                <button type="button" onclick="submitPasswordFormEvent()">確認</button>
            </form>
            <button onclick="document.getElementById('popup_event').style.display='none'">キャンセル</button>
        </div>
    </div>

    <?php
    // パスワード検証エラーメッセージ
    if (isset($errorMessage)) {
        echo "<p style='color:red;'>" . htmlspecialchars($errorMessage) . "</p>";
    }
    ?>
</body>
</html>
