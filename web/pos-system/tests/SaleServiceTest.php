<?php

/**
 * SaleService 単体テスト
 *
 * 売上登録・集計ロジックを網羅的に検証する。
 */

declare(strict_types=1);

namespace Tests\Service;

use App\Service\SaleService;    // ← 本体サービスを use で取り込む
use PHPUnit\Framework\TestCase;
use PDO;
use RuntimeException;

/**
 * Class SaleServiceTest
 *
 * SaleService の CRUD／集計メソッドをテストするクラス。
 */
class SaleServiceTest extends TestCase
{
    /** @var PDO */
    private PDO $pdo;

    /** @var SaleService */
    private SaleService $saleService;

    /**
     * 各テスト前に呼ばれるセットアップ処理。
     *
     * @return void
     */
    protected function setUp(): void
    {
        // テスト専用データベースに接続
        $this->pdo = new PDO(
            'mysql:host=mysql;dbname=possystem_test;charset=utf8',
            'root',
            ''
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // テーブル作成とサンプルデータ投入
        $this->setupDatabase();

        $this->saleService = new SaleService($this->pdo);
    }

    /**
     * テスト用スキーマ・データを生成する。
     *
     * @return void
     */
    private function setupDatabase(): void
    {
        // items
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

        // sales
        $this->pdo->exec('DROP TABLE IF EXISTS sales');
        $this->pdo->exec(
            'CREATE TABLE sales (
                id INT AUTO_INCREMENT PRIMARY KEY,
                item_id INT NOT NULL,
                quantity INT NOT NULL,
                tax INT NOT NULL,
                total_price INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (item_id) REFERENCES items(id)
            )'
        );

        // サンプル商品
        $this->pdo->exec(
            "INSERT INTO items (name, price, stock) VALUES
             ('テスト商品1', 1000, 10),
             ('テスト商品2', 2000, 5)"
        );
    }

    /**
     * createSale() が正常に登録できることを検証。
     *
     * @return void
     */
    public function testCreateSale(): void
    {
        $this->assertTrue($this->saleService->createSale(1, 2));

        $sale = $this->pdo->query('SELECT * FROM sales WHERE item_id = 1')->fetch();
        $this->assertSame(2, (int) $sale['quantity']);
        $this->assertSame(200, (int) $sale['tax']);
        $this->assertSame(2200, (int) $sale['total_price']);

        $item = $this->pdo->query('SELECT stock FROM items WHERE id = 1')->fetch();
        $this->assertSame(8, (int) $item['stock']);
    }

    /**
     * 在庫不足時に例外が投げられることを検証。
     *
     * @return void
     */
    public function testCreateSaleWithInsufficientStock(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('在庫が不足しています。');
        $this->saleService->createSale(2, 6); // 在庫 5 に対し 6 売上
    }

    /**
     * getAllSales() で正しい件数が返ることを検証。
     *
     * @return void
     */
    public function testGetAllSales(): void
    {
        $this->saleService->createSale(1, 2);
        $this->saleService->createSale(2, 1);

        $sales = $this->saleService->getAllSales();
        $this->assertCount(2, $sales);

        // レコード内容の確認
        $this->assertSame(1, (int) $sales[1]['item_id']);
        $this->assertSame(2, (int) $sales[1]['quantity']);
    }

    /**
     * 全売上集計メソッドの検証。
     *
     * @return void
     */
    public function testGetSalesSummary(): void
    {
        $this->saleService->createSale(1, 2); // 2200
        $this->saleService->createSale(2, 1); // 2200

        $summary = $this->saleService->getSalesSummary();
        $this->assertSame(4400, (int) $summary['total_amount']);
        $this->assertSame(400, (int) $summary['total_tax']);
        $this->assertSame(2, (int) $summary['total_transactions']);
    }

    /**
     * 商品別売上集計メソッドの検証。
     *
     * @return void
     */
    public function testGetItemSalesSummary(): void
    {
        $this->saleService->createSale(1, 2);
        $this->saleService->createSale(1, 1);
        $this->saleService->createSale(2, 1);

        $summary = $this->saleService->getItemSalesSummary();
        $this->assertSame(3, (int) $summary[0]['total_quantity']);
    }

    /**
     * テスト後のクリーンアップ。
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS sales');
        $this->pdo->exec('DROP TABLE IF EXISTS items');
        $this->pdo = null;
    }
}
