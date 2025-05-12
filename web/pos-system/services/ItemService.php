<?php

namespace App\Service;

use PHPUnit\Framework\TestCase;
use App\Service\ItemService;
use PDO;
use RuntimeException;

/**
 * 商品マスターの CRUD と在庫管理を行うサービスクラスです。
 */
class ItemService
{
    /**
     * PDO 接続オブジェクト
     *
     * @var PDO
     */
    private PDO $pdo;

    /**
     * コンストラクタ
     *
     * @param PDO $pdo PDO 接続オブジェクト。
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 商品一覧を取得する
     *
     * @return array 商品一覧の配列。
     */
    public function getAllItems(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM items ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    /**
     * 指定IDの商品を取得する
     *
     * @param  integer $id 商品ID。
     * @return array|false      商品情報または false。
     */
    public function getItem(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM items WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * 新しい商品を作成する
     *
     * @param  string  $name  商品名。
     * @param  integer $price 価格（税抜）。
     * @param  integer $stock 在庫数。
     * @return boolean           成功した場合は true。
     */
    public function createItem(string $name, int $price, int $stock): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO items (name, price, stock) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$name, $price, $stock]);
    }

    /**
     * 指定IDの商品を更新する
     *
     * @param  integer $id    商品ID。
     * @param  string  $name  商品名。
     * @param  integer $price 価格（税抜）。
     * @param  integer $stock 在庫数。
     * @return boolean           成功した場合は true。
     */
    public function updateItem(int $id, string $name, int $price, int $stock): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE items SET name = ?, price = ?, stock = ? WHERE id = ?'
        );
        return $stmt->execute([$name, $price, $stock, $id]);
    }

    /**
     * 指定IDの商品を削除する
     *
     * @param  integer $id 商品ID。
     * @return boolean       成功した場合は true。
     */
    public function deleteItem(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM items WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * 在庫数を更新する
     *
     * @param  integer $id       商品ID。
     * @param  integer $quantity 変更数（減少は負数）。
     * @return boolean             成功した場合は true。
     * @throws RuntimeException 在庫が不足している場合に例外を投げます.
     */
    public function updateStock(int $id, int $quantity): bool
    {
        // 「自分で」トランザクションを張る必要があるか調べる
        $ownTx = !$this->pdo->inTransaction();

        if ($ownTx) {
            $this->pdo->beginTransaction();
        }

        try {
            $stmt = $this->pdo->prepare('SELECT stock FROM items WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if (!$item) {
                throw new RuntimeException('商品が見つかりません。');
            }

            $newStock = $item['stock'] + $quantity;
            if ($newStock < 0) {
                throw new RuntimeException('在庫が不足しています。');
            }

            $stmt = $this->pdo->prepare('UPDATE items SET stock = ? WHERE id = ?');
            $result = $stmt->execute([$newStock, $id]);

            if ($ownTx) {
                $this->pdo->commit();
            }
            return $result;
        } catch (\Exception $e) {
            if ($ownTx) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
