<?php

//新規会員登録　登録画面でユーザー登録
//ユーザー登録した人がすでに存在しているユーザーではないか
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新規会員登録</title>
</head>
<body>
<h1>新規会員登録<h1>
  <form action="register.php" method="post">
    <div>
      <label>
        名前:
        <input type="text" name="name" required>
      </label>
    </div>
    <div>
      <label>
      メールアドレス:
      <input type="text" name="email" required>
      </label>
    </div>
    <div>
      <label>
      パスワード:
      <input type="text" name="password" required>
      </label>
    </div>
    <input type="submit" value="新規登録">
  </form>
  <p><a href="login.php">すでに登録済みの方</a></p>
</body>
</html>
