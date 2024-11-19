<?php

/**
 * Validates the given input data based on predefined rules.
 *
 * @param array $data Associative array containing input data to validate.
 *                    Expected keys: 'name', 'date', 'startHour24', 'endHour24', etc.
 *
 * @return array An array of error messages. If valid, an empty array is returned.
 */
function validate($data)
{
    $errors = [];

    // イベント名が空か255文字を超えている場合
    if (empty($data['name'])) {
        $errors[] = "イベント名は必須です。";
    } elseif (strlen($data['name']) > 255) {
        $errors[] = "イベント名は255文字以下で入力してください。";
    }

    // 日付と時刻のチェック
    if (empty($data['date']) || empty($data['startTimeHour']) || empty($data['endTimeHour'])) {
        $errors[] = "候補日時を入れてください。";
    }

    // 終了時刻が開始時刻より前か、無効な時間の場合
    if ($data['endHour24'] <= $data['startHour24'] || $data['endHour24'] >= 24 || $data['endHour24'] < 0) {
        $errors[] = "終了時刻が無効です。開始時刻より遅い時間を指定してください。また、終了時刻は24時間以内で入力してください。";
    }

    return $errors;
}
