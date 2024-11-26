<?php

// include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
// require '../public/createEvent.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';
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
        <input type='datetime-local' id='startTime' name='startTime' required>
        </label><br><br>

        <label>
        終了時間:
        <input type='datetime-local' id='endTime' name='endTime' required>
        </label><br><br>

        <button id='btn' type='button'>候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id='timeSlotContainer'></div>

        <label>編集パスワード:</label>
        <input type='password' name='editPassword'><br><br>

        <input type='hidden' id='timeSlotsInput' name='timeSlots'> <!-- 候補日時をここに送信 -->
        <button type='submit' id='submitFormButton'>イベントを作成</button>

    </form>
</body>
</html>
