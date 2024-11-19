<?php

require_once 'path/to/env.php';
require_once 'path/to/functions.php';
session_start(); // セッション開始

// URLパラメータ「event_id」がセットされている場合、その値を取得
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];  // URLパラメータからevent_idを取得
} else {
    // event_idが存在しない場合、エラーメッセージを表示して処理を終了
    echo "イベントURLが設定されていません。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント作成完了</title>
</head>
<body>
<h1>イベントが作成されました！</h1>

<p>参加者用のURLはこちらです：</p>

<!-- イベントURLをリンクとして表示 -->
//共通関数にする

<!-- htmlspecialchars の代わりに関数 h() を使用 -->
<p><a href="<?php echo h(BASE_URL . $event_id); ?>" target="_blank">
    <?php echo h(BASE_URL . $event_id); ?>
</a></p>

</body>
</html>
