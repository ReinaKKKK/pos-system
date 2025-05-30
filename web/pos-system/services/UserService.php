<?php

declare(strict_types=1);

namespace PosSystem\Services;

use PDO;
use RuntimeException;

/**
 * ユーザー管理のビジネスロジック
 */
class UserService
{
    /** @var PDO データベース接続用 PDO オブジェクト */
    private PDO $pdo;

    /**
     * コンストラクタ
     *
     * @param PDO $pdo データベース接続用 PDO インスタンス
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 新規ユーザー登録
     *
     * @param string $username
     * @param string $password ハッシュ化済みパスワード
     * @return boolean
     * @throws RuntimeException .
     */
    public function createUser(string $username, string $password): bool
    {
        // 重複チェック
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new RuntimeException('このユーザー名はすでに使われています。');
        }

        // 登録
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, password, created_at, updated_at) VALUES (?, ?, NOW(), NOW())'
        );
        return $stmt->execute([$username, $password]);
    }

    /**
     * ユーザー取得（ログインチェック用など）
     *
     * @param string $username
     * @return array|false
     */
    public function getUserByUsername(string $username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}
