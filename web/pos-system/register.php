<?php
require_once 'config.php';
require_once 'helpers.php';

// POST 処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validateCsrfToken($_POST['csrf_token']);

        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $pass     = $_POST['password'];
        $pass2    = $_POST['password_confirm'];

        if (!$username || !$email || !$pass || $pass !== $pass2) {
            throw new RuntimeException('入力に不備があります。');
        }

        // パスワードをハッシュ化
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // ユーザー登録
        $stmt = $pdo->prepare(
          'INSERT INTO users (username,email,password) VALUES (?,?,?)'
        );
        $stmt->execute([$username, $email, $hash]);

        setFlashMessage('登録が完了しました。ログインしてください。');
        header('Location: auth.php');
        exit;
    } catch (Exception $e) {
        setFlashMessage('エラー: ' . $e->getMessage());
    }
}

$token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>新規登録 - POSシステム</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <div class="container">
    <h1>新規ユーザー登録</h1>
    <?php if ($msg = getFlashMessage()) : ?>
      <div class="flash-message"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
      <div class="form-group">
        <label>ユーザー名</label>
        <input name="username" required>
      </div>
      <div class="form-group">
        <label>メールアドレス</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>パスワード</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>パスワード（再入力）</label>
        <input type="password" name="password_confirm" required>
      </div>
      <button class="button" type="submit">登録</button>
    </form>
    <p><a href="auth.php">ログイン画面に戻る</a></p>
  </div>
</body>
</html>
