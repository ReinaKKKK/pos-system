<?php

require_once('/var/www/html/arrange/database/db.php');

// デバッグ用コード: POSTデータとGETデータを確認popupのデータを受け取る
echo "<pre>";
echo "POSTデータ:\n";
print_r($_POST);
echo "</pre>";

// イベントIDと名前、編集パスワードを取得.「isset」はこの変数に値が入っているかどうかを確認するための関数
if (isset($_POST['event_id'], $_POST['name'], $_POST['edit_password'])) {
    $eventId = (int)$_POST['event_id'];
    $name = $_POST['name'];
    $participantPassword = $_POST['edit_password'];

    try {
        // データベースからイベントデータ取得してパスワード検証
        $stmt = $pdo->prepare('SELECT edit_password FROM users WHERE event_id = :event_id and name = :name'); //users テーブルから edit_password 列を選択し、event_id と name に基づいてフィルタリングします。:event_id と :name はプレースホルダで、実際の値は後でバインドされる
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT); //プレースホルダ :event_id に変数 $eventId の値を整数型としてバインド
        $stmt->bindValue(':name', $name, PDO::PARAM_STR); //プレースホルダ :name に変数 $name の値を文字列型としてバインド
        $stmt->execute(); //準備したSQLクエリを実行.バインドされた値がプレースホルダに挿入され、実際にデータベースに対してクエリが実行される
        $event = $stmt->fetch(PDO::FETCH_ASSOC); //実行したクエリの結果から1行を取得.結果は連想配列として $event 変数に格納

        if (!$event || !password_verify($participantPassword, $event['edit_password'])) {
            echo '<p>認証エラー: パスワードが正しくありません。</p>';
            exit;
        } // $event が存在しない（つまり、指定された条件に一致するユーザーが見つからない）場合や、参加者が入力したパスワード（$participantPassword）がデータベースに保存されているパスワード（$event['edit_password']）と一致しない場合、エラーメッセージを表示し、処理を終了します
        // 参加者の回答を取得。ハッシュ化されたパスワードとユーザーが入力したパスワードを比較する。
        $stmt = $pdo->prepare('SELECT responses.id, responses.availability_id, responses.response, responses.comment, availabilities.start_time, availabilities.end_time FROM responses JOIN availabilities ON responses.availability_id = availabilities.id WHERE availabilities.event_id = :event_id');
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
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response']) && isset($_POST['response_id'])) {
//     try {
//         // 各レスポンスを更新
//         foreach ($_POST['response'] as $responseId => $responseValue) {
//             // SQL文を準備
//             $stmt = $pdo->prepare('UPDATE responses SET response = :response, comment = :comment WHERE id = :response_id AND event_id = :event_id');
//             $stmt->bindValue(':response', $responseValue);
//             // コメントも一緒に更新
//             if (isset($_POST['comment'])) {
//                 $stmt->bindValue(':comment', $_POST['comment']);
//             } else {
//                 // コメントがない場合はNULLを設定
//                 $stmt->bindValue(':comment', null, PDO::PARAM_NULL);
//             }
//             $stmt->bindValue(':response_id', $_POST['response_id']);
//             // event_idもバインド
//             if (isset($_POST['event_id'])) {
//                 $stmt->bindValue(':event_id', $_POST['event_id'], PDO::PARAM_INT);
//             }

//             // 実行
//             $stmt->execute();
//         }

//         // 更新後はリダイレクト
//         header("Location: submit_response.php");
//         exit;
//     } catch (PDOException $e) {
//         echo "更新エラー: " . htmlspecialchars($e->getMessage());
//     }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    try {
        foreach ($_POST['response'] as $responseId => $responseValue) {
            $stmt = $pdo->prepare('UPDATE responses SET response = :response, comment = :comment WHERE id = :response_id AND event_id = :event_id');
            $stmt->bindValue(':response', $responseValue);
            $stmt->bindValue(':comment', $_POST['comment'][$responseId] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':response_id', $responseId, PDO::PARAM_INT);
            $stmt->bindValue(':event_id', $_POST['event_id'], PDO::PARAM_INT);

            $stmt->execute();
        }

        header("Location: submit_response.php");
        exit;
    } catch (PDOException $e) {
        echo "更新エラー: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>参加者の回答編集</title>
</head>
<body>
    <h1>回答編集画面</h1>
    <form action="" method="POST">
        <input type="hidden" id="event_id_field" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        <input type="hidden" name="edit_password" value="<?php echo htmlspecialchars($_POST['edit_password']); ?>">
        <table border="1">
            <tr>
                <th>日程</th>
                <th>回答</th>
            </tr>
            <?php foreach ($responses as $response) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($response['start_time']) . ' - ' . htmlspecialchars($response['end_time']); ?></td>
                    <td>
                        <select name="response[<?php echo htmlspecialchars($response['id']); ?>]">
                            <option value="1" <?php echo $response['response'] == 1 ? 'selected' : ''; ?>>〇</option>
                            <option value="2" <?php echo $response['response'] == 2 ? 'selected' : ''; ?>>×</option>
                            <option value="3" <?php echo $response['response'] == 3 ? 'selected' : ''; ?>>△</option>
                        </select>
                    </td>
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                    <input type="hidden" name="edit_password" value="<?php echo htmlspecialchars($_POST['edit_password']); ?>">
                </tr>
            <?php endforeach; ?>
            <tr>
        <td>コメント</td>
        <td>
            <textarea name="comment[<?php echo htmlspecialchars($response['id']); ?>]" rows="4" cols="50"><?php echo isset($response['comment']) ? htmlspecialchars($response['comment']) : ''; ?></textarea>
        </td>
    </tr>
        </table>
    
        <br>
        <input type="submit" value="更新">
    </form>
</body>
</html>
