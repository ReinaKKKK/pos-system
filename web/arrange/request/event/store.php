<?php

/**
 * 編集パスワードが設定されていない場合、エラーメッセージを表示する関数
 *
 * この関数は、編集パスワードが空であるかどうかを確認し、設定されていない場合はエラーメッセージを返します。
 *
 * @param string $password 編集パスワード
 * @return string 編集パスワードが設定されていない場合、エラーメッセージを返します。設定されている場合は空文字列を返します。
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
 * @param string $name 検証対象の文字列
 * @return boolean 空であればtrue、それ以外はfalse
 */
function validate($name)
{
    // $nameが配列の場合、その中の "name" に対して処理を行う
    if (is_array($name)) {
        if (isset($name['name'])) {
            return trim($name['name']) === ''; // "name" フィールドの値をチェック
        }
        // "name" フィールドが存在しない場合、false を返す（必要に応じて処理を追加）
        return false;
    }

    // 文字列の場合
    return trim($name) === '';
}

/**
 * 編集パスワードのバリデーションを行う関数
 * @param string $password 編集パスワード
 * @return boolean 編集パスワードが空ならtrue、それ以外はfalse
 */
function isEditPasswordSet($password)
{
    return !empty(trim($password)); // パスワードが設定されている場合にtrue
}

/**
 * 入力値が最大文字数を超えているかを検証する関数
 * @param string $name 検証対象の文字列
 * @return boolean 255文字を超えていればtrue、それ以外はfalse
 */
function maxLength($name)
{
    return strlen($name) > 255;
}

/**
 * イベント時刻の入力が空かどうかを検証する関数
 * @param mixed $name 検証対象の値
 * @return boolean 空であればtrue、それ以外はfalse
 */
function validationEventTimes($name)
{
    return empty($name);
}

/**
 * 開始時間と終了時間の設定が有効かどうかを検証する関数
 * @param string $startTime 開始時間
 * @param string $endTime   終了時間
 * @return boolean 開始時間が終了時間よりも後であればtrue、それ以外はfalse
 */
function isInvalidTimeRange($startTime, $endTime)
{
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);

    // Ensure both times are valid
    if ($startTime === false || $endTime === false) {
        return false; // Return false if either input is invalid
    }

    return $startTime >= $endTime; // If start time is later than or equal to end time, return true
}

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
        case 'edit_password':
            $errorMessage = '編集パスワードを設定した場合のみ、編集が可能になります。';
            break;
        default:
            $errorMessage = '正常です';
    }
    return $errorMessage;
}

/**
 * Debugエラーメッセージを生成する関数
 *
 * 指定されたエラータイプに基づいて適切なエラーメッセージを返します。
 * コンテキストを指定することで、メッセージ内に詳細を含めることができます。
 *
 * @param string $errorType エラータイプ (例: 'is_empty', 'max_length', 'time_invalid')
 * @param string $context   エラーメッセージに付加する追加情報 (例: 'イベント名')
 * @return string エラータイプに対応するエラーメッセージ
 */
function generateDebugMessage($errorType, $context = '')
{
    $messages = [
        'is_empty' => '【エラー: 必須項目】{$context} が入力されていません。',
        'max_length' => '【エラー: 入力が長すぎます】{$context} は255文字以内にしてください。',
        'time_invalid' => '【エラー: 時間設定が不正】{$context} 開始時間は終了時間よりも前に設定してください。',
        'time_format' => '【エラー: 日時形式が不正】{$context} 正しい日時形式を使用してください。',
        'edit_password' => '【エラー: 編集パスワード未設定】{$context} 編集パスワードが設定されていません。',
    ];
    return $messages[$errorType] ?? '【エラー】未知のエラーが発生しました。';
}
