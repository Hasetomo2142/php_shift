<?php


//セッションstart
session_start();
$year = $_SESSION['year'];
$month = $_SESSION['month'];
$end_month = $_SESSION['end_month'];
$id = $_SESSION['user_id'];


if(isset($_GET['btn'])){
  $_SESSION['btn_pushed3'] = 1;
}


//データベースに接続
$dsn = 'mysql:dbname=*******;host=localhost';
$user = '*******';
$password = '********';

try{
    $dbh = new PDO($dsn, $user, $password);
}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}


//選択された項目を変数に格納
for ($i = 1; $i <= $end_month; $i++){
  $str = 'check_box';
  $str .= $i;
  if(isset($_GET[ $str ])){
    $check_box[$i] = $_GET[ $str ];
  }

}

//選択された項目を削除
for ($i = 1; $i <= $end_month; $i++){
  if(isset($check_box[$i])){
    $sql = sprintf("
    delete
    from request
    where id = '%s' and
          year = '%s' and
          month = '%s' and
          day = '%s'
  ",$id,$year,$month,$i);
  $stmt1 = $dbh->prepare($sql);
  $stmt1->execute();
  }
}





//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき
  header('Location: ./shift_delete_form.php');
  exit();
} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}





?>
