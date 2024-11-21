<?php

session_start(); // セッション開始

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/config/env.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/service/functions.php';

// URLパラメータ「event_id」がセットされている場合、その値を取得
$event_id = $_GET['event_id'] ?? null;

if ($event_id) {
    // event_idが存在する場合、イベントURLを生成
    $event_url = rtrim(BASE_URL, '/') . '/events/' . urlencode($event_id);
} else {
    echo 'イベントIDが設定されていません。';
    exit;
}
?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>イベント作成完了</title>
</head>
<body>
<h1>イベントが作成されました！</h1>

<p>参加者用のURLはこちらです：</p>

<!-- イベントURLをリンクとして表示 -->
<p><a href='<?php echo h($event_url); ?>' target='_blank'>
    <?php echo h($event_url); ?>
</a></p>

<!-- event_idをHTML内で表示・利用する -->
<p id="eventId" data-id="<?php echo h($event_id); ?>"></p>
</body>
</html>
