<?php

session_start();
$text = $_GET['msg'];

// 月と日を抽出するパターンを定義
$pattern = "/(\d{1,2})月(\d{1,2})日/";

// パターンにマッチした部分文字列を取得
preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

// マッチした部分文字列から月と日を取得
foreach ($matches as $match) {
    $month = $match[1];
    $day = $match[2];
}



//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき
  $tmp = sprintf("Location: ./confirmed_shift2.php?date=%d & month=%d",$day,$month);
  header($tmp);
  exit();
} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}


?>