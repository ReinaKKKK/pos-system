<?php

// イベントIDを指定（例えば、GET パラメータで受け取る）
$event_id = isset($_POST['event_id']) ? $_POST['event_id'] : 1;  // デフォルトで1とする
require_once('/var/www/html/arrange/database/db.php');
echo "Event ID: " . $event_id;  // 確認用

try {
    // SQLクエリで必要なデータを取得
    $sql = "SELECT a.start_time, a.end_time, u.name AS user_name, r.response, r.comment
            FROM responses r
            JOIN availabilities a ON r.availability_id = a.id
            JOIN users u ON r.user_id = u.id
            WHERE a.event_id = :event_id
            ORDER BY a.start_time, u.name";

    // クエリを準備
    $stmt = $databaseConnection->prepare($sql);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

    // クエリ実行
    $stmt->execute();

    // 結果を取得
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // テーブルデータの整理
    $availabilities = [];  // 候補日時（新規追加）
    $participants = [];    // 参加者
    $data = [];            // 参加者ごとの回答データ
    $countPositive = [];   // 〇の数をカウント

    foreach ($responses as $response) {
        // 候補日時のキー
        $dateKey = $response['start_time'] . ' ～ ' . $response['end_time'];
        if (!in_array($dateKey, $availabilities)) {
            $availabilities[] = $dateKey;
        }

        // 参加者名
        $userName = $response['user_name'];
        if (!in_array($userName, $participants)) {
            $participants[] = $userName;
            $countPositive[$userName] = 0; // 初期値
        }

        // 回答データを格納
        $data[$userName][$dateKey] = [
            'response' => $response['response'],
            'comment' => $response['comment'],
        ];

        // 〇の数をカウント
        if ($response['response'] == '1') { // 1: 〇
            $countPositive[$userName]++;
        }
    }

    // テーブルを作成
    echo "<table border='1'>
            <thead>
                <tr>
                    <th>参加者</th>";
    foreach ($availabilities as $availability) {
        echo "<th>" . htmlspecialchars($availability, ENT_QUOTES, 'UTF-8') . "</th>";
    }
    echo "<th>〇の数</th>";
    echo "</tr>
            </thead>
            <tbody>";

    foreach ($participants as $participant) {
        echo "<tr>
                <td>" . htmlspecialchars($participant, ENT_QUOTES, 'UTF-8') . "</td>";
        foreach ($availabilities as $availability) {
            if (isset($data[$participant][$availability])) {
                $response = $data[$participant][$availability]['response'];
                $comment = htmlspecialchars($data[$participant][$availability]['comment'], ENT_QUOTES, 'UTF-8');

                // 回答を表示（〇, ×, △）
                switch ($response) {
                    case '1':
                        $displayResponse = '〇';
                        break;
                    case '2':
                        $displayResponse = '×';
                        break;
                    case '3':
                        $displayResponse = '△';
                        break;
                    default:
                        $displayResponse = '-';
                }

                echo "<td>" . $displayResponse . "<br><small>" . $comment . "</small></td>";
            } else {
                echo "<td>-</td>";
            }
        }
        echo "<td>" . $countPositive[$participant] . "</td>"; // 〇の数
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
} catch (PDOException $e) {
    echo "SQLエラー: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>参加者の回答一覧</title>
</head>
<body>
    <h1>参加者の回答一覧</h1>
    <table>
        <thead>
            <tr>
                <th>候補日時</th>
                <?php foreach ($participants as $participant) : ?>
                    <th><?php echo htmlspecialchars($participant, ENT_QUOTES, 'UTF-8'); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($availabilities as $availability) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($availability, ENT_QUOTES, 'UTF-8'); ?></td>
                    <?php foreach ($participants as $participant) : ?>
                        <td>
                            <?php
                            // 〇×△を表示
                            $response = isset($data[$participant][$availability]) ? $data[$participant][$availability]['response'] : null;
                            switch ($response) {
                                case '1':
                                    echo '〇';
                                    break;
                                case '2':
                                    echo '×';
                                    break;
                                case '3':
                                    echo '△';
                                    break;
                                default:
                                    echo 'なし';
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
