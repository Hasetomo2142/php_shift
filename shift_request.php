<?php


//セッションstart
session_start();
$year = $_SESSION['year'];
$month = $_SESSION['month'];
$end_month = $_SESSION['end_month'];
$id = $_SESSION['user_id'];

if(isset($_GET['btn1'])){
  $_SESSION['btn_pushed2'] = 1;
}


//日にちごとに開始時刻を格納
for ($i = 1; $i <= $end_month; $i++) {
  $str = 'start';
  $str .= $i;
  $start[$i] = $_GET[$str];
  $start_time[$i] = new DateTime($start[$i]);
}

//日にちごとに終了時刻を格納
for ($i = 1; $i <= $end_month; $i++) {
  $str = 'end';
  $str .= $i;
  $end[$i] = $_GET[$str];
  $end_time[$i] = new DateTime($end[$i]);
}

//開始時刻が終了時刻よりも遅い場合エラーを出す。
for ($i = 1; $i <= $end_month; $i++) {
  if ($start[$i] != null && $end[$i] != null) {
    if ($start_time[$i] > $end_time[$i]) {
      //エラー処理
      $_SESSION['destination'] = "time_form.php";
      $_SESSION['category'] = "ERROR";
      $_SESSION['error_msg'] = "退勤時間を出勤時間より遅くしてください";
      header('Location: ./error_page.php');
      exit('プログラムを終了します');
    }
  }
}

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


//データベースに格納
for ($i = 1; $i <= $end_month; $i++) {
  //データが存在する場合のみ実行
  if ($start[$i] != null || $end[$i] != null) {
    //挿入用のsql

    $sql_count = sprintf("
      select count(*)
      from request
      where id = '%s' and
            year = '%s' and
            month = '%s' and
            day = '%s'
    ", $id, $year, $month, $i);

    $stmt1 = $dbh->prepare($sql_count);
    $stmt1->execute();
    $count = $stmt1->fetch();

    //データが存在しなければ新規登録
    if ((int) $count[0] < 1) {

      $sql = sprintf("insert into request(id,year,month,day,start_time,end_time ) values( '%s','%s', '%s' ,'%s', '%s', '%s' );", $id, $year, $month, $i, $start[$i], $end[$i]);

      $stmt2 = $dbh->prepare($sql);
      $stmt2->execute();
      //データが既にある場合は更新
    } else {

      //データの更新
      $sql = sprintf("
      update request set
      start_time = '%s',
      end_time = '%s'
      where id = '%s' and
            year = '%s' and
            month = '%s' and 
            day = '%s'
            
      ", $start[$i], $end[$i], $id, $year, $month, $i);
      $stmt3 = $dbh->prepare($sql);
      $stmt3->execute();

    }

  }
}

//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき
  header('Location: ./time_form.php');
  exit();
} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}





?>