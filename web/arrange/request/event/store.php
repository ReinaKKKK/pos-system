<?php

/**
 * 編集パスワードが設定されていない場合、エラーメッセージを表示する関数
 *
 * @param string $password 編集パスワード
 * @return string エラーメッセージまたは空文字列
 */
function validateEditPassword($password)
{
    if (empty(trim($password))) {
        return createErrorMessage('edit_password', '編集パスワード');
    }
    return ''; // エラーなし
}

/**
 * 入力値が空かどうかを検証する関数
 *
 * @param string|array $name 検証対象の文字列または配列
 * @return boolean 空であればtrue、それ以外はfalse
 */
function validate($name)
{
    if (is_array($name) && isset($name['name'])) {
        return trim($name['name']) === '';
    }
    return trim($name) === '';
}

/**
 * 編集パスワードの空かバリデーションを行う関数
 *
 * @param string $password 編集パスワード
 * @return boolean 編集パスワードが空ならfalse、それ以外はtrue
 */
function isEditPasswordSet($password)
{
    return !empty(trim($password));
}
/**
 * 入力値が最大文字数を超えているかを検証する関数
 *
 * @param string $name 検証対象の文字列
 * @return boolean 255文字を超えていればtrue、それ以外はfalse
 */
function maxLength($name)
{
    return strlen($name) > 255;
}

/**
 * 開始時間と終了時間の設定が有効かどうかを検証する関数
 *
 * @param string $startTime 開始時間
 * @param string $endTime   終了時間
 * @return boolean 開始時間が終了時間よりも後であればtrue、それ以外はfalse
 */
function isInvalidTimeRange($startTime, $endTime)
{
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);

    if ($startTime === false || $endTime === false) {
        return false; // 無効な日時
    }

    return $startTime >= $endTime;
}

/**
 * エラータイプに応じたエラーメッセージを生成する関数
 *
 * @param string $errorType エラータイプ
 * @param string $str       エラーメッセージに使用する文字列
 * @return string 生成されたエラーメッセージ
 */
function createErrorMessage($errorType, $str)
{
    $messages = [
        'is_empty' => $str . 'は必須です。',
        'max_length' => $str . 'は255文字以下にしてください。',
        'is_empty_time' => $str . 'の時刻は必須です。',
        'time_setting' => $str . 'の時間設定が正しくありません。開始時間よりも後の終了時間にしてください。',
        'edit_password' => 'パスワードが間違っています。設定した正しいものを入れてください。',
        'participant_password' => 'パスワードが間違っています。設定した正しいものを入れてください。',
    ];

    return $messages[$errorType] ?? '不明なエラーが発生しました。';
}

/**
 * Debugエラーメッセージを生成する関数
 *
 * @param string $errorType エラータイプ
 * @param string $context   コンテキスト情報
 * @return string エラータイプに対応するエラーメッセージ
 */
function generateDebugMessage($errorType, $context = '')
{
    $messages = [
        'is_empty' => "【エラー: 必須項目】{$context} が入力されていません。",
        'max_length' => "【エラー: 入力が長すぎます】{$context} は255文字以内にしてください。",
        'time_invalid' => "【エラー: 時間設定が不正】{$context} 開始時間は終了時間よりも前に設定してください。",
        'time_format' => "【エラー: 日時形式が不正】{$context} 正しい日時形式を使用してください。",
        'edit_password' => "【エラー: 編集パスワード未設定】{$context} 編集パスワードが設定されていません。",
    ];

    return $messages[$errorType] ?? '【エラー】未知のエラーが発生しました。';
}
