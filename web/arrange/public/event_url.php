<?php

// 必要な設定ファイルやライブラリをインクルード
require '../config/env.php';  // 環境設定ファイル
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php'; // データベース接続
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php'; // イベントの保存処理
/**
 *
 * Escapes special characters in a string for use in HTML.
 *
 * This function is a wrapper for `htmlspecialchars` to simplify usage and prevent XSS (Cross-Site Scripting) attacks.
 *
 * @param string $val The string to be escaped.
 *
 * @return string The escaped string.
 */
function h($val)
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

/**
 * 新しいイベントを作成します。
 * この関数は、イベント名をデータベースに保存し、新しく作成されたイベントのIDを返します。
 * 保存に成功した場合は、生成されたイベントIDを返します。失敗した場合はnullを返します。
 *
 * @param string $name               イベントの名前
 * @param PDO    $databaseConnection データベース接続を表す PDO オブジェクト
 * @return integer|null 作成されたイベントのID、失敗した場合はnull
 */
function createEvent($name, $databaseConnection)
{
    $eventId = null;

    if ($databaseConnection && $name) {
        try {
            // SQL文の準備と実行
            $stmt = $databaseConnection->prepare("INSERT INTO events (name) VALUES (:name)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();

            // 最後に挿入されたIDを取得
            $eventId = $databaseConnection->lastInsertId();
        } catch (PDOException $e) {
            // エラーをキャッチして表示
            echo 'データベースエラー: ' . $e->getMessage();
        }
    }

    return $eventId;
}

// イベントIDを取得（例：URLパラメータから）
$eventId = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if (!$eventId) {
     echo "<p>イベントIDが指定されていません。</p>";
     exit;
}

if ($eventId) {
    // 参加者用のURLを作成
    $eventUrl = BASE_URL . "respond.php?event_id=" . urlencode($eventId);

    echo '<p>イベントが作成されました。参加者には以下のURLを送ってください：</p>';
    echo '<p><a href="' . h($eventUrl) . '">' . h($eventUrl) . '</a></p>';
}
