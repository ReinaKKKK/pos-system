<?php

require_once '../db.php';

// 新規会員登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送られたデータを受け取る
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // メールアドレスが既に登録されているか確認
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $member = $stmt->fetch();

    if ($member) {
        // メールアドレスが既に存在する場合
        echo "<p>同じメールアドレスが既に登録されています。</p>";
    } else {
        // パスワードをハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ユーザーをデータベースに登録
        $sql = "INSERT INTO users (name, email, password, created_at, updated_at) 
                VALUES (:name, :email, :password, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);  // ここも修正
        $stmt->bindValue(':name', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashed_password);
        if ($stmt->execute()) {
            echo "<p>新規会員登録が完了しました！</p>";
        } else {
            echo "<p>登録に失敗しました。</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新規会員登録</title>
</head>
<body>
<h1>新規会員登録</h1>
  <form action="signup.php" method="POST">
    <div>
      <label for="username">
        名前:
        <input type="text" name="username" id="username" required>
      </label>
    </div>
    <div>
      <label for="email">
      メールアドレス:
      <input type="email" name="email" id="email" required>
      </label>
    </div>
    <div>
      <label for="password">
      パスワード:
      <input type="password" name="password" id="password" required>
      </label>
    </div>
    <input type="submit" value="新規登録">
  </form>
  <p><a href="login.php">すでに登録済みの方</a></p>
</body>
</html>
