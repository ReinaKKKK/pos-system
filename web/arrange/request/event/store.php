<?php

/**
 * 入力値が空かどうかを検証する関数
 * @param string $val 検証対象の文字列
 * @return boolean 空であればtrue、それ以外はfalse
 */
function validate($val)
{
    return trim($val) === '';
}

/**
 * 入力値が最大文字数を超えているかを検証する関数
 * @param string $val 検証対象の文字列
 * @return boolean 255文字を超えていればtrue、それ以外はfalse
 */
function maxLength($val)
{
    return strlen($val) > 255;
}

/**
 * イベント時刻の入力が空かどうかを検証する関数
 * @param mixed $val 検証対象の値
 * @return boolean 空であればtrue、それ以外はfalse
 */
function validationEventTimes($val)
{
    return empty($val);
}

/**
 * 開始時間と終了時間の設定が有効かどうかを検証する関数
 * @param string $startTime 開始時間
 * @param string $endTime   終了時間
 * @return boolean 開始時間が終了時間よりも後であればtrue、それ以外はfalse
 */
function Timesetting($startTime, $endTime)
{
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);

    // Ensure both times are valid
    if ($startTime === false || $endTime === false) {
        return false; // Return false if either input is invalid
    }

    return $startTime >= $endTime; // If start time is later than or equal to end time, return true
}
var_dump($startTime);
var_dump($endTime);

/**
 * エラータイプに応じたエラーメッセージを生成する関数
 * @param string $errorType エラータイプ
 * @param string $str       エラーメッセージに使用する文字列
 * @return string 生成されたエラーメッセージ
 */
function createErrorMessage($errorType, $str)
{
    switch ($errorType) {
        case 'is_empty':
            $errorMessage = $str . 'は必須です。';
            break;
        case 'max_length':
            $errorMessage = $str . 'は255文字以下にしてください。';
            break;
        case 'is_empty_time':
            $errorMessage = $str . 'の時刻は必須です。';
            break;
        case 'time_setting':
            $errorMessage = $str . 'の時間設定が正しくありません。開始時間よりも後の終了時間にしてください';
            break;
        default:
            $errorMessage = '正常です';
    }
    return $errorMessage;
}

// エラー判定
$errorType = '';
$name = 'テストイベント名'; // 例として設定
$startTime = '2024-01-01 10:00:00';
$endTime = '2024-01-01 09:00:00';

// 入力検証
if (validate($name)) {
    $errorType = 'is_empty';
} elseif (maxLength($name)) {
    $errorType = 'max_length';
} elseif (Timesetting($startTime, $endTime)) {
    $errorType = 'time_setting';
}

// エラーメッセージの作成
$errors = [];
$errors['name'] = createErrorMessage($errorType, 'イベント');

// 結果出力
print_r($errors);
