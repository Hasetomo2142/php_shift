<?php

session_start();
// $username = $_SESSION['name'];
if (isset($_SESSION['user_id'])) { //ログインしているとき
    $msg = 'こんにちは' . htmlspecialchars($_SESSION['name'], \ENT_QUOTES, 'UTF-8') . 'さん';
    $msg2 = 'id:' . htmlspecialchars($_SESSION['user_id']);
    $link = '<a href="logout.php">ログアウト</a>';
} else { //ログインしていない時
    $msg = 'ログインしていません';
    $link = '<a href="login_form.php">ログイン</a>';
    header('Location: ./login_form.php');
    exit();
}

//データベースに接続
$dsn = 'mysql:dbname=xs443757_shift;host=localhost';
$user = '*******';
$password = '*******';

try {
  $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  print('Error:' . $e->getMessage());
  die();
}

$id = $_SESSION['user_id'];


?>



<HEAD>
  <TITLE>ログインメニュー</TITLE>
  <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="login_form.css">
  <link rel="stylesheet" href="index.css">
</HEAD>

<a href="index.php" style="text-decoration:none;">
  <header>
    <div class="container">
      <h1>シュシュ勤怠管理システム</h1>
    </div>
  </header>
  </a>
    <div class="user">
        <div class="container">
            <h1><?php echo $msg; ?></h1>
            <h2><?php echo $msg2; ?></h2>
        </div>
    </div>


<div class="login">
  <br>
  <br>

  
  <h2 class="login-header">パスワード変更</h2>

  <form class="login-container"action="setting2.php" method="post">
    <p><input type="text" name="current_password" placeholder="現在のパスワード" required></p>
    <p><input type="password" name="new_password" placeholder="新しいパスワード" required></p>
    <p><input type="password" name="confirm_password" placeholder="新しいパスワード（確認用）"  required></p>
    <p><input type="submit" value="変更"></p>
  </form>
</div>
