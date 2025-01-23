<?php

// Question1 配列の中身を表示する
$names = ["Alice", "Bob", "Charlie", "Diana"];
echo "---------- <br/>";
echo "Question1 <br/>";
foreach ($names as $name) {
    echo "Name: {$name} <br/>";
}

// Question2 配列の合計を計算する
$numbers = [10, 20, 30, 40, 50];
echo "---------- <br/>";
echo "Question2 <br/>";
$sum = 0;
foreach ($numbers as $number) {
    $sum += $number;
}
echo "Total: {$sum} <br/>";

// Question3 条件に応じた値の抽出
$scores = [35, 65, 50, 45, 80, 90, 30];
echo "---------- <br/>";
echo "Question3 <br/>";
$array = [];

foreach ($scores as $score) {
    if ($score >= 50) {
        $array[] = $score;
        // echo "Passing scores: {$score} <br/>";
    }
}
// var_dump($array);
$str = implode(", ", $array);
// echo $str . "\n";
echo "Passing scores: {$str} <br/>";
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
foreach ($categories as $type => $menus) {
    echo 'Category:' . $type . "<br>";
    foreach ($menus as $menu) {
        echo '・' . $menu . "<br>";
    }
}

// Question6 値の変換
$numbers = [1, 2, 3, 4, 5];
echo "---------- <br/>";
echo "Question6 <br/>";
$array = [];

foreach ($numbers as $number) {
    $number = $number * 2;
    $array[] = $number;
}
$str = implode(", ", $array);
echo "[{$str}] <br/>";


// Question7 特定の条件で値を変更する
$scores = [35, 65, 50, 45, 80, 90, 30];
echo "---------- <br/>";
echo "Question7 <br/>";
$array = [];
foreach ($scores as $score) {
    if ($score < 50) {
        $array[] = '"Fail"';
    } else {
        $array[] = '"Pass"';
    }
}
$str = implode(", ", $array);

echo "[{$str}] <br/>";


// Question8 配列の中の重複をカウントする
$items = ["apple", "banana", "apple", "orange", "banana", "apple"];
echo "---------- <br/>";
echo "Question8 <br/>";

$items = array_count_values($items);

foreach ($items as $key => $value) {
    echo  "{$key}: {$value} <br>";
}

  // Question9  配列の値を逆順に表示する
  $colors = ["Red", "Blue", "Green", "Yellow"];
echo "---------- <br/>";
echo "Question9 <br/>";
// 配列の長さを取得
$len = count($colors);
$array = [];
foreach ($colors as $color) {
    array_unshift($array, $color);
        // echo "Name: {$color} <br/>";
}
foreach ($array as $color) {
    echo " {$color} <br>";
}

// Question10 特定のキーを持つ値を表示する
$products = [
    ["name" => "Laptop", "price" => 1000],
    ["name" => "Mouse", "price" => 20],
    ["name" => "Keyboard", "price" => 50],
];
echo "---------- <br/>";
echo "Question10 <br/>";

foreach ($products as $product) {
    if ($product['price'] >= 50) {
        echo $product['name'] . "<br/>";
    }
}

// Question11 配列の文字列を大文字に変換する
$words = ["hello", "world", "php", "foreach"];

echo "---------- <br/>";
echo "Question11 <br/>";

foreach ($words as $word) {
    echo strtoupper($word) . ', ' ;
}

 // Question12 文字列の長さを計算する
 $words = ["apple", "banana", "cherry"];

 echo "<br/>---------- <br/>";
 echo "Question12 <br/>";
foreach ($words as $word) {
    echo "$word:";
     echo mb_strlen($word) . " <br/>";
}

// Question13  配列の偶数と奇数を分類する
$numbers = [1, 2, 3, 4, 5, 6, 7, 8];
echo "---------- <br/>";
echo "Question13 <br/>";
$array = [];
$odd = [];
foreach ($numbers as $value) {
    if ($value % 2 == 0) {
        $array[] = $value;
    } else {
        $odd[] = $value;
    }
}

$str = implode(", ", $array);
// echo $str . "\n";
echo "Even: [{$str}] <br/>";


$str = implode(", ", $odd);
// echo $str . "\n";
echo "Odd: [{$str}] <br/>";


  // Question14  配列の値をスキップする
  $numbers = [10, 20, 30, 40, 50];
  echo "---------- <br/>";
  echo "Question14 <br/>";
foreach ($numbers as $number) {
    if ($number == '30') {
        continue;
    }
    echo $number . "<br>";
}

// Question15  ネストした配列の合計を計算する
$data = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
    ];
  echo "---------- <br/>";
  echo "Question15 <br/>";
  $total = 0;
foreach ($data as $number) {
      $total += array_sum($number);
}
echo "Total: {$total} <br> ";


  // Question16  配列のキーを取得する
  $user = [
    "id" => 101,
    "name" => "John",
    "email" => "john@example.com",
  ];
  echo "---------- <br/>";
  echo "Question16 <br/>";
  foreach ($user as $key => $value) {
      echo $key . "<br>";
  }


  // Question17  配列の値をランク付けする
  $scores = [85, 70, 95, 60];
  echo "---------- <br/>";
  echo "Question17 <br/>";
  $sorted_scores = [];
  $count = 0;
  while ($count < count($scores)) {
        $tmp = null;
      foreach ($scores as $val) {
          if (in_array($val, $sorted_scores)) {
              continue;
          }
          if ($tmp === null) {
               $tmp = $val;
          } else {
               $tmp = max($tmp, $val);
          }
      }
      $sorted_scores[] = $tmp;
      $count++;
  }
  $str = implode(", ", $sorted_scores);
  echo " {$str}";

// Question18  配列の重複を削除する
  $items = ["apple", "banana", "apple", "orange", "banana"];

  echo "<br/>---------- <br/>";
  echo "Question18 <br/>";
  $arrayUnique = array_unique($items);
  foreach ($arrayUnique as $value) {

      echo $value . ",";
  }

  // Question19 配列の値を逆順にする（変更）
  $letters = ["A", "B", "C", "D"];
  echo "<br/>---------- <br/>";
  echo "Question19 <br/>";
  $letters = array_reverse($letters);

  $str = implode(", ", $letters);

    echo "[{$str}]";

  $cnt = count($letters);
  $array = [];
  $loop = 1;
  foreach ($letters as $value) {
    $array[] = $letters[$cnt - $loop];
    $loop++;
  }


  // Question20 各値の出現回数を数える
  $data = ["yes", "no", "yes", "no", "yes"];

  echo "<br/>---------- <br/>";
  echo "Question20 <br/>";
//   foreach ($data as $word) {
//       echo "$word:";
//        echo mb_strlen($word) . " <br/>";
// }

    // $yes = [];
    // $no = [];
    //     foreach ($data as $value) {
    //     if ($value == 'yes') {
    //         $yes[] = $value;
    //     } else {
    //         $no[] = $value;
    //     }
    // }

    $yesCount = 0;
    $noCount = 0;
  foreach ($data as $value) {
      if ($value == 'yes') {
            $yes[] = $value;
            $yesCount++;
      } else {
            $no[] = $value;
            $noCount++;
      }
  }

    $str = implode(", ", $array);
    // echo $str . "\n";
    echo "yes: {$yesCount} <br/>";

    $str = implode(", ", $odd);
    // echo $str . "\n";
    echo "no: {$noCount} <br/>";
