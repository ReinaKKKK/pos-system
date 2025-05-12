<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

/**
 * ログイン済みか？
 *
 * @return boolean
 * @SuppressWarnings("Superglobals")
 */
function isLoggedIn(): bool
{
    global $session;
    return isset($session['user_id']);
}

/**
 * 未ログインならログイン画面へ
 *
 * @return void
 * @SuppressWarnings("HeaderLocation")
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlashMessage('ログインが必要です。');
        header('Location: /auth.php');
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        validateCsrfToken($_POST['csrf_token']);

        // --- ログイン処理 ---
        if ($_POST['action'] === 'login') {
            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                throw new RuntimeException('ユーザー名とパスワードを入力してください。');
            }

            // DBからユーザー取得
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // 認証失敗チェック
            if (!$user || !password_verify($password, $user['password'])) {
                throw new RuntimeException('ユーザー名またはパスワードが正しくありません。');
            }

            // セッションに保存
            setSession('user_id', (int)$user['id']);
            setSession('username', $user['username']);
            setFlashMessage('ログインに成功しました');

            header('Location: /pos-system/index.php');
            return;
        }

        // --- ログアウト処理 ---
        if ($_POST['action'] === 'logout') {
            session_destroy();
            setFlashMessage('ログアウトしました。');
            header('Location: /pos-system/auth.php');
            return;
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
                    <!-- ログインフォーム直後に追加 -->
                    <p>まだアカウントをお持ちでない方は
                    <a href="register.php">こちら</a> から登録してください。
                    </p>

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
