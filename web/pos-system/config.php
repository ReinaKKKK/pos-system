<?php

/**
 * POS システム共通設定ファイル
 *
 * DB 接続とセッションヘルパ。
 */

declare(strict_types=1);

/* ---------- セッション ---------- */
session_start();

// スーパグローバルをラップ
$session = &$_SESSION;

/* ---------- DB 接続 ---------- */
const DB_HOST    = 'mysql';
const DB_NAME    = 'possystem';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8';

try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );
    $pdo = new \PDO($dsn, DB_USER, DB_PASS, [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    exit('DB 接続エラー: ' . $e->getMessage());
}

/* ---------- 以下ヘルパ関数 (略) ---------- */
