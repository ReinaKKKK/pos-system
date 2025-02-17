<?php

// データベース接続
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';

// イベントIDを取得
$eventId = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if (!$eventId) {
    echo "イベントIDが無効です。";
    exit;
}

// イベント情報を取得
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $eventId]);
$event = $stmt->fetch();

// イベントが存在しない場合のエラーハンドリング
if (!$event) {
    echo "指定されたイベントが存在しません。";
    exit;
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['name'];
    $eventDetails = $_POST['details'];
    $timeSlots = isset($_POST['timeSlots']) ? json_decode($_POST['timeSlots'], true) : [];

    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // `events` テーブルのデータを更新
        $sqlEvents = 'UPDATE events SET name = :name, detail = :detail, updated_at = NOW() WHERE id = :event_id';
        $stmtEvents = $pdo->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $eventName,
            ':detail' => $eventDetails,
            ':event_id' => $eventId,
        ]);

        // `availabilities` テーブルのデータを更新
        $sqlAvailabilities = 'DELETE FROM availabilities WHERE event_id = :event_id';
        $stmtAvailabilities = $pdo->prepare($sqlAvailabilities);
        $stmtAvailabilities->execute([':event_id' => $eventId]);

        // 新しい時間スロットを挿入
        $sqlAvailabilitiesInsert = 'INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                                    VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
        $stmtAvailabilitiesInsert = $pdo->prepare($sqlAvailabilitiesInsert);

        foreach ($timeSlots as $slot) {
            $stmtAvailabilitiesInsert->execute([
                ':event_id' => $eventId,
                ':start_time' => $slot['startTime'],
                ':end_time' => $slot['endTime'],
            ]);
        }

        // トランザクションをコミット
        $pdo->commit();

        // 成功した場合、イベント一覧にリダイレクト
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        // データベース関連のエラーをキャッチしてロールバック
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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>イベント編集</h1>

    <!-- イベント編集フォーム -->
    <form method="POST">
        <label for="name">イベント名:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required><br>

        <label for="details">詳細:</label>
        <textarea id="details" name="details" required><?php echo htmlspecialchars($event['detail']); ?></textarea><br>

        <label>開始時間:</label>
        <input type='datetime-local' id='startTime' name='startTime' required><br><br>

        <label>終了時間:</label>
        <input type='datetime-local' id='endTime' name='endTime' required><br><br>

        <button type='button' id='addSlotBtn'>候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id='timeSlotContainer'>
            <?php
            // 既存の時間スロットを表示
            $stmtTimeSlots = $pdo->prepare("SELECT * FROM availabilities WHERE event_id = :event_id");
            $stmtTimeSlots->execute(['event_id' => $eventId]);
            $timeSlots = $stmtTimeSlots->fetchAll();
            foreach ($timeSlots as $slot) {
                echo "<div class='time-slot'>開始: " . htmlspecialchars($slot['start_time']) . " 終了: " . htmlspecialchars($slot['end_time']) . "</div>";
            }
            ?>
        </div>

        <label>編集パスワード:※イベント内容を変更する際に使用します。</label>
        <input type='password' name='editPassword'><br><br>

        <input type='hidden' id='timeSlotsInput' name='timeSlots'> <!-- 候補日時をここに送信 -->
        <button type='submit'>イベントを更新</button>
    </form>

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

        // 新しい時間スロットを表示
        var timeSlotContainer = document.getElementById('timeSlotContainer');
        var newSlot = document.createElement('div');
        newSlot.classList.add('time-slot');
        newSlot.textContent = '開始: ' + startTime + ' 終了: ' + endTime;

        // 新しいスロットを追加
        existingTimeSlots.push({ startTime: startTime, endTime: endTime });
        timeSlotsInput.value = JSON.stringify(existingTimeSlots);
        timeSlotContainer.appendChild(newSlot);
    });
</script>
