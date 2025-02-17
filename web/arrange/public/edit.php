<?php
// データベース接続
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("UPDATE arrange SET title = ?, description = ?, date = ? WHERE id = ?");
    $stmt->execute([$title, $description, $date, $id]);

    header("Location: index.php");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM arrange WHERE id = ?");
$stmt->execute([$id]);
$schedule = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予定編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>予定を編集する</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
        <label for="title">タイトル</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($schedule['title']); ?>" required><br>
        <label for="description">詳細</label>
        <textarea name="description" required><?php echo htmlspecialchars($schedule['description']); ?></textarea><br>
        <label for="date">日付</label>
        <input type="date" name="date" value="<?php echo $schedule['date']; ?>" required><br>
        <button type="submit">更新</button>
    </form>
</body>
</html>
