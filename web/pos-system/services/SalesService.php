<?php

namespace App\Service;

use PDO;
use RuntimeException;
use Exception;

/**
 * Class SaleService
 *
 * 売上管理と在庫の更新を行うサービスクラスです。
 */
class SaleService
{
    /** @var PDO */
    private PDO $pdo;

    /** @var ItemService */
    private ItemService $itemService;

    private const TAX_RATE = 0.1; // 消費税率 10%

    /**
     * コンストラクタ
     *
     * @param PDO $pdo PDOオブジェクト
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->itemService = new ItemService($pdo);
    }

    /**
     * 売上一覧を取得
     *
     * @return array 売上一覧
     */
    public function getAllSales()
    {
        $stmt = $this->pdo->query('
            SELECT 
                sales.*,
                items.name as item_name,
                items.price as item_price
            FROM sales
            JOIN items ON sales.item_id = items.id
            ORDER BY sales.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    /**
     * 売上を登録
     *
     * @param integer $itemId   商品ID
     * @param integer $quantity 数量
     * @return boolean 成功した場合はtrue
     * @throws RuntimeException 在庫不足や商品が見つからない場合.
     */
    public function createSale($itemId, $quantity)
    {
        $this->pdo->beginTransaction();

        try {
            // 商品情報の取得と在庫チェック
            $stmt = $this->pdo->prepare('SELECT price, stock FROM items WHERE id = ? FOR UPDATE');
            $stmt->execute([$itemId]);
            $item = $stmt->fetch();

            if (!$item) {
                throw new RuntimeException('商品が見つかりません。');
            }

            if ($item['stock'] < $quantity) {
                throw new RuntimeException('在庫が不足しています。');
            }

            // 金額計算
            $price = $item['price'];
            $subtotal = $price * $quantity;
            $tax = floor($subtotal * self::TAX_RATE);
            $total = $subtotal + $tax;

            // 売上データの登録
            $stmt = $this->pdo->prepare('
                INSERT INTO sales (item_id, quantity, tax, total_price, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ');
            $stmt->execute([$itemId, $quantity, $tax, $total]);

            // 在庫数の更新
            $this->itemService->updateStock($itemId, -$quantity);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * 売上集計を取得
     *
     * @param string $startDate 開始日（Y-m-d）
     * @param string $endDate   終了日（Y-m-d）
     * @return array 集計結果
     */
    public function getSalesSummary($startDate = null, $endDate = null)
    {
        $sql = '
            SELECT 
                SUM(total_price) as total_amount,
                SUM(tax) as total_tax,
                COUNT(*) as total_transactions
            FROM sales
            WHERE 1=1
        ';
        $params = [];

        if ($startDate) {
            $sql .= ' AND DATE(created_at) >= ?';
            $params[] = $startDate;
        }
        if ($endDate) {
            $sql .= ' AND DATE(created_at) <= ?';
            $params[] = $endDate;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * 商品別売上集計を取得
     *
     * @param string $startDate 開始日（Y-m-d）
     * @param string $endDate   終了日（Y-m-d）
     * @return array 商品別集計結果
     */
    public function getItemSalesSummary($startDate = null, $endDate = null)
    {
        $sql = '
            SELECT 
                items.id,
                items.name,
                SUM(sales.quantity) as total_quantity,
                SUM(sales.total_price) as total_amount,
                SUM(sales.tax) as total_tax
            FROM sales
            JOIN items ON sales.item_id = items.id
            WHERE 1=1
        ';
        $params = [];

        if ($startDate) {
            $sql .= ' AND DATE(sales.created_at) >= ?';
            $params[] = $startDate;
        }
        if ($endDate) {
            $sql .= ' AND DATE(sales.created_at) <= ?';
            $params[] = $endDate;
        }

        $sql .= ' GROUP BY items.id, items.name ORDER BY total_amount DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
