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
     echo 'Passing scores: ' . $score . '<br/>';
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
$colors = array_reverse($colors);
echo $colors;
    echo '<br>';

  // Question11 配列の文字列を大文字に変換する
  $words = ["hello", "world", "php", "foreach"];

echo "---------- <br/>";
echo "Question11 <br/>";

foreach ($words as $word) {
    echo strtoupper($word) . "\n";
}

  // Question12 文字列の長さを計算する
  $words = ["apple", "banana", "cherry"];

echo "---------- <br/>";
echo "Question12 <br/>";
$words = array_count_values($words);

foreach($strArray as $val){
  echo "<tr>";
  echo"<td>" . $val . "</td>";
  echo"<td>" . strlen($val) . "</td>";
  echo"<td>" . mb_strlen($val) . "</td>";
  echo "</tr>";
}
