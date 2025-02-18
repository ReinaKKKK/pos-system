<?php

require_once('/var/www/html/arrange/database/db.php');

// イベントIDを取得
if (isset($_GET['event_id'])) {
    $eventId = (int)$_GET['event_id'];

    try {
        // イベント名を取得
        $stmt = $pdo->prepare('SELECT name, edit_password FROM events WHERE id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            echo '<p>指定されたイベントが見つかりません。</p>';
            exit;
        }

        // イベントの日程を取得
        $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM availabilities WHERE event_id = :event_id');
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'SQLエラー: ' . $e->getMessage();
        exit;
    }
} else {
    echo '<p>イベントIDが指定されていません。</p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'] ?? '';
    $availabilityIds = $_POST['availability_id'] ?? [];
    $startTimes = $_POST['start_time'] ?? [];
    $endTimes = $_POST['end_time'] ?? [];
    $deleteIds = $_POST['delete_id'] ?? [];

    try {
        // イベント名の更新
        if (!empty($eventName)) {
            $stmt = $pdo->prepare('UPDATE events SET name = :name WHERE id = :event_id');
            $stmt->bindValue(':name', $eventName);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
        }

        // 日程の更新
        for ($i = 0; $i < count($availabilityIds); $i++) {
            $stmt = $pdo->prepare('UPDATE availabilities SET start_time = :start_time, end_time = :end_time WHERE id = :availability_id');
            $stmt->bindValue(':start_time', $startTimes[$i]);
            $stmt->bindValue(':end_time', $endTimes[$i]);
            $stmt->bindValue(':availability_id', $availabilityIds[$i], PDO::PARAM_INT);
            $stmt->execute();
        }

       // 新しい日程の追加（INSERT）
        for ($i = 0; $i < count($startTimes); $i++) {
            if (empty($availabilityIds[$i])) {  // availability_idが空の場合、新規追加
                $stmt = $pdo->prepare('
                    INSERT INTO availabilities (event_id, start_time, end_time, created_at, updated_at) 
                    VALUES (:event_id, :start_time, :end_time, NOW(), NOW())
                ');
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':start_time', $startTimes[$i]);
                $stmt->bindValue(':end_time', $endTimes[$i]);
                $stmt->execute();
            }
        }

        // 削除対象の日程があれば削除
        if (!empty($deleteIds)) {
            $stmt = $pdo->prepare('DELETE FROM availabilities WHERE id IN (' . implode(',', array_fill(0, count($deleteIds), '?')) . ')');
            foreach ($deleteIds as $index => $deleteId) {
                $stmt->bindValue($index + 1, $deleteId, PDO::PARAM_INT);
            }
            $stmt->execute();
        }

        // 更新後、リダイレクトして一覧を表示
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "更新エラー: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント管理</title>
    <script>
        // 新しい候補を追加する関数
        document.addEventListener("DOMContentLoaded", function() {
            const addButton = document.getElementById('addNewAvailability');
            const availabilityTableBody = document.querySelector('table tbody');

            addButton.addEventListener('click', function() {
                // 新しい行を作成
                const newRow = document.createElement('tr');

                // 新しい行の内容
                newRow.innerHTML = `
                    <td>新しい日程</td>
                    <td>
                        <input type="datetime-local" name="start_time[]" required>
                    </td>
                    <td>
                        <input type="datetime-local" name="end_time[]" required>
                    </td>
                    <td>
                        <input type="checkbox" name="delete_id[]">
                        <input type="hidden" name="availability_id[]" value="">
                    </td>
                `;
                
                // 新しい行をテーブルに追加
                availabilityTableBody.appendChild(newRow);
            });
        });
    </script>
</head>
<body>
    <h1>イベント編集画面:</h1>

    <form method="POST" style="display:inline;">
        <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
    </form>
    
    <form method="POST">
        <table border="1">
            <thead>
                <tr>
                    <th>日程</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($availabilities as $availability) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($availability['start_time']) . ' - ' . htmlspecialchars($availability['end_time']); ?></td>
                        <td>
                            <input type="datetime-local" name="start_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($availability['start_time'])); ?>" required>
                        </td>
                        <td>
                            <input type="datetime-local" name="end_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($availability['end_time'])); ?>" required>
                        </td>
                        <td>
                            <input type="checkbox" name="delete_id[]" value="<?php echo $availability['id']; ?>">
                            <input type="hidden" name="availability_id[]" value="<?php echo $availability['id']; ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="button" id="addNewAvailability">新しい候補を追加</button>
        <br><br>
        <button type="submit">更新</button>
    </form>
    <a href="index.php">イベント一覧に戻る</a>
</body>
</html>
