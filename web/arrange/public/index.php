<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';

// POSTリクエストがある場合のみイベント作成処理を実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTリクエストからデータを取得
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : ''; // detailは任意
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : ''; // editPasswordも任意
    $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : '';
    $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : '';

    // 日時を適切な形式に変換
    $startTime = date('Y-m-d H:i:s', strtotime($startTime));
    $endTime = date('Y-m-d H:i:s', strtotime($endTime));

    // 入力データのバリデーション
    $errors = validate([
        'name' => $name,
        'startTime' => $startTime,
        'endTime' => $endTime,
    ]);

    try {
        // トランザクション開始
        $databaseConnection->beginTransaction();

        // `events` テーブルにデータを挿入
        $sqlEvents = 'INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())';
        $stmtEvents = $databaseConnection->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
        ]);

        // 挿入されたイベントIDを取得
        $eventId = $databaseConnection->lastInsertId();
        if ($eventId === false) {
            throw new Exception("イベントIDの取得に失敗しました。");
        }

/**
     * Generate time slots between the given start and end times.
     *
     * This function divides the given time range into hourly slots and
     * returns them in an array with 'startTime' and 'endTime' keys.
     *
     * @param string $startTime The start time in 'Y-m-d H:i:s' format.
     * @param string $endTime   The end time in 'Y-m-d H:i:s' format.
     * @return array An array of time slots, each containing 'startTime' and 'endTime'.
     */
        function generateTimeSlots($startTime, $endTime)
        {
            // 時間スロットを生成するロジック（例: 1時間ごと）
            $timeSlots = [];
            $current = strtotime($startTime);
            $end = strtotime($endTime);

            while ($current < $end) {
                $slotEnd = min($end, strtotime('+1 hour', $current));
                $timeSlots[] = [
                    'startTime' => date('Y-m-d H:i:s', $current),
                    'endTime' => date('Y-m-d H:i:s', $slotEnd),
                ];
                $current = $slotEnd;
            }

            return $timeSlots;
        }

        // `availabilities` テーブルに日時スロットを挿入
        $timeSlots = generateTimeSlots($startTime, $endTime);
        foreach ($timeSlots as $slot) {
            $sqlAvailabilities = 'INSERT INTO availabilities (
            event_id, start_time, end_time, created_at, updated_at) 
                                VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
            $stmtAvailabilities = $databaseConnection->prepare($sqlAvailabilities);
            $stmtAvailabilities->execute([
                ':event_id' => $eventId,
                ':start_time' => $slot['startTime'],
                ':end_time' => $slot['endTime'],
            ]);
        }


        // トランザクションをコミット
        $databaseConnection->commit();

        // イベントURLを表示するページ（event_url.php）へリダイレクト
        if (!headers_sent()) {
            header('Location: event_url.php?event_id=' . urlencode($eventId));
            exit;
        } else {
            // リダイレクト失敗時には手動でアクセスするためのリンクを表示
            echo "リダイレクトに失敗しました。手動でアクセスしてください。";
            echo '<a href="event_url.php?event_id=' . htmlspecialchars($eventId, ENT_QUOTES) . '">ここをクリック</a>';
            exit;
        }
    } catch (PDOException $e) {
        // データベース関連のエラーをキャッチしてロールバック
        $databaseConnection->rollBack();
        echo "データベースエラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        error_log($e->getMessage());
    } catch (Exception $e) {
        // その他の例外をキャッチ
        echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <title>イベント作成</title>
    <link rel='stylesheet' href='style.css'> 
    <script src='script.js'></script>
</head>
<body>
    <form action='index.php' method='POST'>
        
        <label>イベント名:</label>
        <input type='text' name='name' required><br><br>

        <label>イベント詳細:</label>
        <textarea name='detail'></textarea><br><br>

        <label>
        開始時間:
        <input type='datetime-local' id='startTime' name='startTime' required>
        </label><br><br>

        <label>
        終了時間:
        <input type='datetime-local' id='endTime' name='endTime' required>
        </label><br><br>

        <button id='btn' type='button'>候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id='timeSlotContainer'></div>

        <label>編集パスワード:</label>
        <input type='password' name='editPassword'><br><br>

        <input type='hidden' id='timeSlotsInput' name='timeSlots'> <!-- 候補日時をここに送信 -->
        <button type='submit' id='submitFormButton'>イベントを作成</button>

    </form>
</body>
</html>
