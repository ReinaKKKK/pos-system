<?php

session_start();
require '../db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "ログインに失敗しました";
    }
}
?>

<h2>ログイン</h2>
<link rel="stylesheet" type="text/css" href="styles.css">
<?php if (isset($error)) { ?>
    <p style='color:red;'><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST">
    ユーザー名: <input type="text" name="username" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">ログイン</button>
</form>

<p style="margin-top: 10px;">
    アカウントをお持ちでない方は <a href="register.php">新規登録はこちら</a>
</p>
