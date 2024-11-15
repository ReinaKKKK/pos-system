<?php
try {
    $db = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $edit_password = isset($_POST['edit_password']) ? $_POST['edit_password'] : '';
    $date = $_POST['date'];
    $start_time_hour = $_POST['start_time'];
    $start_time_minute = $_POST['start_time_minute'];
    $start_time_of_day = $_POST['start_time_of_day'];
    $end_time_hour = $_POST['end_time'];
    $end_time_minute = $_POST['end_time_minute'];
    $end_time_of_day = $_POST['end_time_of_day'];


 // 必須項目欄が空だった場合、選択しなかった場合
    if (empty($name)) {
        echo "イベント名は必須です。";
        exit;
    }

    if (empty($date) || empty($start_time_hour) || empty($end_time_hour)) {
        echo "候補日時を入れてください。";
        exit;
    }
    // 午前・午後を24時間形式に変換
    $start_hour_24 = ($start_time_of_day == 'PM' && $start_time_hour != 12) ? $start_time_hour + 12 : ($start_time_hour == 12 && $start_time_of_day == 'AM' ? 0 : $start_time_hour);
    $end_hour_24 = ($end_time_of_day == 'PM' && $end_time_hour != 12) ? $end_time_hour + 12 : ($end_time_hour == 12 && $end_time_of_day == 'AM' ? 0 : $end_time_hour);

    // 終了時刻の時間が24時間制に収まっているかを確認
    if ($end_hour_24 >= 24 || $end_hour_24 < 0) {
        echo "終了時刻が無効です。24時間以内で入力してください。";
        exit;
    }
 
    // 24時間形式の時間と日付を結合してDATETIMEを作成
    $start_time = date("Y-m-d H:i:s", strtotime("$date $start_hour_24:$start_time_minute:00"));
    $end_time = date("Y-m-d H:i:s", strtotime("$date $end_hour_24:$end_time_minute:00"));

    try {
        // eventsテーブルへのデータ挿入
        $sql_events = "INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                       VALUES (:name, :detail, :edit_password, NOW(), NOW())";
        $stmt_events = $db->prepare($sql_events);
        $stmt_events->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $edit_password,
        ]); 

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

        // 参加者に渡すURLの表示
        $event_url = "http://yourdomain.com/event_details.php?id=" . $event_id; // イベントIDを含むURLを作成
        echo "イベントが作成されました。参加者はこちらのURLからイベント詳細をご確認ください: <a href='$event_url'>$event_url</a>";
        exit; // 追加後は処理を終了してページ遷移

    } catch (PDOException $e) {
        // データベース挿入時のエラーハンドリング
        echo "データベースエラー: " . $e->getMessage();
        echo "<br><strong>デバッグ情報:</strong><br>";
        echo "SQL: $sql_events <br>";
        echo "Bindings: ";
        print_r([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $edit_password,
        ]);
        exit();
    }
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
        <select id="start-time-of-day" name="start_time_of_day">
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
        <select id="end-time-of-day" name="end_time_of_day">
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


