<?php

require_once '../config.php';
require_once '../helpers.php';

try {
    $stmt = $pdo->query('SELECT * FROM items ORDER BY id DESC');
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlashMessage('商品情報の取得に失敗しました: ' . $e->getMessage());
    $items = [];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧 - 簡易POSシステム</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>商品一覧</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">トップ</a></li>
                    <li><a href="create.php">商品登録</a></li>
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
                <a href="create.php" class="button">新規商品登録</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>商品名</th>
                        <th>価格（税抜）</th>
                        <th>在庫数</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)) : ?>
                        <tr>
                            <td colspan="5">商品が登録されていません。</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo number_format($item['price']); ?>円</td>
                                <td><?php echo number_format($item['stock']); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="button">編集</a>
                                    <form action="delete.php" method="post" class="delete-form" onsubmit="return confirm('本当に削除しますか？');">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="button delete">削除</button>
                                    </form>
                                </td>
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
