<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : '';
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $eventId = $_POST['event_id'];
    $event_url = $_POST['event_url'];

    // ここで日時を適切な形式に変換してデータベースに保存
    $startTime = date('Y-m-d H:i:s', strtotime($startTime));
    $endTime = date('Y-m-d H:i:s', strtotime($endTime));


    // 入力データのバリデーション
    $errors = validate([
        'name' => $name,
        'startTime' => $startTime,
        'endTime' => $endTime,
    ]);

    // エラーがあれば表示
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
        exit;
    }

    try {
        // トランザクション開始
        $db->beginTransaction();

        // eventsテーブルへのデータ挿入
        $sqlEvents = 'INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())';
        $stmtEvents = $db->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
        ]);

        $eventId = $db->lastInsertId();
        // availabilitiesテーブルに日時情報を挿入
        foreach ($timeSlots as $slot) {
            $startTime = $slot['startTime'];
            $endTime = $slot['endTime'];

            $sqlAvailabilities = 'INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                                  VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
            $stmtAvailabilities = $db->prepare($sqlAvailabilities);
            $stmtAvailabilities->execute([
                ':event_id' => $eventId,
                ':start_time' => $startTime,
                ':end_time' => $endTime,
            ]);
        }

        // トランザクションをコミット
        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        echo 'イベント作成に失敗しました。';
        error_log($e->getMessage()); // エラーログに記録
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <title>イベント作成</title>
    <link rel='stylesheet' href='style.css'> 
    <script src='script.js'></script>
</head>
<body>
    <form action='save.php' method='POST'>
        
        <label>イベント名:</label>
        <input type='text' name='name' required><br><br>

        <label>イベント詳細:</label>
        <textarea name='detail'></textarea><br><br>

        <label>
        開始時間:
        <input type="datetime-local" id="startTime" name="startTime" required>
        </label><br><br>

        <label>
        終了時間:
        <input type="datetime-local" id="endTime" name="endTime" required>
        </label><br><br>

        <button id="btn" type="button">候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id="timeSlotContainer"></div>

        <label>編集パスワード:</label>
        <input type='password' name='editPassword'><br><br>
        
        <input type="hidden" id="timeSlotsInput" name="timeSlots"> <!-- 候補日時をここに送信 -->
        <button type="submit" id="submitFormButton">イベントを作成</button>

    </form>
</body>
</html>

