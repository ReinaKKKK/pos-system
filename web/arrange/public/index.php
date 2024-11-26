<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';
use arrange\publicFunctions\createEvent;

// POSTリクエストがある場合のみ処理を行う
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTリクエストからデータを取得
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : ''; // detailは任意
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : ''; // editPasswordも任意
    $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : '';
    $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : '';
    // 日時を適切な形式に変換
    $startTime = date('Y-m-d H:i:s', strtotime($startTime));
    $endTime = date('Y-m-d H:i:s', strtotime($endTime));

    // 入力データのバリデーション
    $errors = validate([
        'name' => $name,
        'startTime' => $startTime,
        'endTime' => $endTime,
    ]);

    try {
        // トランザクション開始
        $databaseConnection->beginTransaction();

        // `events` テーブルにデータを挿入
        $sqlEvents = 'INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())';
        $stmtEvents = $databaseConnection->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
        ]);

        // 挿入されたイベントIDを取得
        $eventId = $databaseConnection->lastInsertId();

        // `availabilities` テーブルに日時スロットを挿入
        $timeSlots = generateTimeSlots($startTime, $endTime);
        foreach ($timeSlots as $slot) {
            $sqlAvailabilities = 'INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                                  VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
            $stmtAvailabilities = $databaseConnection->prepare($sqlAvailabilities);
            $stmtAvailabilities->execute([
                ':event_id' => $eventId,
                ':start_time' => $slot['startTime'],
                ':end_time' => $slot['endTime'],
            ]);
        }

        // トランザクションをコミット
        $databaseConnection->commit();

        // URL表示ページにリダイレクト（event_idをURLパラメータとして渡す）
        header('Location: /arrange/display_url.php?event_id=' . $eventId);
        exit;
    } catch (PDOException $e) {
        // エラー発生時はロールバック
        $databaseConnection->rollBack();
        echo 'イベント作成に失敗しました。';
        error_log($e->getMessage()); // エラーログに記録
        exit;
    }
} else {
    // POSTリクエスト以外のアクセスはエラー表示
    echo '<p>不正なリクエストです。再度お試しください。</p>';
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
    <form action='index.php' method='POST'>
        
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
