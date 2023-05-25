<?php


//セッションstart
session_start();
$year = $_SESSION['form_year'];
$month = $_SESSION['form_month'];
$id = $_SESSION['user_id'];

//データベースに接続
$dsn = 'mysql:dbname=*******;host=localhost';
$user = '*******';
$password = '********';

try {
  $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  print('Error:' . $e->getMessage());
  die();
}

//ユーザー情報の取得
$sql = "
SELECT *
FROM login 
WHERE id = :id
";


$stmt = $dbh->prepare($sql);
$stmt->bindValue("id", $id);
$stmt->execute();
$user = $stmt->fetch();

//リダイレクト処理
if (isset($_SESSION['user_id'])) { //ログインしているとき

  if ($user['status'] != 1) {
    //管理者か確認
    printf("あなたは管理者ではありません。");
    exit();
  }

} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}

//締め切り時の処理
if (isset($_GET["btn1"])) {
  $btn1 = $_GET["btn1"];

  $sql = sprintf("
  update request set
  status = 1
  where year = '%s' and
        month = '%s'
  
  ", $year, $month);

  $stmt1 = $dbh->prepare($sql);
  $stmt1->execute();
  //時間を記録
  $str1 = sprintf("log_msg[%s][%s][0]",$year,$month);
  $str2 = sprintf("log_msg[%s][%s][1]",$year,$month);
  setcookie($str1,time(),time() + 60 * 60 * 24 * 30);
  setcookie($str2,"締切",time() + 60 * 60 * 24 * 30);

  //ボタンが押されたか？
  $_SESSION['btn_pushed'] = 1;



}

//解除の処理
if (isset($_GET["btn2"])) {
  $btn2 = $_GET["btn2"];

  $sql = sprintf("
  update request set
  status = 0
  where year = '%s' and
        month = '%s'
  
  ", $year, $month);

  $stmt2 = $dbh->prepare($sql);
  $stmt2->execute();
  //時間を記録
  $str1 = sprintf("log_msg[%s][%s][0]",$year,$month);
  $str2 = sprintf("log_msg[%s][%s][1]",$year,$month);
  setcookie($str1,time(),time() + 60 * 60 * 24 * 30);
  setcookie($str2,"締切解除",time() + 60 * 60 * 24 * 30);

  //ボタンが押されたか？
  $_SESSION['btn_pushed'] = 1;
}

//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき
  header('Location: ./request_close.php');
  exit();
} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}



?>