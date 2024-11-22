<?php

require '../config/env.php';
require '../request/event/store.php';

// イベントIDを生成（UUIDを使用）
$eventId = uniqid('event_', true); // UUIDを使いたければここを変更

// データベースに保存
if (saveEventToDatabase($eventId)) {
    // 完全なURLを生成
    $event_url = BASE_URL . '?event_id=' . $eventId;

    // イベント作成後に自動的にリダイレクト
    header('Location: ' . $event_url);
    exit;
} else {
    // エラーメッセージを返す
    echo "イベント作成に失敗しました。";
}
