<?php
session_start();

// ログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// 商品情報をデータベースから取得
$sql = "SELECT * FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
</head>
<body>
<h1>商品一覧</h1>

<?php if ($products) : ?>
    <table border="1">
        <thead>
            <tr>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>更新日時</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?>円</td>
                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                    <td><?php echo htmlspecialchars($product['updated_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>商品が登録されていません。</p>
<?php endif; ?>

<p><a href="logout.php">ログアウト</a></p>
</body>
</html>
