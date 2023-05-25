<?php
session_start();

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

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');


$today = filter_input(INPUT_POST, 'today');
$monthNext = filter_input(INPUT_POST, 'monthNext');
$yearNext = filter_input(INPUT_POST, 'yearNext');
$monthPrev = filter_input(INPUT_POST, 'monthPrev');
$yearPrev = filter_input(INPUT_POST, 'yearPrev');

$caldate = time();

if ($today == 1) {
  $month = date("n", $caldate);
  $year = date("Y", $caldate);
}
if ($monthNext > 12) {
  $monthNext = 1;
  $yearNext++;
}
if ($monthPrev === "0") {
  $monthPrev = 12;
  $yearPrev--;
}
$month = $monthNext ?? $monthPrev ?? date('n');
$year = $yearNext ?? $yearPrev ?? date('Y');

//ページ更新時の処理
if (isset($_SESSION['form_month']) && isset($_SESSION['form_year']) && isset($_SESSION['btn_pushed5'])) {
  $month = $_SESSION['form_month'];
  $year = $_SESSION['form_year'];
  unset($_SESSION['btn_pushed5']);
}
$_SESSION['form_month'] = $month;
$_SESSION['form_year'] = $year;


//月末日を取得
$end_month = date('t', strtotime($year . $month . '01'));


$_SESSION['year'] = $year;
$_SESSION['month'] = $month;
$_SESSION['end_month'] = $end_month;


$id = $_SESSION['user_id'];

$aryCalendar = [];
$aryMsg = [];


//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき

} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}

//労働時間を格納する変数
$time = 0;

//入力欄の生成
for ($i = 1; $i <= $end_month; $i++) {

  //startの初期値を設定
  $sql_start = sprintf("
  select start_time
  from shift
  where id = '%s' and
        year = '%s' and
        month = '%s' and
        day = '%s'
", $id, $year, $month, $i);

  $stmt1 = $dbh->prepare($sql_start);
  $stmt1->execute();
  $start = $stmt1->fetch();

  //endの初期値を設定
  $sql_end = sprintf("
    select end_time
    from shift
    where id = '%s' and
          year = '%s' and
          month = '%s' and
          day = '%s'
  ", $id, $year, $month, $i);

  $stmt2 = $dbh->prepare($sql_end);
  $stmt2->execute();
  $end = $stmt2->fetch();

  $aryScheduler[$i] = '';

  if ((!empty($start['start_time'])) || (!empty($end['end_time']))) {

    if (!empty($start['start_time'])) {
      $aryScheduler[$i] .= $start['start_time'];
    } else {
      $aryScheduler[$i] .= '     ';
    }

    $aryScheduler[$i] .= '～';

    if (!empty($end['end_time'])) {
      $aryScheduler[$i] .= $end['end_time'];
    } else {
      $aryScheduler[$i] .= '     ';
    }
  }

  $start_time2 = null;
  $end_time2 = null;
  if((!empty($start['start_time'])) && (!empty($end['end_time']))) {

    $diff_hour = (strtotime($end['end_time']) - strtotime($start['start_time'])) / 3600;
    $time += $diff_hour;
    $aryMsg[$i] = sprintf("<a href='confirmed_shift2.php?date=%d & month=%d'>詳細</a>",$i,$month);

  }else{
    $aryMsg[$i] = "";
  }


}

$time_str = "勤務時間合計 : " . (string) $time . "時間";


//1日から月末日までループ
for ($i = 1; $i <= $end_month; $i++) {
  $aryCalendar[$i]['day'] = $i;
  $aryCalendar[$i]['week'] = date('w', mktime(12, 00, 00, $month, sprintf('%02d', $i), $year));
  $aryCalendar[$i]['text'] = $aryScheduler[$i];
  $aryCalendar[$i]['link'] = $aryMsg[$i];
}

$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>シュシュ勤怠管理システム</title>
  <link rel="stylesheet" href="styles2.css">
</head>

<body>
  <a href="index.php">
    <header>
      <div class="container">
        <h1>シュシュ勤怠管理システム</h1>
      </div>
    </header>
  </a>

  <div class="bar">
    <div class="container">
      <section>

        <article>
          <form action="" method="post">
            <th><button type="submit" id="prev">
                &laquo;
                <input type="hidden" name="monthPrev" value="<?php echo $month - 1; ?>">
                <input type="hidden" name="yearPrev" value="<?php echo $year; ?>">
              </button></th>
          </form>
        </article>
        <article>
          <h1><?php echo $year ?></h1>
          <bar>
            <h1><?php echo $month ?></h1>
        </article>
        <article>
          <form action="" method="post">
            <th><button type="submit" id="next">
                &raquo;
                <input type="hidden" name="monthNext" value="<?php echo $month + 1; ?>">
                <input type="hidden" name="yearNext" value="<?php echo $year; ?>">
              </button></th>
          </form>
        </article>

      </section>
    </div>
  </div>

    <div class="container">
      <table class="calender_column">
        <?php foreach ($aryCalendar as $value) { ?>
        <?php if ($value['day'] != date('j')) { ?>
        <tr class="week<?php echo $value['week'] ?>">
          <?php } else { ?>
        <tr class="today">
          <?php } ?>
          <td>
            <?php echo $value['day'] ?>(<?php echo $aryWeek[$value['week']] ?>)
          </td>
          <td>
            <?php echo $value['text'] ?>
          </td>
          <td>
            <?php echo $value['link'] ?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <div class="container">
<br>
    </div>
    <div class="container">
      <p><?php echo $time_str; ?></p>
    </div>
    <div class="container">
<br>
    </div>

</body>

</html>