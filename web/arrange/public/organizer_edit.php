<?php

require_once('/var/www/html/arrange/database/db.php');

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
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT); // プレースホルダーに値をバインド
        $stmt->execute(); // クエリ実行
        $event = $stmt->fetchAll(PDO::FETCH_ASSOC); // 結果を取得（複数行の場合があるためfetchAllを使用）

        // イベントが見つからない、またはパスワードが一致しない場合
        if (!$event || $eventEditPassword !== $event[0]['edit_password']) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        }
    } catch (PDOException $e) {
        // データベースエラーの処理
        echo 'データベースエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDまたはパスワードが無効です。</p>';
    exit;
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
    <p><strong>Event Title:</strong> <?php echo htmlspecialchars($event[0]['name'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Event Memo:</strong> <?php echo htmlspecialchars($event[0]['detail'], ENT_QUOTES, 'UTF-8'); ?></p>

    <!-- 日程提案 -->
    <div class="date-proposals">
        <label>日程の提案:</label><br>
        <?php foreach ($event as $availability) : ?>
            <div class="date-item">
                <input type="text" value="<?php echo htmlspecialchars($availability['start_time'], ENT_QUOTES, 'UTF-8'); ?> 〜 <?php echo htmlspecialchars($availability['end_time'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                <button class="delete-date">削除</button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 新しい日程の追加 -->
    <div class="add-date">
        <label>新しい日程を入力:</label><br>
        <input type="text" id="new-date" placeholder="例: Aug 7(Mon) 19:00〜"><br>
        <button id="add-date">日程追加</button>
    </div>

    <!-- イベントアクション -->
    <div class="buttons">
        <!-- イベント更新ボタン（submit_response.phpに遷移） -->
        <form action="submit_response.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $event[0]['id']; ?>">
            <input type="hidden" name="event_edit_password" value="<?php echo $event[0]['edit_password']; ?>">
            <button type="submit">変更して更新</button>
        </form>
        <!-- 一覧ページに戻るボタン -->
        <a href="submit_response.php"><button type="button">一覧ページに戻る</button></a>
    </div>
</div>

<script>
    // 日程削除機能
    document.querySelectorAll('.delete-date').forEach(function(button) {
        button.addEventListener('click', function() {
            // 削除ボタンがクリックされた日程を親要素ごと削除
            this.parentElement.remove();
        });
    });

    // 日程追加機能
    document.getElementById('add-date').addEventListener('click', function() {
        const newDateInput = document.getElementById('new-date');
        const newDateValue = newDateInput.value.trim();

        if (newDateValue) {
            const dateProposals = document.querySelector('.date-proposals');
            const newDateItem = document.createElement('div');
            newDateItem.classList.add('date-item');

            // 新しい日程を追加
            newDateItem.innerHTML = `
                <input type="text" value="${newDateValue}" readonly>
                <button class="delete-date">削除</button>
            `;
            dateProposals.appendChild(newDateItem);

            // 新しい日程の削除ボタンにイベントリスナーを追加
            newDateItem.querySelector('.delete-date').addEventListener('click', function() {
                this.parentElement.remove();
            });

            // 入力フィールドをクリア
            newDateInput.value = '';
        } else {
            alert('日程を入力してください');
        }
    });
</script>

</body>
</html>
