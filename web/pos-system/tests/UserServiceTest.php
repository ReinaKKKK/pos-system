<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../services/UserService.php';

class UserServiceTest extends TestCase
{
    private $pdo;
    private $service;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            'mysql:host=mysql;dbname=possystem_test;charset=utf8',
            'root', ''
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // テスト用 users テーブルを初期化
        $this->pdo->exec('DROP TABLE IF EXISTS users');
        $this->pdo->exec('
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $this->service = new UserService($this->pdo);
    }

    public function testCreateUser(): void
    {
        $ok = $this->service->createUser('user1', password_hash('secret1', PASSWORD_DEFAULT));
        $this->assertTrue($ok);

        $user = $this->service->getUserByUsername('user1');
        $this->assertEquals('user1', $user['username']);
    }

    public function testDuplicateUserThrows(): void
    {
        $this->service->createUser('dup', password_hash('pass', PASSWORD_DEFAULT));
        $this->expectException(RuntimeException::class);
        $this->service->createUser('dup', password_hash('pass', PASSWORD_DEFAULT));
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}
