<?php

/**
 * 共通ヘルパ関数群
 *
 * - セッションラッパ
 * - フラッシュメッセージ
 * - CSRF トークン
 */

declare(strict_types=1);

/* ---------------- セッション系 ---------------- */
/* ---------------- セッション系 ---------------- */

/**
 * セッション値を取得する。
 *
 * @param  string $key
 * @return mixed|null 値。存在しなければ null。
 */
function getSession(string $key)
{
    global $session;
    return $session[$key] ?? null;
}

/**
 * セッション値を設定する。
 *
 * @param string $key
 * @param mixed  $value
 * @return void
 */
function setSession(string $key, $value): void
{
    global $session;
    $session[$key] = $value;
}

/**
 * セッション値を削除する。
 *
 * @param string $key
 * @return void
 */
function removeSession(string $key): void
{
    global $session;
    unset($session[$key]);
}

/* ---------------- フラッシュメッセージ ---------------- */

/**
 * フラッシュメッセージをセットする。
 *
 * @param string $msg メッセージ本文。
 * @return void
 */
function setFlashMessage(string $msg): void
{
    setSession('flash', $msg);
}

/**
 * フラッシュメッセージを取得し、1 度きりで消去する。
 *
 * @return string|null 取得したメッセージ。存在しなければ null。
 */
function getFlashMessage(): ?string
{
    $msg = getSession('flash');
    if ($msg !== null) {
        removeSession('flash');
    }
    return $msg;
}

/* ---------------- CSRF トークン ---------------- */

/**
 * CSRF トークンを生成し、セッションに保存して返す。
 *
 * @return string 生成されたトークン。
 */
function generateCsrfToken(): string
{
    $token = getSession('csrf_token');
    if ($token === null) {
        $token = bin2hex(random_bytes(32));
        setSession('csrf_token', $token);
    }
    return $token;
}

/**
 * 送られてきた CSRF トークンを検証する。
 *
 * @param  string $token クライアント送信トークン。
 * @return boolean          検証 OK なら true。
 * @throws \RuntimeException 不正トークンの場合に例外を投げます。.
 */
function validateCsrfToken(string $token): bool
{
    $stored = getSession('csrf_token');
    if ($stored === null || $token !== $stored) {
        // ここも \ を付ける
        throw new \RuntimeException('不正なリクエストです。');
    }
    return true;
}
