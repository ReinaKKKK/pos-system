<?php
try {
    $db = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // エラーを例外として処理
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
    exit();
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';  // 空欄でもOK
    $edit_password = isset($_POST['edit_password']) ? $_POST['edit_password'] : '';  // 空欄でもOK
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // nameが空でないか確認
    if (empty($name)) {
        echo "イベント名は必須です。";
        exit;
    }
    
    // dateが空でないか確認
    if (empty($date)) {
        echo "候補日時を入れてください。";
        exit;
    }

    // start_timeが空でないか確認
    if (empty($start_time)) {
        echo "候補日時を入れてください。";
        exit;
    }

    // end_timeが空でないか確認
    if (empty($end_time)) {
        echo "候補日時を入れてください。";
        exit;
    }
    
        // イベントをまずeventsテーブルに挿入
        $sql_events = "INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
        VALUES (:name, :detail, :edit_password, NOW(), NOW())";
        $stmt_events = $db->prepare($sql_events);
        $stmt_events->execute([
        ':name' => $name,
        ':detail' => $detail,
        ':edit_password' => $edit_password,
        ]);
         
        // 挿入されたイベントIDを取得
        $event_id = $db->lastInsertId();  
        
        // availabilitiesテーブルに日時情報を挿入
        $sql_availabilities = "INSERT INTO availabilities (event_id, date, start_time, end_time, created_at, updated_at) 
                        VALUES (:event_id, :date, :start_time, :end_time, NOW(), NOW())";
        $stmt_availabilities = $db->prepare($sql_availabilities);  
        $stmt_availabilities->execute([
        ':event_id' => $event_id,
        ':date' => $date,
        ':start_time' => $start_time, 
        ':end_time' => $end_time, 
        ]);


    // イベント作成後にURLを表示するページにリダイレクト
    header("Location: save_event.php?event_id=" . $event_id);
    exit();  // リダイレクト後に処理を終了
    } else {
    // エラー時に詳細を表示
    $errorInfo = $stmt_availabilities->errorInfo();  
    echo "日時の挿入に失敗しました。エラー情報: " . print_r($errorInfo, true);
    }

    // 時間帯(AM/PM)と時間、分からDATETIMEを作成する関数
    function convertToDatetime($date, $time, $ampm, $minute) {
    $hour = (int)$time;
    if ($ampm == 'PM' && $hour < 12) {
    $hour += 12;
    } elseif ($ampm == 'AM' && $hour == 12) {
    $hour = 0;
    }
    return $date . ' ' . sprintf('%02d', $hour) . ':' . $minute . ':00';
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント作成</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルをリンク -->
    <script src="script.js"></script><!-- 外部CSSファイルをリンク -->
</head>
<body>
    <h2>新しいイベントを作成</h2>
    <form action="index.php" method="POST">
        <label>イベント名:</label>
        <input type="text" name="name" required><br><br>

        <label>イベント詳細:</label>
        <textarea name="detail"></textarea><br><br>

        <label>
        日付を選択してください
        <input type='date' id="inputDate" name="date" oninput='dateResult.textContent = this.value'>
        </label><br><br>
        
        <label for="start-time-of-day">時間帯:</label>
        <select id="start-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
        </select><br><br>

        <label for="start_time">時間:</label>
        <select id="start_time" name="start_time"></select>
        <br><br>

        <label for="start_time_minute">分:</label>
        <select id="start_time_minute" name="start_time_minute"></select>
        <br><br>
        <text> ～</text><br><br>

        <label for="end-time-of-day">時間帯:</label>
        <select id="end-time-of-day">
        <option value="AM">午前</option>
        <option value="PM">午後</option>
        </select><br><br>

        <label for="end_time">時間:</label>
        <select id="end_time" name="end_time"></select>
        <br><br>
        <label for="end_time_minute">分:</label>
        <select id="end_time_minute" name="end_time_minute"></select>
        <br><br>
        <button type="button" onclick="addTimeSlot()">候補を追加</button><br><br>

        <!-- 候補日を表示するコンテナ -->
        <div id="time-slot-container"></div>

        <label>編集パスワード:</label>
        <input type="password" name="edit_password"><br><br>


        <input type="submit" value="イベントを作成">
    </form>
</body>
</html>
