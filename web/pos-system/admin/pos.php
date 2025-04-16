<?php

require '../db.php';

// POSTデータが送信された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームから送信された商品IDと数量を取得
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // 商品の価格をデータベースから取得
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = :id");
    $stmt->execute(['id' => $item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    // 商品が見つからない場合はエラーメッセージを設定
    if ($item === false) {
        $error = "商品が見つかりませんでした。";
    } else {
        // 商品が見つかった場合、計算を行う
        $price = $item['price'];  // 税抜き価格
        $total_price = $price * $quantity;  // 総額を計算

        // 税金計算（例: 10%）
        $tax = $total_price * 0.10;
        $total_price_with_tax = $total_price + $tax;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSシステム</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>POSシステム</h1>

    <!-- 商品選択フォーム -->
    <form method="POST" action="pos.php" class="form-container">
        <label for="item">商品:</label>
        <select name="item_id" id="item" required>
            <?php
            // 商品情報をデータベースから取得して表示
            $stmt = $pdo->query("SELECT * FROM items");
            while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $item['id'] . "'>" . $item['name'] . " - ¥" . number_format($item['price'], 2) . "</option>";
            }
            ?>
        </select>

        <label for="quantity">数量:</label>
        <input type="number" name="quantity" id="quantity" min="1" required>

        <button type="submit">計算</button>
    </form>

    <!-- エラーメッセージ -->
    <?php if (isset($error)) : ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- 計算結果表示 -->
    <?php if (isset($total_price)) : ?>
        <div class="result">
            <p><strong>商品名:</strong> <?php echo htmlspecialchars($item['name']); ?></p>
            <p><strong>数量:</strong> <?php echo htmlspecialchars($quantity); ?></p>
            <p><strong>税抜き価格:</strong> ¥<?php echo number_format($total_price); ?></p>
            <p><strong>消費税（10%）:</strong> ¥<?php echo number_format($tax); ?></p>
            <p><strong>税込み価格:</strong> ¥<?php echo number_format($total_price_with_tax); ?></p>
        </div>
    <?php endif; ?>
</div>
<div class="back-link">
    <a href="index.php">← 商品一覧に戻る</a>
</div>

</body>
</html>
