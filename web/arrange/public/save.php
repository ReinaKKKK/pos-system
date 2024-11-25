<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/arrange/public/createEvent.php';
use arrange\publicFunctions\createEvent;

require '../config/env.php';
require '../request/event/store.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
require_once '../request/event/eventFunctions.php';


// POSTリクエストがある場合のみ処理を行う
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // イベント名を取得
    $name = $_POST['name'];

    // イベントを保存し、イベントIDを取得
    $eventId = createEvent($name, $databaseConnection);

    // saveEventが成功した場合のみURLを生成
    if ($eventId) {
        // 参加者用のURLを生成（BASE_URL + event_id）
        $eventUrl = BASE_URL . "?event_id=" . $eventId;
        // 成功した場合、URLを表示
        echo '<p>イベントが作成されました。参加者には以下のURLを送ってください：</p>';
        echo '<p><a href="' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8') . '</a></p>';
    } else {
        // イベントIDが生成できなかった場合のエラーハンドリング
        echo '<p>イベント作成に失敗しました。もう一度お試しください。</p>';
    }
} else {
    // POSTリクエスト以外でアクセスした場合のエラーハンドリング
    echo '<p>不正なリクエストです。再度お試しください。</p>';
}
