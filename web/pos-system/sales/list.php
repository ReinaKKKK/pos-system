<?php
require_once '../config.php';

try {
    // 売上情報を商品名と共に取得
    $stmt = $pdo->query('
        SELECT 
            sales.*,
            items.name as item_name,
            items.price as item_price
        FROM sales
        JOIN items ON sales.item_id = items.id
        ORDER BY sales.created_at DESC
    ');
    $sales = $stmt->fetchAll();

    // 合計金額の計算
    $total_amount = array_sum(array_column($sales, 'total_price'));
    $total_tax = array_sum(array_column($sales, 'tax'));
} catch (PDOException $e) {
    setFlashMessage('売上情報の取得に失敗しました: ' . $e->getMessage());
    $sales = [];
    $total_amount = 0;
    $total_tax = 0;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>売上一覧 - 簡易POSシステム</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>売上一覧</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">トップ</a></li>
                    <li><a href="create.php">売上登録</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <?php if ($flashMessage = getFlashMessage()) : ?>
                <div class="flash-message">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <div class="action-links">
                <a href="create.php" class="button">新規売上登録</a>
            </div>

            <div class="summary-box">
                <div class="summary-item">
                    <h3>売上合計（税込）</h3>
                    <p class="amount"><?php echo number_format($total_amount); ?>円</p>
                </div>
                <div class="summary-item">
                    <h3>消費税合計</h3>
                    <p class="amount"><?php echo number_format($total_tax); ?>円</p>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>日時</th>
                        <th>商品名</th>
                        <th>単価（税抜）</th>
                        <th>数量</th>
                        <th>消費税</th>
                        <th>合計（税込）</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)) : ?>
                        <tr>
                            <td colspan="6">売上記録がありません。</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($sales as $sale) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('Y/m/d H:i', strtotime($sale['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($sale['item_name']); ?></td>
                                <td class="number"><?php echo number_format($sale['item_price']); ?>円</td>
                                <td class="number"><?php echo number_format($sale['quantity']); ?></td>
                                <td class="number"><?php echo number_format($sale['tax']); ?>円</td>
                                <td class="number"><?php echo number_format($sale['total_price']); ?>円</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> 簡易POSシステム</p>
        </footer>
    </div>
</body>
</html> 
