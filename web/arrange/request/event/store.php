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
    // 開始日と終了日のチェック
    if (empty($data['startTime']) || empty($data['endTime'])) {
        $errors[] = "開始時間と終了時間は必須です。";
    }
    // 候補日時のリストが空かどうかを確認
    if (empty($data['timeSlots'])) {
        $errors[] = "少なくとも1つの候補日時を追加してください。";
    } else {
        foreach ($data['timeSlots'] as $slot) {
            // 各候補日時が正しい形式かをチェック
            if (empty($slot['startTime']) || empty($slot['endTime'])) {
                $errors[] = "すべての候補日時に開始時間と終了時間を入力してください。";
            }
        }
    }
    return $errors;
}
