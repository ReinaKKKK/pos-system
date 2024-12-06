<?php

require_once('/var/www/html/arrange/database/db.php');

// デバッグ用コード: POSTデータとGETデータを確認
echo "<pre>";
echo "POSTデータ:\n";
print_r($_POST);
echo "</pre>";

// イベントIDと編集パスワードを取得
if (isset($_POST['event_id'], $_POST['name'], $_POST['edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $name = $_POST['name'];
    $participantPassword = $_POST['edit_password'];

    try {
        // データベースからイベントデータ取得してパスワード検証
        $stmt = $pdo->prepare('SELECT edit_password FROM users WHERE id = :event_id and name = :name');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event || !password_verify($participantPassword, $event['edit_password'])) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        }

        // 参加者の回答を取得
        $stmt = $pdo->prepare('SELECT responses.id, responses.availability_id, responses.response, responses.comment, availabilities.start_time, availabilities.end_time FROM responses JOIN availabilities ON responses.availability_id = availabilities.id WHERE responses.event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDまたはパスワードが無効です。</p>';
    exit;
}

// POSTデータ処理：ユーザーが更新を送信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response']) && isset($_POST['response_id'])) {
    try {
        $stmt = $pdo->prepare('UPDATE responses SET response = :response, comment = :comment WHERE id = :response_id');
        $stmt->bindValue(':response', $_POST['response']);
        $stmt->bindValue(':comment', $_POST['comment']);
        $stmt->bindValue(':response_id', $_POST['response_id']);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "更新エラー: " . $e->getMessage();
    }
}

?>

// // POSTで受け取ったデータをチェック
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $eventId = $_POST['event_id'] ?? '';
//     $editPassword = $_POST['edit_password'] ?? '';
//     $userName = $_POST['name'] ?? '';

//     if (!$userId) {
//         echo "ユーザーIDが提供されていません。";
//         exit;
//     }
//     try {
//         // パスワード検証
//         $stmt = $pdo->prepare(
//             'SELECT r.*, u.name AS user_name, a.start_time, a.end_time 
// //              FROM responses r 
// //              JOIN users u ON r.user_id = u.id 
// //              JOIN availabilities a ON r.availability_id = a.id
// //              WHERE u.event_id = :event_id AND r.user_id = :user_id'
// //         );
//         $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
//         $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
//         $stmt->execute();
//         $responseData = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         if ($responseData) {
//             echo json_encode($responseData); // 結果をJSON形式で返す
//         } else {
//             echo "<p>データが見つかりませんでした。</p>";
//         }
//     } catch (PDOException $e) {
//         echo "エラー: " . $e->getMessage();
//         exit;
//     }
// } else {
//     echo "データ不足";
//     exit;
// }

    //     // 回答データを取得する
    //     $stmt = $pdo->prepare('SELECT * FROM responses WHERE user_id = :user_id AND event_id = :event_id');
    //     $stmt->bindValue(':user_id', $responseData['id'], PDO::PARAM_INT);
    //     $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $response = $stmt->fetch(PDO::FETCH_ASSOC);
    // } catch (PDOException $e) {
    //     echo "エラー: " . $e->getMessage();
    //     exit;
    // }


// if (isset($_POST['event_id']) && isset($_POST['edit_password'])) {
//     $eventId = (int)$_POST['event_id'];
//     $edit_password = $_POST['edit_password'];
//     // デバッグ: 受け取ったパスワードを表示
//     echo "受け取ったパスワード: " . htmlspecialchars($edit_password) . "<br>";

//     // `users` テーブルで参加者用のパスワード認証を確認
//     $stmt = $pdo->prepare('SELECT edit_password FROM users WHERE event_id = :event_id');
//     $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
//     $stmt->execute();
//     $storedPassword = $stmt->fetchColumn();
//     // $samplehash = password_hash('12345678',PASSWORD_DEFAULT);
//     // var_dump(password_verify('12345678', $samplehash));
//     // パスワードが正しい場合
//     // var_dump($storedPassword);
//     // var_dump($edit_password);
//     // var_dump(password_verify($edit_password, $storedPassword));
//     if ($storedPassword && password_verify($edit_password, $storedPassword)) {

//         // パスワードが正しい場合
//         var_dump(100);
//         if (isset($_POST['response'])) {
//             var_dump(200);
//             $response = (int)$_POST['response'];
//             // コメントが空の場合でも処理を行う
//             $comment = isset($_POST['comment']) ? $_POST['comment'] : ''; // 空文字を許可

//             // データベース更新処理
//             $stmt = $pdo->prepare('
//             UPDATE responses
//             SET response = :response, comment = IFNULL(:comment, "")
//             WHERE availability_id IN (
//                 SELECT id FROM availabilities WHERE event_id = :event_id
//             )
//         ');
//             $stmt->bindValue(':response', $response, PDO::PARAM_INT);
//             $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
//             $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
//             $stmt->execute();
//             echo "回答が更新されました。";
//         } else {
//             echo "無効な入力です。";
//         }
//     } else {
//         echo "パスワードが正しくありません。";
//     }
// } else {
//     echo "イベントIDとパスワードが必要です。";
// }


// // イベントIDを取得
// if (isset($_GET['event_id']) && isset($_POST['edit_password'])) {
//     $eventId = (int)$_GET['event_id'];

    // $participantPassword = $_POST['edit_password'];

    

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>参加者の回答編集</title>
</head>
<body>
    <h1>回答編集画面</h1>
    <form action="" method="POST">
        <input type="hidden" name="edit_password" value="<?php echo htmlspecialchars($_POST['edit_password']); ?>">
        <table border="1">
            <tr>
                <th>日程</th>
                <th>回答</th>
                <th>コメント</th>
            </tr>
            <?php foreach ($responses as $response) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($response['start_time']) . ' - ' . htmlspecialchars($response['end_time']); ?></td>
                    <td>
                        <select name="response">
                            <option value="1" <?php echo $response['response'] == 1 ? 'selected' : ''; ?>>〇</option>
                            <option value="2" <?php echo $response['response'] == 2 ? 'selected' : ''; ?>>×</option>
                            <option value="3" <?php echo $response['response'] == 3 ? 'selected' : ''; ?>>△</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="comment" value="<?php echo htmlspecialchars($response['comment']); ?>">
                    </td>
                    <input type="hidden" name="response_id" value="<?php echo htmlspecialchars($response['id']); ?>">
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <input type="submit" value="更新">
    </form>
</body>
</html>
