<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

    try {
        // イベント名を取得
        $stmt = $pdo->prepare('SELECT name FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }

        // イベントの日程を取得
        $stmt = $pdo->prepare('SELECT id, start_time, end_time 
                                              FROM availabilities 
                                              WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 参加者とその回答を取得
        $stmt = $pdo->prepare('SELECT users.id AS user_id, users.name AS user_name, 
                                              responses.availability_id, responses.response, responses.comment 
                                              FROM responses 
                                              JOIN users ON responses.user_id = users.id 
                                              WHERE responses.availability_id IN (SELECT id FROM availabilities WHERE event_id = :event_id)');
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

// 参加者の回答を整理
$participantsResponses = [];
foreach ($responses as $response) {
    $participantsResponses[$response['user_name']][$response['availability_id']] = [
        'response' => $response['response'],
        'comment' => $response['comment']
    ];
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
                <th>日程</th>
                <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                    <th><?php echo htmlspecialchars($userName); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($availabilities as $availability) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($availability['start_time']) . ' - ' . htmlspecialchars($availability['end_time']); ?></td>
                    <?php foreach ($participantsResponses as $userName => $responsesForUser) : ?>
                        <td>
                            <?php
                            if (isset($responsesForUser[$availability['id']])) {
                                $response = $responsesForUser[$availability['id']]['response'];
                                $comment = $responsesForUser[$availability['id']]['comment'];
                                switch ($response) {
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
                                // コメントがあれば表示
                                if ($comment) {
                                    echo "<br>" . htmlspecialchars($comment);
                                }
                            } else {
                                echo "未回答";
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
