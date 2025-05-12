<?php
require_once '../config.php';
require_once '../helpers.php';

// 商品一覧の取得
try {
    $stmt = $pdo->query('SELECT id, name, price, stock FROM items ORDER BY name');
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlashMessage('商品情報の取得に失敗しました: ' . $e->getMessage());
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validateCsrfToken($_POST['csrf_token']);

        // 入力値の検証
        $item_id = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

        if (!$item_id) {
            throw new RuntimeException('商品を選択してください。');
        }
        if ($quantity === false || $quantity <= 0) {
            throw new RuntimeException('有効な数量を入力してください。');
        }

        // トランザクション開始
        $pdo->beginTransaction();

        // 商品情報の取得
        $stmt = $pdo->prepare('SELECT price, stock FROM items WHERE id = ? FOR UPDATE');
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();

        if (!$item) {
            throw new RuntimeException('選択された商品が見つかりません。');
        }

        // 在庫チェック
        if ($item['stock'] < $quantity) {
            throw new RuntimeException('在庫が不足しています。');
        }

        // 金額計算
        $price = $item['price'];
        $subtotal = $price * $quantity;
        $tax = floor($subtotal * 0.1);  // 消費税10%
        $total = $subtotal + $tax;

        // 売上データの登録
        $stmt = $pdo->prepare('
            INSERT INTO sales (item_id, quantity, tax, total_price, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$item_id, $quantity, $tax, $total]);

        // 在庫数の更新
        $stmt = $pdo->prepare('UPDATE items SET stock = stock - ? WHERE id = ?');
        $stmt->execute([$quantity, $item_id]);

        $pdo->commit();

        setFlashMessage('売上を追加しました');
        header('Location: list.php');
        exit;
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        setFlashMessage('エラー: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>売上登録 - 簡易POSシステム</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script>
        function calculateTotal() {
            const itemSelect = document.getElementById('item_id');
            const quantityInput = document.getElementById('quantity');
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            
            if (selectedOption.value && quantityInput.value > 0) {
                const price = parseInt(selectedOption.dataset.price);
                const quantity = parseInt(quantityInput.value);
                const subtotal = price * quantity;
                const tax = Math.floor(subtotal * 0.1);
                const total = subtotal + tax;

                document.getElementById('subtotal').textContent = subtotal.toLocaleString() + '円';
                document.getElementById('tax').textContent = tax.toLocaleString() + '円';
                document.getElementById('total').textContent = total.toLocaleString() + '円';
            } else {
                document.getElementById('subtotal').textContent = '0円';
                document.getElementById('tax').textContent = '0円';
                document.getElementById('total').textContent = '0円';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h1>売上登録</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">トップ</a></li>
                    <li><a href="list.php">売上一覧</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <?php if ($flashMessage = getFlashMessage()) : ?>
                <div class="flash-message">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="form-group">
                    <label for="item_id">商品</label>
                    <select id="item_id" name="item_id" required onchange="calculateTotal()">
                        <option value="">選択してください</option>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?php echo $item['id']; ?>"
                                    data-price="<?php echo $item['price']; ?>"
                                    <?php echo isset($_POST['item_id']) && $_POST['item_id'] == $item['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($item['name']); ?>
                                (<?php echo number_format($item['price']); ?>円)
                                - 在庫: <?php echo number_format($item['stock']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">数量</label>
                    <input type="number" id="quantity" name="quantity" required min="1"
                           value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '1'; ?>"
                           onchange="calculateTotal()" onkeyup="calculateTotal()">
                </div>

                <div class="calculation-box">
                    <div class="calc-item">
                        <label>小計（税抜）:</label>
                        <span id="subtotal">0円</span>
                    </div>
                    <div class="calc-item">
                        <label>消費税（10%）:</label>
                        <span id="tax">0円</span>
                    </div>
                    <div class="calc-item total">
                        <label>合計（税込）:</label>
                        <span id="total">0円</span>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="button">登録</button>
                    <a href="list.php" class="button">キャンセル</a>
                </div>
            </form>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> 簡易POSシステム</p>
        </footer>
    </div>
</body>
</html> 
