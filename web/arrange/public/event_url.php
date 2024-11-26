<?php

//  include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';
// // require_once $_SERVER['DOCUMENT_ROOT'] . '/arrange/public/createEvent.php';
// use arrange\publicFunctions\createEvent;

require '../config/env.php';
// require_once '../request/event/eventFunctions.php';
// イベントIDを取得（例：URLパラメータから）

$eventId = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if ($eventId) {
    // 参加者用のURLを作成
    $eventUrl = BASE_URL . "respond.php?event_id=" . urlencode($eventId);

    echo '<p>イベントが作成されました。参加者には以下のURLを送ってください：</p>';
    echo '<p><a href="' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '</a></p>';
} else {
    echo '<p>イベントIDが指定されていません。</p>';
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
