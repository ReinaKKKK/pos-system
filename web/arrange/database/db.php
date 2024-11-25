<?php

try {
    $databaseConnection = new PDO('mysql:host=mysql; dbname=arrange; charset=utf8', 'root', '');
    $databaseConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
    exit();
}
