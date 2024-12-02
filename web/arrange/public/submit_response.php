<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

    try {
        // イベント名を取得
        $stmt = $databaseConnection->prepare('SELECT name FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }

        // 回答一覧を取得
        $stmt = $databaseConnection->prepare('SELECT users.name AS user_name, responses.response, responses.comment 
                                              FROM responses 
                                              JOIN users ON responses.user_id = users.id 
                                              WHERE responses.event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'SQLエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>参加者の回答</title>
</head>
<body>
    <h1>イベント: <?php echo htmlspecialchars($event['name']); ?></h1>

    <!-- 回答一覧 -->
    <table border="1">
        <thead>
            <tr>
                <th>参加者</th>
                <th>回答</th>
                <th>コメント</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $response) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($response['user_id']); ?></td>
                    <td>
                        <?php
                        switch ($response['response']) {
                            case 1:
                                echo "〇";
                                break;
                            case 2:
                                echo "×";
                                break;
                            case 3:
                                echo "△";
                                break;
                            default:
                                echo "未回答";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($response['comment']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
