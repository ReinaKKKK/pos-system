<?php
require_once '../config.php';

// 商品IDの取得と検証
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    setFlashMessage('無効な商品IDです。');
    header('Location: list.php');
    exit;
}

try {
    // 商品情報の取得
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        setFlashMessage('指定された商品が見つかりません。');
        header('Location: list.php');
        exit;
    }
} catch (PDOException $e) {
    setFlashMessage('商品情報の取得に失敗しました: ' . $e->getMessage());
    header('Location: list.php');
    exit;
}

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

        // データベースの更新
        $stmt = $pdo->prepare('UPDATE items SET name = ?, price = ?, stock = ? WHERE id = ?');
        $stmt->execute([$name, $price, $stock, $id]);

        setFlashMessage('商品情報を更新しました。');
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
    <title>商品編集 - 簡易POSシステム</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>商品編集</h1>
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
                           value="<?php echo htmlspecialchars($item['name']); ?>">
                </div>

                <div class="form-group">
                    <label for="price">価格（税抜）</label>
                    <input type="number" id="price" name="price" required min="0"
                           value="<?php echo htmlspecialchars($item['price']); ?>">
                </div>

                <div class="form-group">
                    <label for="stock">在庫数</label>
                    <input type="number" id="stock" name="stock" required min="0"
                           value="<?php echo htmlspecialchars($item['stock']); ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="button">更新</button>
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
