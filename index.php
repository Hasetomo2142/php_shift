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



// 過去一週間で優先度が高いインスタンスを取得
// $time = date("Y-m-d H:i:s",strtotime("-1 week"));


// $sql = sprintf("
// select 
// 	* 
// from 
// 	information 
// where 
// 	priority = 1 
// 	and (
// 		address = '%s' 
// 		or address = 'all'
// 	) 
// 	and time_stamp > '%s'
// order by time_stamp desc
// LIMIT 
// 	1
// ",$id,$time);

// $stmt = $dbh->prepare($sql);
// $stmt->execute();
// $info_first = $stmt->fetch(PDO::FETCH_ASSOC);

// //もし結果があれば
// if(count($info_first)>0){

//     $time_str = date("Y.m.d",strtotime($info_first['time_stamp']));

//     $info[] = sprintf("
//     <dt><span class = 'color_red'>%s</span>
//     <time>%s</time></dt>
//     <dd><a href='#'>%s</a></dd>
//     ",$info_first['categly'],$time_str,$info_first['message']);

// }



//2番目以降のお知らせ
$sql2 = sprintf("
select 
	* 
from 
	information 
where 
	(
		address = '%s' 
		or address = 'all'
	) 
	and time_stamp > '%s'

order by time_stamp desc

LIMIT 
	10
",$id,$time);


$stmt2 = $dbh->prepare($sql2);
$stmt2->execute();
$info = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if(count($info)>0){

    for($i = 0; $i < count($info); $i++){
        //色を確定
        if(!strcmp("重要",$info[$i]['categly'])){
            $color ="color_red";
        }elseif(!strcmp("シフト変更",$info[$i]['categly'])){
            $color ="color_red";
        }elseif(!strcmp("お知らせ",$info[$i]['categly'])){
            $color ="color_green";
        }else{
            $color ="color_blue";
        }


        $time_str = date("Y.m.d",strtotime($info[$i]['time_stamp']));



        if (strpos($info[$i]['message'], 'シフト') !== false) {
            // 'シフト'が含まれている場合の処理
            
        $info_str[$i] = sprintf("
        <dt><span class = '%s'>%s</span>
        <time>%s</time></dt>
        <dd><a href='infomation_link.php?msg=%s'>%s</a></dd>
        ",$color,$info[$i]['categly'],$time_str,$info[$i]['message'],$info[$i]['message']);

          } else {
            // 'シフト'が含まれていない場合の処理
            
        $info_str[$i] = sprintf("
        <dt><span class = '%s'>%s</span>
        <time>%s</time></dt>
        <dd>%s</dd>
        ",$color,$info[$i]['categly'],$time_str,$info[$i]['message']);

          }


    }
}







// お知らせの取得

// <dt><span class = "color_red">重要</span><time>2020.7.29</time></dt>
// <dd><a href="#">ダミーダミーダミーダミー</a></dd>

?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>シュシュ勤怠管理システム</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>




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




<!-- お知らせボックス -->
<div class="container_box">
<dl class="info">
    <?php 
        for($i = 0; $i < count($info_str);$i++){
            printf("%s",$info_str[$i]);
        }
    ?>
</dl>
</div>

<div class="container">
<section>
        <article>
            <a href="time_form.php" style="text-decoration:none;">
                <div clas="container2">
                <p>シフト申請</p>
                </div>
            </a>

        </article>
        <article>
        <a href="shift_delete_form.php" style="text-decoration:none;">
                <div clas="container2">
                <p>申請削除</p>
                </div>
            </a>
        </article>
        <article>
        <a href="confirmed_shift.php" style="text-decoration:none;">
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
