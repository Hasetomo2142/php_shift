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

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>シュシュ勤怠管理システム</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>


<a href="index_admin.php" style="text-decoration:none;">
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

<div class="container">
<section>
        <article>
            <a href="shift_list.php" style="text-decoration:none;">
                <div clas="container2">
                <p>申請シフト</p>
                </div>
            </a>

        </article>
        <article>
        <a href="request_close.php" style="text-decoration:none;">
                <div clas="container2">
                <p>申請締切</p>
                </div>
            </a>
        </article>
        <article>
        <a href="confirmed_shift_list.php" style="text-decoration:none;">
                <div clas="container2">
                <p>確定シフト</p>
                </div>
            </a>
        </article>
        <article>
        <a href="setting.php" style="text-decoration:none;">
                <div clas="container2">
                <p>設定</p>
                </div>
            </a>
        </article>
    </section>
</div>

    <div class="container">
        <?php echo $link; ?>
    </div>

</body>

</html>