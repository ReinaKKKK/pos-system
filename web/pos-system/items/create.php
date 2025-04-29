<?php
require_once '../config.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validateCsrfToken($_POST['csrf_token']);

        // 入力値の検証
        $name = trim($_POST['name']);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_INT);
        $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);

        if (empty($name)) {
            throw new RuntimeException('商品名を入力してください。');
        }
        if ($price === false || $price < 0) {
            throw new RuntimeException('有効な価格を入力してください。');
        }
        if ($stock === false || $stock < 0) {
            throw new RuntimeException('有効な在庫数を入力してください。');
        }

        // データベースに登録
        $stmt = $pdo->prepare('INSERT INTO items (name, price, stock) VALUES (?, ?, ?)');
        $stmt->execute([$name, $price, $stock]);

        setFlashMessage('商品を登録しました。');
        header('Location: list.php');
        exit;
    } catch (Exception $e) {
        setFlashMessage('エラー: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録 - 簡易POSシステム</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>商品登録</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">トップ</a></li>
                    <li><a href="list.php">商品一覧</a></li>
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
                    <label for="name">商品名</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="price">価格（税抜）</label>
                    <input type="number" id="price" name="price" required min="0"
                           value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="stock">在庫数</label>
                    <input type="number" id="stock" name="stock" required min="0"
                           value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>">
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
