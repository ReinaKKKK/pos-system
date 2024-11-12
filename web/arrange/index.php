<!-- create_event.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント作成</title>
    <script>
        // 時間スロットの追加を動的に行う関数
        function addTimeSlot() {
            const timeSlotsContainer = document.getElementById("timeSlots");
            
            // 新しい日付と時間スロットの入力欄を作成
            const newSlot = document.createElement("div");
            newSlot.classList.add("time-slot");
            
            newSlot.innerHTML = `
                <label>日付:</label>
                <input type="date" name="dates[]" required>
                <label>開始時刻:</label>
                <input type="time" name="start_times[]" required>
                <label>終了時刻:</label>
                <input type="time" name="end_times[]" required><br>
            `;

            timeSlotsContainer.appendChild(newSlot);
        }
    </script>
</head>
<body>
    <h2>新しいイベントを作成</h2>
    <form action="save_event.php" method="POST">
        <label>イベント名:</label>
        <input type="text" name="name" required><br><br>

        <label>イベント詳細:</label>
        <textarea name="detail" required></textarea><br><br>

        <label>編集パスワード:</label>
        <input type="password" name="edit_password" required><br><br>

        <label>候補日時:</label>
        <div id="timeSlots">
            <!-- 最初の日時入力欄 -->
            <div class="time-slot">
                <label>日付:</label>
                <input type="date" name="dates[]" required>
                <label>開始時刻:</label>
                <input type="time" name="start_times[]" required>
                <label>終了時刻:</label>
                <input type="time" name="end_times[]" required><br>
            </div>
        </div>
        <button type="button" onclick="addTimeSlot()">時間帯を追加</button><br><br>

        <input type="submit" value="イベントを作成">
    </form>
</body>
</html>

<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $detail = $_POST['detail'];
    $edit_password = password_hash($_POST['edit_password'], PASSWORD_DEFAULT);
    $dates = $_POST['dates'];
    $start_times = $_POST['start_times'];
    $end_times = $_POST['end_times'];

    try {
        // イベント情報を保存
        $stmt = $pdo->prepare("INSERT INTO events (name, detail, edit_password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $detail, $edit_password]);
        $event_id = $pdo->lastInsertId();

        // 各候補日時をavailabilitiesテーブルに保存
        for ($i = 0; $i < count($dates); $i++) {
            $date = $dates[$i];
            $start_time = $start_times[$i];
            $end_time = $end_times[$i];
            $stmt = $pdo->prepare("INSERT INTO availabilities (event_id, date, time_slot) VALUES (?, ?, ?)");
            $stmt->execute([$event_id, $date, "$start_time-$end_time"]);
        }

        echo "イベントが作成されました。";
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}
?>
