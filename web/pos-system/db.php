<?php

try {
    $pdo = new PDO('mysql:host=mysql; dbname=possystem; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
    exit();
}
