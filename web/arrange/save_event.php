<?php
session_start(); // セッションを開始。セッションデータを使用できるようにする

// URLパラメータ「event_id」がセットされている場合、その値を取得
var_dump($_GET);
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
    <meta charset="UTF-8"> <!-- 文字コードをUTF-8に設定 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- レスポンシブデザイン対応 -->
    <title>イベント作成完了</title> <!-- ページのタイトルを設定 -->
</head>
<body>
<h1>イベントが作成されました！</h1> <!-- ページの見出し（イベント作成完了） -->

<p>参加者用のURLはこちらです：</p> <!-- 説明文：参加者用のURLを表示 -->

<!-- イベントURLをリンクとして表示 -->
<p><a href="<?php echo htmlspecialchars('http://localhost:18888/arrange/'. $event_id, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
    <?php echo htmlspecialchars('http://localhost:18888/arrange/'.$event_id, ENT_QUOTES, 'UTF-8'); ?>
</a></p> <!-- htmlspecialcharsを使ってURLをエスケープし、安全に表示 -->
</body>
</html>
