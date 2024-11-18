<?php
include 'db.php'; 

// バリデーション
function validate($data) {
    $errors = [];

    // イベント名が空か255文字を超えている場合
    if (empty($data['name'])) {
        $errors[] = "イベント名は必須です。";
    } elseif (strlen($data['name']) > 255) {
        $errors[] = "イベント名は255文字以下で入力してください。";
    }

    // 日付と時刻のチェック
    if (empty($data['date']) || empty($data['startTimeHour']) || empty($data['endTimeHour'])) {
        $errors[] = "候補日時を入れてください。";
    }

    // 終了時刻が開始時刻より前か、無効な時間の場合
    if ($data['endHour24'] <= $data['startHour24'] || $data['endHour24'] >= 24 || $data['endHour24'] < 0) {
        $errors[] = "終了時刻が無効です。開始時刻より遅い時間を指定してください。また、終了時刻は24時間以内で入力してください。";
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : '';
    $date = $_POST['date'];
    $startTimeHour = $_POST['startTimeHour'];
    $startTimeMinute = $_POST['startTimeMinute'];
    $startTimeOfDay = $_POST['startTimeOfDay'];
    $endTimeHour = $_POST['endTimeHour'];
    $endTimeMinute = $_POST['endTimeMinute'];
    $endTimeOfDay = $_POST['endTimeOfDay'];
        // AM/PMを24時間形式に変換
    $startHour24 = ($startTimeOfDay === 'PM' && $startTimeHour != 12) ? $startTimeHour + 12 : ($startTimeOfDay === 'AM' && $startTimeHour == 12 ? 0 : $startTimeHour);
    $endHour24 = ($endTimeOfDay === 'PM' && $endTimeHour != 12) ? $endTimeHour + 12 : ($endTimeOfDay === 'AM' && $endTimeHour == 12 ? 0 : $endTimeHour);


    // 入力データのバリデーション
    $errors = validate([
        'name' => $name,
        'date' => $date,
        'startTimeHour' => $startTimeHour,
        'endTimeHour' => $endTimeHour,
        'startHour24' => $startHour24,
        'endHour24' => $endHour24
    ]);

    // エラーがあれば表示
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        exit;
    }
    
    // 24時間形式の時間と日付を結合してDATETIMEを作成
    $startTime = date("Y-m-d H:i:s", strtotime("$date $startHour24:$startTimeMinute:00"));
    $endTime = date("Y-m-d H:i:s", strtotime("$date $endHour24:$endTimeMinute:00"));

    try {
        // トランザクション開始
        $db->beginTransaction();
    
        // eventsテーブルへのデータ挿入
        $sqlEvents = "INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())";
        $stmtEvents = $db->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
        ]); 
    
        $eventId = $db->lastInsertId(); // 追加したイベントIDを取得
    
        // availabilitiesテーブルに日時情報を挿入
        $sqlAvailabilities = "INSERT INTO availabilities (event_id, date, start_time, end_time, created_at, updated_at) 
                              VALUES (:event_id, :date, :start_time, :end_time, NOW(), NOW())";
        $stmtAvailabilities = $db->prepare($sqlAvailabilities);
        $stmtAvailabilities->execute([
            ':event_id' => $eventId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ]);
    
        // トランザクションをコミット
        $db->commit();
    
    } catch (PDOException $e) {
        // エラーが発生した場合はロールバック
        $db->rollBack();
        
        // エラーメッセージとデバッグ情報を表示
        echo "データベースエラー: " . $e->getMessage();
        echo "<br><strong>デバッグ情報:</strong><br>";
        echo "SQL: $sqlEvents <br>";
        echo "Bindings: ";
        print_r([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
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
        
        <label for="startTimeOfDay">時間帯:</label>
        <select id="startTimeOfDay" name="startTimeOfDay">
            <option value="AM">午前</option>
            <option value="PM">午後</option>
        </select><br><br>

        <label for="startTimeHour">時間:</label>
        <select id="startTimeHour" name="startTimeHour"></select>
        <br><br>

        <label for="startTimeMinute">分:</label>
        <select id="startTimeMinute" name="startTimeMinute"></select>
        <br><br>
        <text> ～</text><br><br>

        <label for="endTimeOfDay">時間帯:</label>
        <select id="endTimeOfDay" name="endTimeOfDay">
            <option value="AM">午前</option>
            <option value="PM">午後</option>
        </select><br><br>

        <label for="endTimeHour">時間:</label>
        <select id="endTimeHour" name="endTimeHour"></select>
        <br><br>
        <label for="endTimeMinute">分:</label>
        <select id="endTimeMinute" name="endTimeMinute"></select>
        <br><br>

        <button type="button" onclick="addTimeSlot()">候補を追加</button><br><br>

        <!-- 候補日を表示するコンテナ -->
        <div id="timeSlotContainer"></div>

        <label>編集パスワード:</label>
        <input type="password" name="editPassword"><br><br>
        <input type="submit" value="イベントを作成">
    </form>
</body>
</html>