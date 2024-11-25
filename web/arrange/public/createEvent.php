<?php

namespace arrange\publicFunctions;

/**
 * 新しいイベントを作成します。
 *
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
