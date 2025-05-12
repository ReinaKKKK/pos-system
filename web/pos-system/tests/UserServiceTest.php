<?php

namespace PosSystem\Tests\Services;

use PHPUnit\Framework\TestCase;
use PosSystem\Services\UserService;

/**
 * @covers \PosSystem\Services\UserService
 */
class UserServiceTest extends TestCase
{
    /** @var \PDO */
    private $pdo;

    /** @var UserService */
    private $service;

    /**
     * セットアップ処理
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->pdo = new \PDO(
            'mysql:host=mysql;dbname=possystem_test;charset=utf8',
            'root',
            ''
        );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // テスト用 users テーブルを初期化
        $this->pdo->exec('DROP TABLE IF EXISTS users');

        // CREATE TABLE の SQL は変数に入れて一行の引数で渡す
        $createTableSql = <<<'SQL'
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
SQL;
        $this->pdo->exec($createTableSql);

        $this->service = new UserService($this->pdo);
    }

    /**
     * ユーザー作成が成功すること
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $ok = $this->service->createUser(
            'user1',
            password_hash('secret1', PASSWORD_DEFAULT)
        );
        $this->assertTrue($ok);

        $user = $this->service->getUserByUsername('user1');
        $this->assertEquals('user1', $user['username']);
    }

    /**
     * 重複ユーザー名で例外が投げられること
     *
     * @return void
     */
    public function testDuplicateUserThrows(): void
    {
        $this->service->createUser(
            'dup',
            password_hash('pass', PASSWORD_DEFAULT)
        );
        $this->expectException(\RuntimeException::class);
        $this->service->createUser(
            'dup',
            password_hash('pass', PASSWORD_DEFAULT)
        );
    }

    /**
     * クリーンアップ
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}
