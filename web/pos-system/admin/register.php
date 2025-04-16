<?php

require '../db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);

    header("Location: login.php");
    exit();
}
?>

<h2>ユーザー登録</h2>
<link rel="stylesheet" type="text/css" href="styles.css">
<form method="POST">
    ユーザー名: <input type="text" name="username" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">登録</button>
</form>

<div class="back-link">
    <a href="index.php">← ログイン画面に戻る</a>
</div>
