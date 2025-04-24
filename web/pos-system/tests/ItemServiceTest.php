<?php

/**
 * ItemService 単体テスト
 *
 * @author YourName
 */

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\ItemService;
use PDO;
use RuntimeException;

/**
 * Class ItemServiceTest
 *
 * ItemService の CRUD・在庫更新を検証するテストクラス。
 */
class ItemServiceTest extends TestCase
{
    /** @var PDO */
    private PDO $pdo;

    /** @var ItemService */
    private ItemService $itemService;

    /**
     * 各テスト前に実行されるセットアップ。
     *
     * @return void
     */
    protected function setUp(): void
    {
        // テスト用データベース（Docker Compose 内の mysql）
        $this->pdo = new PDO(
            'mysql:host=mysql;dbname=possystem_test;charset=utf8',
            'root',
            ''
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // テーブル再生成
        $this->pdo->exec('DROP TABLE IF EXISTS items');
        $this->pdo->exec(
            'CREATE TABLE items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price INT NOT NULL,
                stock INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->itemService = new ItemService($this->pdo);
    }

    /**
     * createItem() が正常に登録できることを検証する。
     *
     * @return void
     */
    public function testCreateItem(): void
    {
        $this->assertTrue(
            $this->itemService->createItem('テスト商品', 1000, 10)
        );

        $item = $this->itemService->getItem(1);
        $this->assertSame('テスト商品', $item['name']);
        $this->assertSame(1000, (int) $item['price']);
        $this->assertSame(10, (int) $item['stock']);
    }

    /**
     * updateItem() が既存レコードを正しく更新するかを検証する。
     *
     * @return void
     */
    public function testUpdateItem(): void
    {
        $this->itemService->createItem('テスト商品', 1000, 10);

        $this->assertTrue(
            $this->itemService->updateItem(1, 'テスト商品（更新）', 2000, 20)
        );

        $item = $this->itemService->getItem(1);
        $this->assertSame('テスト商品（更新）', $item['name']);
        $this->assertSame(2000, (int) $item['price']);
        $this->assertSame(20, (int) $item['stock']);
    }

    /**
     * updateStock() が在庫増減を正しく処理することを検証する。
     *
     * @return void
     */
    public function testUpdateStock(): void
    {
        $this->itemService->createItem('テスト商品', 1000, 10);

        $this->assertTrue($this->itemService->updateStock(1, 5));
        $this->assertSame(15, (int) $this->itemService->getItem(1)['stock']);

        $this->assertTrue($this->itemService->updateStock(1, -3));
        $this->assertSame(12, (int) $this->itemService->getItem(1)['stock']);
    }

    /**
     * 在庫不足時に RuntimeException が投げられることを検証する。
     *
     * @return void
     */
    public function testUpdateStockWithInsufficientStock(): void
    {
        $this->itemService->createItem('テスト商品', 1000, 10);

        $this->expectException(RuntimeException::class);
        $this->itemService->updateStock(1, -15);
    }

    /**
     * 各テスト後のクリーンアップ処理。
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS items');
        $this->pdo = null;
    }
}
