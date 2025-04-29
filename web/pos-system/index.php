<?php

require_once 'config.php';
require_once 'helpers.php';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>簡易POSシステム</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>簡易POSシステム</h1>
            <nav>
                <ul>
                    <li><a href="items/list.php">商品管理</a></li>
                    <li><a href="items/create.php">商品登録</a></li>
                    <li><a href="sales/list.php">売上一覧</a></li>
                    <li><a href="sales/create.php">売上登録</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <?php if ($flashMessage = getFlashMessage()) : ?>
                <div class="flash-message">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard">
                <h2>ダッシュボード</h2>
                <div class="dashboard-grid">
                    <div class="dashboard-item">
                        <h3>商品管理</h3>
                        <p><a href="items/list.php" class="button">商品一覧を表示</a></p>
                        <p><a href="items/create.php" class="button">新規商品登録</a></p>
                    </div>
                    <div class="dashboard-item">
                        <h3>売上管理</h3>
                        <p><a href="sales/list.php" class="button">売上一覧を表示</a></p>
                        <p><a href="sales/create.php" class="button">売上登録</a></p>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> 簡易POSシステム</p>
        </footer>
    </div>
</body>
</html> 
