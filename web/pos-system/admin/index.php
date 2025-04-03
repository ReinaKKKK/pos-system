<?php
require_once '../db.php';

// 商品データを取得
$sql = "SELECT id, name, price, stock FROM products";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <h1>商品一覧</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>商品名</th>
            <th>価格（円）</th>
            <th>在庫数</th>
        </tr>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?>円</td>
                <td><?= htmlspecialchars($product['stock']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
