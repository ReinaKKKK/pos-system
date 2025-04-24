<?php

declare(strict_types=1);

require_once 'config.php';

/* ---------- 認証ヘルパ ---------- */

/**
 * 現在ログインしているか判定する。
 *
 * @return boolean ログインしていれば true。
 *
 * @SuppressWarnings("Superglobals") $_SESSION ラッパーで代替。
 */
function isLoggedIn(): bool
{
    global $session;                 // config.php で宣言したラッパー
    return isset($session['user_id']);
}

/**
 * 未ログインの場合にログインページへリダイレクトする。
 *
 * @return void
 *
 * @SuppressWarnings("ExitExpression")
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlashMessage('ログインが必要です。');
        header('Location: /auth.php');
        exit; // ルール「exit禁止」を抑制するアノテーション
    }
}

/* ---------- POST アクション ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        validateCsrfToken($_POST['csrf_token']);

        if ($_POST['action'] === 'login') {
            $username = trim((string) $_POST['username']);
            $password = (string) $_POST['password'];

            if ($username === '' || $password === '') {
                throw new RuntimeException('ユーザー名とパスワードを入力してください。');
            }

            // ★実運用では DB 認証に置き換え
            if ($username === 'admin' && $password === 'password') {
                setSession('user_id', 1);
                setSession('username', $username);
                setFlashMessage('ログインしました。');
                header('Location: /index.php');
                exit;
            }
            throw new RuntimeException('ユーザー名またはパスワードが正しくありません。');
        }

        if ($_POST['action'] === 'logout') {
            session_destroy();
            setFlashMessage('ログアウトしました。');
            header('Location: /auth.php');
            exit;
        }
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
    <title>ログイン - 簡易POSシステム</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header><h1>ログイン</h1></header>

        <main>
            <?php if ($flash = getFlashMessage()) : ?>
                <div class="flash-message"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!isLoggedIn()) : ?>
                <form method="post" class="form login-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="username">ユーザー名</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">パスワード</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="button">ログイン</button>
                    </div>
                </form>
            <?php else : ?>
                <div class="logged-in-box">
                    <p>ログイン中: <?= htmlspecialchars(getSession('username'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="button">ログアウト</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>

        <footer><p>&copy; <?= date('Y'); ?> 簡易POSシステム</p></footer>
    </div>
</body>
</html>
