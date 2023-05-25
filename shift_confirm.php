<?php

//データベースに接続
$dsn = 'mysql:dbname=xs443757_shift;host=localhost';
$user = 'xs443757_tomo';
$password = 'TomoTomo0420';

try{
    $dbh = new PDO($dsn, $user, $password);
}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}


//セッションスタート
session_start();
$id = $_SESSION['user_id'];


//ユーザー情報の取得
$sql ="
SELECT *
FROM login 
WHERE id = :id
";


$stmt = $dbh->prepare($sql);
$stmt->bindValue("id", $id);
$stmt->execute();
$user = $stmt->fetch();




//リダイレクト処理
if (isset($_SESSION['user_id'])) {//ログインしているとき
  
  if($user['status'] != 1){
      //管理者か確認
      printf("あなたは管理者ではありません。");
      exit();
    }

} else {//ログインしていない時
  header('Location: ./login_form.php');
  exit();
}

?>


<HTML>
  <HEAD>
    <TITLE>メニュー</TITLE>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </HEAD>
  <BODY>
  
  メニュー<BR>
  
  <UL>
    <LI><A HREF="employee_list.php">従業員の一覧表示</A>
    <LI><A HREF="employee_add_form.php">従業員のデータ追加</A>
    <LI><A HREF="employee_delete_form.php">従業員のデータ削除</A>
    <LI><A HREF="employee_update_form1.php">従業員のデータ更新</A>
    <LI><A HREF="employee_search_form.php">従業員の検索（部門名での検索）</A>
  </UL>
  
  </BODY>
  </HTML>
  