<?php

/**
 * Validates the given input data based on predefined rules.
 *
 * @param array $data Associative array containing input data to validate.
 *                    Expected keys: 'name', 'startTime', 'endTime', 'timeSlots', etc.
 *
 * @return array An array of error messages. If valid, an empty array is returned.
 */
function validate($data)
{
    $errors = [];

    // イベント名のチェック
    if (empty($data['name'])) {
        $errors[] = 'イベント名は必須です。';
    } elseif (strlen($data['name']) > 255) {
        $errors[] = 'イベント名は255文字以下で入力してください。';
    }

    // 開始時間と終了時間のチェック
    if (empty($data['startTime']) || empty($data['endTime'])) {
        $errors[] = "開始時間と終了時間は必須です。";
    } elseif (strtotime($data['startTime']) >= strtotime($data['endTime'])) {
        $errors[] = "終了時間は開始時間より後である必要があります。";
    }

    // 候補日時リストのチェック
    if (empty($data['timeSlots'])) {
        $errors[] = "少なくとも1つの候補日時を追加してください。";
    } else {
        foreach ($data['timeSlots'] as $index => $slot) {
            if (empty($slot['startTime']) || empty($slot['endTime'])) {
                $errors[] = '候補日時の{$index + 1}番目に開始時間と終了時間を入力してください。';
            } elseif (strtotime($slot['startTime']) >= strtotime($slot['endTime'])) {
                $errors[] = '候補日時の[$index + 1]番目に開始時間と終了時間を入力してください。';
            }
        }
    }

    // 重複する候補日時のチェック
    $timeSlotHashes = [];
    foreach ($data['timeSlots'] as $slot) {
        $hash = md5($slot['startTime'] . $slot['endTime']);
        if (isset($timeSlotHashes[$hash])) {
            $errors[] = "同じ候補日時（{$slot['startTime']} ～ {$slot['endTime']}）が重複しています。";
        }
        $timeSlotHashes[$hash] = true;
    }

    return $errors;
}
