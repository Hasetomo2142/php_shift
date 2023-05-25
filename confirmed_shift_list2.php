<?php
//セッションstart
session_start();
$year = $_SESSION['form_year'];
$month = $_SESSION['form_month'];
$id = $_SESSION['user_id'];

//データベースに接続
$dsn = 'mysql:dbname=xs443757_shift;host=localhost';
$user = 'xs443757_tomo';
$password = 'TomoTomo0420';

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



if (isset($_GET["btn1"])){
  $_SESSION['btn_pushed'] = 1;
}


if (isset($_GET['id'])) {
  $_SESSION['id_select'] = $_GET['id'];

} else {
  //選択されていない場合の初期値を設定
  $sql = "
    select *
    from login
    where status = 0;
";

  $stmt6 = $dbh->prepare($sql);
  $stmt6->execute();
  $user_first = $stmt6->fetch();

  $_SESSION['id_select'] = $user_first['id'];
}





//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき
  header('Location: ./confirmed_shift_list.php');
  exit();
} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}



?>