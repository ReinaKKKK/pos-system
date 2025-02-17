<?php

// データベース接続
require_once 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM arrange WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
