<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/arrange/public/createEvent.php';
use arrange\publicFunctions\createEvent;

require '../config/env.php';
// require_once '../request/event/eventFunctions.php';


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

    // エラーがあれば表示して処理を終了
    if (!empty($errors)) {
        foreach ($errors as $field => $error) {
            echo "Error in $field: $error<br>";
        }
        exit;
    }

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

        // URL生成と表示
        $eventUrl = BASE_URL . '?event_id=' . $eventId;
        echo '<p>イベントが作成されました。参加者には以下のURLを送ってください：</p>';
        echo '<p><a href="' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '</a></p>';
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

/**
 * Generates time slots within a specified time range, dividing by 1-hour intervals.
 *
 * @param string $startTime The start time in 'Y-m-d H:i:s' format.
 * @param string $endTime   The end time in 'Y-m-d H:i:s' format.
 *
 * @return array An array of time slots. Each slot is an associative array with
 *               'startTime' and 'endTime' keys in 'Y-m-d H:i:s' format.
 */
function generateTimeSlots($startTime, $endTime)
{
    // 時間スロットを生成するロジック（例: 1時間ごと）
    $timeSlots = [];
    $current = strtotime($startTime);
    $end = strtotime($endTime);

    while ($current < $end) {
        $slotEnd = min($end, strtotime('+1 hour', $current));
        $timeSlots[] = [
            'startTime' => date('Y-m-d H:i:s', $current),
            'endTime' => date('Y-m-d H:i:s', $slotEnd),
        ];
        $current = $slotEnd;
    }

    return $timeSlots;
}
