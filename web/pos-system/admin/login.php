<?php

session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを受け取る
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ユーザー情報をデータベースから取得
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    // ユーザーが存在し、パスワードが正しい場合
    if ($user && password_verify($password, $user['password'])) {
        // セッションを開始し、管理者情報を格納
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // ログイン後、在庫一覧画面にリダイレクト
        header("Location: index.php");
        exit;
    } else {
        echo "<p>メールアドレスまたはパスワードが間違っています。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
<h1>管理者ログイン</h1>
<form action="login.php" method="POST">
    <div>
        <label for="email">メールアドレス:
            <input type="email" name="email" id="email" required>
        </label>
    </div>
    <div>
        <label for="password">パスワード:
            <input type="password" name="password" id="password" required>
        </label>
    </div>
    <input type="submit" value="ログイン">
</form>
<p><a href="signup.php">新規登録はこちら</a></p>
</body>
</html>
