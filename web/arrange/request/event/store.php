<?php

/**
 * 編集パスワードが設定されていない場合、エラーメッセージを表示する関数
 *
 * @param string $editPassword 編集パスワード
 * @return string エラーメッセージまたは空文字列
 */
// function validateEditPassword($editPassword)
// {
//     if (empty(trim($editPassword))) {
//         return createErrorMessage('editPassword', '編集パスワード');
//     }
//     return ''; // エラーなし
// }

/**
 * 入力値が空かどうかを検証する関数
 *
 * @param array $name 検証対象の配列
 * @return boolean 空であればtrue、それ以外はfalse
 */
// function validate($name)
// {
//     if (is_array($name) && isset($name['name'])) {
//         return trim($name['name']) === '';
//     }
//     return true;
// }


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
        'max_length' => $str . 'は255文字以下にしてください。',
        'time_setting' => $str . 'の時間設定が正しくありません。開始時間よりも後の終了時間にしてください。',
        'edit_password' => 'パスワードが間違っています。設定した正しいものを入れてください。',
        'participant_password' => 'パスワードが間違っています。設定した正しいものを入れてください。',
    ];

    return $messages[$errorType] ?? '不明なエラーが発生しました。';
}
