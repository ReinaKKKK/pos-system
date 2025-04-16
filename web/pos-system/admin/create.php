<?php

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['price']]);
    header("Location: index.php");
    exit();
}
?>

<h2>商品追加</h2>
<link rel="stylesheet" type="text/css" href="styles.css">
<form method="POST">
    商品名: <input type="text" name="name" required><br>
    価格: <input type="number" name="price" required><br>
    <button type="submit">登録</button>
</form>
<div class="back-link">
    <a href="index.php">← 商品一覧に戻る</a>
</div>
