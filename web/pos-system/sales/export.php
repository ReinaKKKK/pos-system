<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sales_' . date('Ymd') . '.csv"');

// 出力用ファイルポインタ
$fp = fopen('php://output', 'w');
// ヘッダー行（日本語列名は UTF-8 BOM を付けると Excel で文字化けしにくい）
fwrite($fp, "\xEF\xBB\xBF");
fputcsv($fp, ['日時','商品ID','商品名','数量','税額','税込価格']);

$stmt = $pdo->query(
    "SELECT s.created_at, s.item_id, i.name, s.quantity, s.tax, s.total_price
   FROM sales s
   JOIN items i ON i.id = s.item_id
   ORDER BY s.created_at DESC"
);
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($fp, $row);
}

fclose($fp);
exit;
