<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームから送信されたデータを取得
    $response_id = $_POST['response_id'];
    $response = $_POST['response'];
    $comment = $_POST['comment'];

    // SQL 更新クエリ
    $sql = "UPDATE responses 
            SET response = '$response', comment = '$comment' 
            WHERE id = $response_id";

    if ($conn->query($sql) === true) {
        echo "Record updated successfully";
        // 更新後、一覧ページにリダイレクトすることもできます
        header("Location: submit_response.php?event_id=" . $_GET['event_id']);
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
