<?php
  function generateTimeOptions($name, $value) {
    echo "<select style='width: 200px; height: 60px;' class='area' height='150' name='{$name}'>";
    echo "<option value=''>&nbsp;</option>";
    for ($i = 5; $i <= 20; $i++) {
      for ($j = 0; $j < 2; $j++) {
        $hour = str_pad($i, 2, "0", STR_PAD_LEFT);
        $minute = str_pad($j * 30, 2, "0", STR_PAD_LEFT);
        $time = "{$hour}:{$minute}";
        if ($time === $value) {
          echo "<option value='{$time}' selected>{$time}</option>";
        } else {
          echo "<option value='{$time}'>{$time}</option>";
        }
      }
    }
    echo "</select>";
  }
?>



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

if (isset($_SESSION['form_month']) && isset($_SESSION['form_year']) && isset($_SESSION['btn_pushed2'])) {
  $month = $_SESSION['form_month'];
  $year = $_SESSION['form_year'];
  unset($_SESSION['btn_pushed2']);
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
$start_tmp = [];
$end_tmp = [];


//リダイレクト
if (isset($_SESSION['user_id'])) { //ログインしているとき

} else { //ログインしていない時
  header('Location: ./login_form.php');
  exit();
}

//入力欄の生成
for ($i = 1; $i <= $end_month; $i++) {

  //startの初期値を設定
  $sql_start = sprintf("
  select start_time
  from request
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
    from request
    where id = '%s' and
          year = '%s' and
          month = '%s' and
          day = '%s'
  ", $id, $year, $month, $i);

  $stmt2 = $dbh->prepare($sql_end);
  $stmt2->execute();
  $end = $stmt2->fetch();

  //編集可能か判定
  $sql_status = sprintf("
        select status
        from request
        where id = '%s' and
              year = '%s' and
              month = '%s' and
              day = '%s'
      ", $id, $year, $month, $i);

  $stmt4 = $dbh->prepare($sql_status);
  $stmt4->execute();
  $s = $stmt4->fetch();

  if (isset($s['status'])) {
    if ($s['status'] == 0) {
      

      if (!empty($start['start_time'])) {
        $start_tmp[$i] = $start['start_time'];
      }

      if (!empty($end['end_time'])) {
        $end_tmp[$i] = $end['end_time'];
      }

      ob_start();
      $t = "start".$i; 
      generateTimeOptions($t,$start_tmp[$i]);
      $str2 = ob_get_clean();
      $aryScheduler[$i] = $str2;

      $aryScheduler[$i] .= '  ';

      ob_start();
      $t = "end".$i; 
      generateTimeOptions($t,$end_tmp[$i]);
      $str2 = ob_get_clean();
      $aryScheduler[$i] .= $str2;
      


    } else {
      //申請済みの場合はテキストボックスを非表示
      if (!empty($start['start_time'])) {
        $aryScheduler[$i] = $start['start_time'];
      } else {
        $aryScheduler[$i] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }

      $aryScheduler[$i] .= '　～　';

      if (!empty($end['end_time'])) {
        $aryScheduler[$i] .= $end['end_time'];
      } else {
        $aryScheduler[$i] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }
    }
  } else {
    if (!empty($start['start_time'])) {
      $start_tmp[$i] = $start['start_time'];
    }

    if (!empty($end['end_time'])) {
      $end_tmp[$i] = $end['end_time'];
    }

    ob_start();
    $t = "start".$i; 
    generateTimeOptions($t,$start_tmp[$i]);
    $str2 = ob_get_clean();
    $aryScheduler[$i] = $str2;

    $aryScheduler[$i] .= '  ';

    ob_start();
    $t = "end".$i; 
    generateTimeOptions($t,$end_tmp[$i]);
    $str2 = ob_get_clean();
    $aryScheduler[$i] .= $str2;
    
  }





}

//ステータスの確認

$status = [];

for ($i = 1; $i <= $end_month; $i++) {
  $sql = sprintf("
  select status
  from request
  where id = '%s' and
        year = '%s' and
        month = '%s' and
        day = '%s'
", $id, $year, $month, $i);

  $stmt3 = $dbh->prepare($sql);
  $stmt3->execute();
  $s = $stmt3->fetch();

  //値が格納されている日のみ処理を行う
  $tmp = isset($s['status']);

  //値が格納されている場合
  if ($tmp == 1) {
    if ($s['status'] == 0) {
      $status[$i] = '<font color="#0000ff">申請中</font>';
    } else {
      $status[$i] = '<font color="#ff7f7f">申請済</font>';
    }
    //値が格納されていない場合
  } else {
    $status[$i] = '';
  }




}

//1日から月末日までループ
for ($i = 1; $i <= $end_month; $i++) {
  $aryCalendar[$i]['day'] = $i;
  $aryCalendar[$i]['week'] = date('w', mktime(12, 00, 00, $month, sprintf('%02d', $i), $year));
  $aryCalendar[$i]['text'] = $aryScheduler[$i];
  $aryCalendar[$i]['status'] = $status[$i];
}

$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>シュシュ勤怠管理システム</title>
  <link rel="stylesheet" href="styles.css">
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

  <FORM ACTION="shift_request.php" METHOD="GET">

    <div class="container">
      <table class="calender_column">
        <tr>
          <td></td>
          <td>
          <div style="display: flex; justify-content: space-between; align-items: center;">
  <div style="flex-basis: 50%; text-align: center;">出勤</div>
  <div style="flex-basis: 50%; text-align: center;">退勤</div>
</div>
          </td>
          <td></td>
          <td></td>
        </tr>
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
            <?php echo $value['status'] ?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>


    <BR>
    <div class="container">
      <INPUT TYPE="submit" VALUE="送信" class="button" name="btn1">
    </div>
    <BR>
  </FORM>

</body>

</html>