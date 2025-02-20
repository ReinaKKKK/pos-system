<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['name'];
    $eventDetails = $_POST['details'];
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : '';
    $timeSlots = isset($_POST['timeSlots']) ? json_decode($_POST['timeSlots'], true) : [];

    try {
        $hashedPassword = password_hash($editPassword, PASSWORD_DEFAULT); // ハッシュ化

        $pdo->beginTransaction();

        $sqlEvents = 'INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())';
        $stmtEvents = $pdo->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $eventName,
            ':detail' => $eventDetails,
            ':edit_password' => $hashedPassword,
        ]);

        // 挿入されたイベントIDを取得
        $eventId = $pdo->lastInsertId();
        if ($eventId === false) {
            throw new Exception("イベントIDの取得に失敗しました。");
        }

        // `availabilities` テーブルに複数の日時スロットを挿入
        $sqlAvailabilities = 'INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                              VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
        $stmtAvailabilities = $pdo->prepare($sqlAvailabilities);

        foreach ($timeSlots as $slot) {
            $stmtAvailabilities->execute([
                ':event_id' => $eventId,
                ':start_time' => $slot['startTime'],
                ':end_time' => $slot['endTime'],
            ]);
        }

        $pdo->commit();

        if (!headers_sent()) {
            header("Location: index.php");
            exit;
        } else {
            echo "リダイレクトに失敗しました。";
            // echo '<a href="event_url.php?event_id=' . htmlspecialchars($eventId, ENT_QUOTES) . '">ここをクリック</a>';
            exit;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "データベースエラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        error_log($e->getMessage());
    } catch (Exception $e) {
        // その他の例外をキャッチ
        echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        error_log($e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <title>イベント作成</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>
    <h1>新しいイベントを作成</h1>

    <!-- イベント作成フォーム -->
    <form method="POST">
        <label for="name">イベント名:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="details">詳細:</label>
        <textarea id="details" name="details" required></textarea><br>

        <label>開始時間:</label>
        <input type='datetime-local' id='startTime' name='startTime' required><br><br>

        <label>終了時間:</label>
        <input type='datetime-local' id='endTime' name='endTime' required><br><br>

        <button type='button' id='addSlotBtn'>候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id='timeSlotContainer'></div>

        <label>編集パスワード:※イベント内容を変更する際に使用します。</label>
        <input type='password' name='editPassword'><br><br>

        <input type='hidden' id='timeSlotsInput' name='timeSlots'> <!-- 候補日時をここに送信 -->
        <button type='submit'>イベントを作成</button>
    </form>
    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <br>
    <a href="index.php">イベント一覧に戻る</a>
</body>
</html>
<script>
    document.getElementById('addSlotBtn').addEventListener('click', function() {
        var startTime = document.getElementById('startTime').value;
        var endTime = document.getElementById('endTime').value;

        // 既存の時間スロットがあるか確認
        var timeSlotsInput = document.getElementById('timeSlotsInput');
        var existingTimeSlots = timeSlotsInput.value ? JSON.parse(timeSlotsInput.value) : [];

        // 既存の時間スロットが空でない場合のみ重複チェックを行う
    if (existingTimeSlots.length > 0) {
        var isDuplicate = existingTimeSlots.some(function(slot) {
            return slot.startTime === startTime && slot.endTime === endTime;
        });

        if (isDuplicate) {
            alert('この候補日はすでに使われています。');
            return; // 重複していた場合は追加しない
        }
    }


        // 新しい時間スロットを表示
        var timeSlotContainer = document.getElementById('timeSlotContainer');
        var newSlot = document.createElement('div');
        newSlot.classList.add('time-slot');
        newSlot.textContent = '開始: ' + startTime + ' 終了: ' + endTime;

        // 削除ボタンを作成
        var deleteButton = document.createElement('button');
        deleteButton.textContent = '削除';
        deleteButton.classList.add('delete-btn');

        // 削除ボタンの動作
        deleteButton.addEventListener('click', function() {
            newSlot.remove(); // 候補日を削除
            // 同じ候補日を timeSlots から削除
            var index = existingTimeSlots.findIndex(function(slot) {
                return slot.startTime === startTime && slot.endTime === endTime;
            });
            if (index !== -1) {
                existingTimeSlots.splice(index, 1); // 削除
                timeSlotsInput.value = JSON.stringify(existingTimeSlots); // 更新
            }
        });

        // 新しいスロットと削除ボタンを表示
        newSlot.appendChild(deleteButton);
        timeSlotContainer.appendChild(newSlot);

        // 入力した時間スロットをフォームに追加
        existingTimeSlots.push({ startTime: startTime, endTime: endTime });
        timeSlotsInput.value = JSON.stringify(existingTimeSlots);
    });
    </script>
