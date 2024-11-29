<?php

// 編集するレスポンスIDを取得
$response_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($response_id) {
    // SQLクエリで特定のレスポンスデータを取得
    $sql = "SELECT * FROM responses WHERE id = $response_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // 編集フォームを表示
        echo "<form method='POST' action='update_response.php'>
                <label for='response'>Response:</label>
                <input type='text' id='response' name='response' value='" . $row['response'] . "' required><br>
                <label for='comment'>Comment:</label>
                <textarea id='comment' name='comment'>" . $row['comment'] . "</textarea><br>
                <input type='hidden' name='response_id' value='" . $row['id'] . "'>
                <input type='submit' value='Update'>
            </form>";
    }
}
