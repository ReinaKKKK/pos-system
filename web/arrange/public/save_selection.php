<?php

include $_SERVER['DOCUMENT_ROOT'] . '/arrange/config/env.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/service/functions.php';
include $_SERVER['DOCUMENT_ROOT'] . '/arrange/database/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // 参加者の選択を保存
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'candidate_') === 0 && in_array($value, ['〇', '×', '△'])) {
            // 候補日のIDを取得
            $date_id = str_replace('candidate_', '', $key);

            // 表示値（〇、×、△）をデータベースに保存するために変換
            switch ($value) {
                case '〇':
                    $selection_value = 1; // 〇 → 1
                    break;
                case '×':
                    $selection_value = 2; // × → 2
                    break;
                case '△':
                    $selection_value = 3; // △ → 3
                    break;
                default:
                    $selection_value = 0; // 無効な値の場合（必要なら）
                    break;
            }

            // データベースに保存する関数（仮）で変換した値を保存
            saveParticipantSelection($event_id, $date_id, $selection_value); // 仮の関数で保存処理
        }
    }

    // 保存完了後、リダイレクトやメッセージの表示を行う
    echo '選択が保存されました！';
} else {
    echo '無効なリクエストです。';
}
