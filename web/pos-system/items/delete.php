<?php

require_once '../config.php';
require_once '../helpers.php';

// POSTリクエスト以外は許可しない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('不正なアクセスです。');
    header('Location: list.php');
    exit;
}

try {
    validateCsrfToken($_POST['csrf_token']);

    // 商品IDの検証
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if (!$id) {
        throw new RuntimeException('無効な商品IDです。');
    }

    // 商品の存在確認
    $stmt = $pdo->prepare('SELECT id FROM items WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new RuntimeException('指定された商品が見つかりません。');
    }

    // 商品の削除
    $stmt = $pdo->prepare('DELETE FROM items WHERE id = ?');
    $stmt->execute([$id]);

    setFlashMessage('商品を削除しました。');
} catch (Exception $e) {
    setFlashMessage('エラー: ' . $e->getMessage());
}

header('Location: list.php');
exit;
