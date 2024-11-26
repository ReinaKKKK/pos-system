<?php

require_once 'db.php';

if (isset($_GET['event_id'])) {
    $eventId = htmlspecialchars($_GET['event_id'], ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $databaseConnection->prepare(
            'SELECT u.name, r.response, r.comment 
             FROM responses r 
             JOIN users u ON r.user_id = u.id 
             WHERE u.event_id = :event_id'
        );
        $stmt->execute([':event_id' => $eventId]);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo '<p>データの取得中にエラーが発生しました。</p>';
        error_log($e->getMessage());
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
    <title>回答結果</title>
</head>
<body>
    <h1>回答結果</h1>
    <table border="1">
        <tr>
            <th>名前</th>
            <th>回答</th>
            <th>コメント</th>
        </tr>
        <?php foreach ($responses as $response) : ?>
            <tr>
                <td><?php echo htmlspecialchars($response['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($response['response'], ENT_QUOTES, 'UTF-8') === '1' ? '〇' : (htmlspecialchars($response['response'], ENT_QUOTES, 'UTF-8') === '0' ? '×' : '△'); ?></td>
                <td><?php echo htmlspecialchars($response['comment'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>





