<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
//サーバーから取得
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();
//POST送信されたものを受け取り処理
if (isset($_POST['event_edit_password']) && isset($_POST['event_id'])) { //イベントのパスワードとIDがPOST送信されてるのを確認したら。
    $inputPassword = $_POST['event_edit_password']; //イベントのパスワードはこの配列に入れる
    $eventId = $_POST['event_id']; //イベントのIDはこの配列に入れる

    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id"); //データベースに入ってるidとevent_idという入力ボックスに入ったパスワードが同じのイベントテーブルの全て
    $stmt->execute(['event_id' => $eventId]);//イベントのIDが入った配列を実行
    $event = $stmt->fetch();//特定のものを取り出す

    if ($event && password_verify($inputPassword, $event['edit_password'])) {//特定のイベントIDの全てと編集パスと入力されたパスを照合して
        header('Location: organizer_edit.php?event_id=' . $eventId);//もしその照合が有ってればこれを実行
        exit;
    } else { //もしその照合後違うとわかったら同じIDには入力されたパスはデータベースに入ってるパスとは違うと出す
        $errorMessage = "編集用パスワードが違います";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント一覧</title>
    <link rel="stylesheet" href="style.css">


    <script>
        function showPasswordPopupForOrganizer(eventId) {//主催者パスワード記入のポップアップ関数
            //関数の内容は
            document.getElementById('popup_event').style.display = 'flex';
            document.getElementById('event_id_field_event').value = eventId;
        }
        function submitPasswordFormEvent() {
            const password = document.getElementById('event_edit_password').value;
            const eventId = document.getElementById('event_id_field_event').value;
            if (password.trim() !== "") {
                document.getElementById('editForm_event').submit();
            } else {
                alert("パスワードを入力してください");
            }
        }
    </script>


</head>
<body>
    <h1>イベント一覧</h1>
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
                        $eventUrl = "respond.php?event_id=" . urlencode($event['id']);
                        ?>
                        <a href="<?php echo $eventUrl; ?>" target="_blank">参加ページ</a>
                    </td>
                    <td>
                        <button onclick="showPasswordPopupForOrganizer(<?php echo $event['id']; ?>)">編集</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="create_event.php">新しいイベントを作成</a>

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
    if (isset($errorMessage)) {
        echo "<p style='color:red;'>" . htmlspecialchars($errorMessage) . "</p>";
    }
    ?>
</body>
</html>
