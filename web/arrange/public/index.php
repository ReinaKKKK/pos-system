<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/request/event/store.php';

// タイムゾーンを東京に設定
date_default_timezone_set('Asia/Tokyo');

// POSTリクエストがある場合のみイベント作成処理を実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTリクエストからデータを取得
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : ''; // detailは任意
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : '';
    $timeSlots = isset($_POST['timeSlots']) ? json_decode($_POST['timeSlots'], true) : [];

// 入力データのバリデーション
    $errors = [];

    // 必須項目の検証
    if (!empty($name) && maxLength($name)) {
        $errors[] = createErrorMessage('max_length', 'イベント名');
    }
    // 時間スロットの検証
    foreach ($timeSlots as $slot) {
        if (isInvalidTimeRange($slot['startTime'], $slot['endTime'])) {
            $errors[] = createErrorMessage('time_setting', '時間スロット');
        }
    }

    // バリデーションエラーがあれば終了
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit;
    }

    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // `events` テーブルにデータを挿入
        $sqlEvents = 'INSERT INTO events (name, detail, edit_password, created_at, updated_at) 
                      VALUES (:name, :detail, :edit_password, NOW(), NOW())';
        $stmtEvents = $pdo->prepare($sqlEvents);
        $stmtEvents->execute([
            ':name' => $name,
            ':detail' => $detail,
            ':edit_password' => $editPassword,
        ]);

        // 挿入されたイベントIDを取得
        $eventId = $pdo->lastInsertId();
        if ($eventId === false) {
            throw new Exception("イベントIDの取得に失敗しました。");
        }

        // `availabilities` テーブルに複数の日時スロットを挿入
        $sqlAvailabilities = 'INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                              VALUES (:event_id, :start_time, :end_time, NOW(), NOW())';
        $stmtAvailabilities = $pdo->prepare($sqlAvailabilities);

        foreach ($timeSlots as $slot) {
            $stmtAvailabilities->execute([
                ':event_id' => $eventId,
                ':start_time' => $slot['startTime'],
                ':end_time' => $slot['endTime'],
            ]);
        }

        // トランザクションをコミット
        $pdo->commit();

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
        $pdo->rollBack();
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
</head>
<header>
      <div class="container">
        <div class="header-left">
          <img class="logo" src="https://itviec.com/rails/active_storage/representations/proxy/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBMVlqUFE9PSIsImV4cCI6bnVsbCwicHVyIjoiYmxvYl9pZCJ9fQ==--76777d2ebf9ab42c750bffca6eb909302c5107bf/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaDdCem9MWm05eWJXRjBPZ2wzWldKd09oSnlaWE5wZW1WZmRHOWZabWwwV3dkcEFhb3ciLCJleHAiOm51bGwsInB1ciI6InZhcmlhdGlvbiJ9fQ==--bb0ebae071595ab1791dc0ad640ef70a76504047/Yet.png">
        </div>
        <div class="header-right">
        </div>
      </div>
  </header>
<body>
  
  <div class="top-wrapper">
  <div class="container">
    <form action='index.php' method='POST'>
        
        <label>イベント名:</label>
        <input type='text' name='name' required><br><br>

        <label>イベント詳細:</label>
        <textarea name='detail'></textarea><br><br>

        <label>開始時間:</label>
        <input type='datetime-local' id='startTime' name='startTime' required><br><br>

        <label>終了時間:</label>
        <input type='datetime-local' id='endTime' name='endTime' required><br><br>

        <button type='button' id='addSlotBtn'>候補日を追加</button>

        <!-- 日付を表示する部分 -->
        <div id='timeSlotContainer'></div>
        <button type='button' id='deleteSchedule'>消す</button>

        <label>編集パスワード:※イベント内容を変更する際に使用します。</label>
        <input type='password' name='editPassword'><br><br>

        <input type='hidden' id='timeSlotsInput' name='timeSlots'> <!-- 候補日時をここに送信 -->
        <button type='submit'>イベントを作成</button>

    </form>
    </div>
    <div class="top-wrapper">
    <script>
document.getElementById('addSlotBtn').addEventListener('click', function() {
    // 開始時間と終了時間を取得
    var startTime = document.getElementById('startTime').value;
    var endTime = document.getElementById('endTime').value;
    
    // 時間スロットを生成
    var timeSlotContainer = document.getElementById('timeSlotContainer');
    var newSlot = document.createElement('div');
    newSlot.textContent = '開始: ' + startTime + ' 終了: ' + endTime;

    // 新しいスロットを表示する
    timeSlotContainer.appendChild(newSlot);

    // 入力した時間スロットをフォームに追加
    var timeSlotsInput = document.getElementById('timeSlotsInput');
    var existingTimeSlots = timeSlotsInput.value ? JSON.parse(timeSlotsInput.value) : [];
    existingTimeSlots.push({ startTime: startTime, endTime: endTime });
    timeSlotsInput.value = JSON.stringify(existingTimeSlots);
});
</script>
<footer>
      <div class="container">
        <img src="https://itviec.com/rails/active_storage/representations/proxy/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBMVlqUFE9PSIsImV4cCI6bnVsbCwicHVyIjoiYmxvYl9pZCJ9fQ==--76777d2ebf9ab42c750bffca6eb909302c5107bf/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaDdCem9MWm05eWJXRjBPZ2wzWldKd09oSnlaWE5wZW1WZmRHOWZabWwwV3dkcEFhb3ciLCJleHAiOm51bGwsInB1ciI6InZhcmlhdGlvbiJ9fQ==--bb0ebae071595ab1791dc0ad640ef70a76504047/Yet.png">
        <p>Learn to code, learn to be creative.</p>
      </div>
    </footer>
</body>
</html>
