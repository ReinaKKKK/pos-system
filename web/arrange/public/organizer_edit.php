<?php
require_once('/var/www/html/arrange/database/db.php');
var_dump($_POST);
// イベントIDと編集パスワードが送信されているか確認
if (isset($_POST['event_id'], $_POST['event_edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $eventEditPassword = $_POST['event_edit_password'];

    try {
        // イベント情報と関連する日程をデータベースから取得
        $stmt = $pdo->prepare('
            SELECT e.id, e.name, e.detail, e.edit_password, a.start_time, a.end_time
            FROM events e
            LEFT JOIN availabilities a ON e.id = a.event_id
            WHERE e.id = :event_id
        ');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // イベントが見つからない、またはパスワードが一致しない場合
        if (!$event || $eventEditPassword !== $event[0]['edit_password']) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        }
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDまたはパスワードが無効です。</p>';
    exit;
}

// POSTデータ処理：イベント内容の更新を送信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    try {
        foreach ($_POST['event'] as $eventId => $eventValue) {
            // 'EVENTS' テーブルの 'name detail を更新
            $stmt = $pdo->prepare('UPDATE events SET name = :name WHERE id = :event_id');
            $stmt->bindValue(':name', $eventValue);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();

            // 'availabilities' テーブルの 'start time end time' を更新
            $detail = $_POST['detail'][$responseId] ?? null;
            $stmt = $pdo->prepare('UPDATE events SET detail = :detail WHERE id = :event_id');
            $stmt->bindValue(':detail', $detail, $detail !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT); // $eventIdを使用
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント編集/削除</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px;
        }
        .event-details, .date-proposals, .buttons {
            margin-bottom: 20px;
        }
        .event-details label, .date-proposals label {
            font-weight: bold;
        }
        .date-proposals input {
            margin: 5px;
        }
        .buttons button {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>イベント編集/削除</h1>

    <!-- イベントタイトルと詳細を動的に表示 -->
    <form action="submit_response.php" method="GET">
        <p><strong>Event Title:</strong> <input type="text" name="name" value="<?php echo htmlspecialchars($event[0]['name'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
        <p><strong>Event Memo:</strong> <textarea name="detail"><?php echo htmlspecialchars($event[0]['detail'], ENT_QUOTES, 'UTF-8'); ?></textarea></p>

        <!-- 日程提案 -->
        <div class="date-proposals">
            <label>日程の提案:</label><br>
            <?php foreach ($event as $availability) : ?>
                <div class="date-item">
                    <span><?php echo htmlspecialchars($availability['start_time'], ENT_QUOTES, 'UTF-8'); ?> 〜 <?php echo htmlspecialchars($availability['end_time'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <button class="delete-date">削除</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 新しい日程の追加 -->
        <div class="add-date">
            <label>新しい日程を入力:</label><br>
            <label>開始時間:</label>
            <input type='datetime-local' id='startTime' name='startTime'><br><br>

            <label>終了時間:</label>
            <input type='datetime-local' id='endTime' name='endTime'><br><br>

            <button id="add-date" type="button">日程追加</button>
        </div>

        <!-- イベントアクション -->
        <div class="buttons">
            <input type="hidden" name="event_id" value="<?php echo $event[0]['id']; ?>">
            <input type="hidden" name="event_edit_password" value="<?php echo $event[0]['edit_password']; ?>">
            <button type="submit">変更して更新</button>
        </div>
    </form>

    <!-- 一覧ページに戻るボタン -->
    <a href="submit_response.php"><button type="button">一覧ページに戻る</button></a>
</div>

<script>
    // 日程削除機能
    document.querySelectorAll('.delete-date').forEach(function(button) {
        button.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });

    // 日程追加機能
    document.getElementById('add-date').addEventListener('click', function() {
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const startTimeValue = startTimeInput.value.trim();
        const endTimeValue = endTimeInput.value.trim();

        if (startTimeValue && endTimeValue) {
            const startFormatted = formatDate(startTimeValue);
            const endFormatted = formatDate(endTimeValue);
            const dateProposals = document.querySelector('.date-proposals');
            const newDateItem = document.createElement('div');
            newDateItem.classList.add('date-item');
            newDateItem.innerHTML = `<span>${startFormatted} 〜 ${endFormatted}</span><button class="delete-date">削除</button>`;
            dateProposals.appendChild(newDateItem);
            newDateItem.querySelector('.delete-date').addEventListener('click', function() {
                this.parentElement.remove();
            });
            startTimeInput.value = '';
            endTimeInput.value = '';
        } else {
            alert('開始時間と終了時間を入力してください');
        }
    });

    // 日付を「yyyy-MM-dd HH:mm:ss」の形式に変換する関数　整理整頓
    function formatDate(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
</script>

</body>
</html>
