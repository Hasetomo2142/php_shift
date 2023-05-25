<?php
session_start();
$id = $_POST['id'];


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


$sql ="
SELECT *
FROM login 
WHERE id = :id
";


$stmt = $dbh->prepare($sql);
$stmt->bindValue("id", $id);
$stmt->execute();
$user = $stmt->fetch();
// $pass = $_POST['pass']

//指定したハッシュがパスワードにマッチしているかチェック
if (strcmp($_POST['pass'], $user['pass']) == 0) {
    //DBのユーザー情報をセッションに保存
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    // $msg = 'ログインしました。';
    $link = '<a href="index.php">ホーム</a>';
} else {
    $_SESSION['destination'] = "login_form.php";
    $_SESSION['category'] = "ERROR";
    $_SESSION['error_msg'] = "IDもしくはパスワードが違います。";
    header('Location: ./error_page.php');
    exit('プログラムを終了します');

}

	
setcookie('login_id', $user['id'], time() + 60 * 60 * 24 * 30);
setcookie('login_pass', $user['pass'], time() + 60 * 60 * 24 * 30);


printf("%s",$_COOKIE["login_pass"]);
printf("%s",$_COOKIE["login_id"]);

//管理者かどうか判定
if($user['status'] == 0){
    //index.phpにリダイレクト
    // header('Location: ./index.php');
    header('Location: ./index.php');
    exit();
}else{
    //index copy.phpにリダイレクト
    header('Location: ./index_admin.php');
    exit();
}


?>
