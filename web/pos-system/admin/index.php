<?php

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require '../db.php';


$items = $pdo->query("SELECT * FROM items")->fetchAll();
?>

<h2>商品一覧</h2>
<link rel="stylesheet" type="text/css" href="styles.css">
<a href="create.php">新規商品追加</a> | 
<a href="pos.php">POSレジ</a> | 
<a href="logout.php">ログアウト</a>

<table border="1">
<tr><th>ID</th><th>名前</th><th>価格</th></tr>
<?php foreach ($items as $item) : ?>
<tr>
    <td><?= htmlspecialchars($item['id']) ?></td>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td><?= htmlspecialchars($item['price']) ?>円</td>
</tr>
<?php endforeach; ?>
</table>
