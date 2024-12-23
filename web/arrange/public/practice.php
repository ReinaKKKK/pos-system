<?php

// Question1 配列の中身を表示する
$names = ["Alice", "Bob", "Charlie", "Diana"];
echo "---------- <br/>";
echo "Question1 <br/>";
foreach ($names as $value) {
    echo "Name: {$value} <br/>";
}

// Question2 配列の合計を計算する
$numbers = [10, 20, 30, 40, 50];
echo "---------- <br/>";
echo "Question2 <br/>";
$sum = 0;
foreach ($numbers as $value) {
    $sum += $value;
}
echo "Total: {$sum} <br/>";

// Question3 条件に応じた値の抽出
$scores = [35, 65, 50, 45, 80, 90, 30];
echo "---------- <br/>";
echo "Question3 <br/>";
foreach ($scores as $score) {
    if ($score < 50) {
        continue;
    }
    //  echo 'Passing scores: ' . $score . '<br/>';
     echo "Passing scores: {$score} <br/>";
}

// Question4 連想配列のキーと値を表示する
$person = [
  "name" => "Alice",
  "age" => 25,
  "job" => "Developer"
];

echo "---------- <br/>";
echo "Question4 <br/>";
foreach ($person as $key => $value) {
    echo "Key: {$key}, Value: {$value} <br>";
}
// Question5 ネストした配列を処理する
$categories = [
    "Fruits" => ["Apple", "Banana", "Orange"],
    "Vegetables" => ["Carrot", "Broccoli", "Spinach"],
];

  echo "---------- <br/>";
  echo "Question5 <br/>";
  foreach ($categories as $category) {
    foreach ($category as $Fruits => $Vegetables) {
        echo "{$price}: {$value} <br/>";
    }


  
// Question6 値の変換
$numbers = [1, 2, 3, 4, 5];
echo "---------- <br/>";
echo "Question6 <br/>";
foreach ($numbers as $number) {
    $numbers = $number * 2;
}
echo "{$numbers} <br/>";

// Question7 特定の条件で値を変更する
$scores = [35, 65, 50, 45, 80, 90, 30];
echo "---------- <br/>";
echo "Question7 <br/>";
foreach ($scores as $score) {
    if ($score <= 50) {
        echo "Fail<br>";
    } else {
        echo "Pass<br>";
    }
}

// Question8 配列の中の重複をカウントする
$items = ["apple", "banana", "apple", "orange", "banana", "apple"];
echo "---------- <br/>";
echo "Question8 <br/>";

$items = array_count_values($items);

foreach ($items as $value) {
    echo $value;
    echo '<br>';
}

  // Question9  配列の値を逆順に表示する
  $colors = ["Red", "Blue", "Green", "Yellow"];

echo "---------- <br/>";
echo "Question9 <br/>";
$result = array_reverse($colors);
echo'<br>';
print_r($result);

// Question10 特定のキーを持つ値を表示する
$products = [
    ["name" => "Laptop", "price" => 1000],
    ["name" => "Mouse", "price" => 20],
    ["name" => "Keyboard", "price" => 50],
];
echo "---------- <br/>";
echo "Question10 <br/>";

foreach ($products as $product) {
    foreach ($product as $price => $value) {
        echo "{$price}: {$value} <br/>";
    }
}
// if ($price < 50) {
//     continue;
// }
//  echo '{$key} . ' <br/>';
// }


  // Question11 配列の文字列を大文字に変換する
  $words = ["hello", "world", "php", "foreach"];

echo "---------- <br/>";
echo "Question11 <br/>";

foreach ($words as $word) {
    echo strtoupper($word) . " <br/>";
}

  // Question12 文字列の長さを計算する
  $words = ["apple", "banana", "cherry"];

echo "---------- <br/>";
echo "Question12 <br/>";
foreach ($words as $word) {
    echo mb_strlen($word) . " <br/>";
}

  // Question13  配列の偶数と奇数を分類する
  $numbers = [1, 2, 3, 4, 5, 6, 7, 8];
  echo "---------- <br/>";
  echo "Question13 <br/>";
foreach ($numbers as $value) {
    if ($int % 2 == 0) {
        echo $value . "<br>";
    } else {
        echo $value . "<br>";
    }
}


  // Question14  配列の値をスキップする
  $numbers = [10, 20, 30, 40, 50];
  echo "---------- <br/>";
  echo "Question14 <br/>";
foreach ($numbers as $number) {
    if ($number == '30') {
        continue;
    }
    echo $numbers . "<br>";
}

  // Question15  ネストした配列の合計を計算する
  $data = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
    ];
  echo "---------- <br/>";
  echo "Question15 <br/>";
  $sum = 0;
  foreach ($data as $value) {
      $sum += $value;
  }
  echo "Total: {$sum} <br/>";

  // Question16  配列のキーを取得する
  $user = [
    "id" => 101,
    "name" => "John",
    "email" => "john@example.com",
];
echo "---------- <br/>";
echo "Question16 <br/>";
foreach ($information as $key => $value) {
    echo "Key: {$key} <br>";
}

// Question17  配列の値をランク付けする
    $scores = [85, 70, 95, 60];
    echo "---------- <br/>";
    echo "Question17 <br/>";
    $sorted_scores = [];
    $count = 0;
    while($count < count($scores)) {
    $tmp = null;
    foreach ( $scores as $key => $val ) {
    if (in_array($val, $sorted_scores)) continue;
    if ($tmp === null) {
        $tmp = $val;
    } else {
        $tmp = min($tmp, $val);
    }
    }
    $sorted_scores[] = $tmp;
    $count++;
    }


// Question18  配列の重複を削除する
    $items = ["apple", "banana", "apple", "orange", "banana"];

    echo "---------- <br/>";
    echo "Question18 <br/>";
    $arrayUnique = array_unique($items);
    echo($arrayUnique)


// Question19 配列の値を逆順にする（変更）
    $letters = ["A", "B", "C", "D"];
    echo "---------- <br/>";
    echo "Question19 <br/>";
    $letters = array_reverse($letters);
    echo'<br>';
    print_r($letters);


// Question20 各値の出現回数を数える
$data = ["yes", "no", "yes", "no", "yes"];

echo "---------- <br/>";
echo "Question20 <br/>";
foreach ($data as $dataa) {
