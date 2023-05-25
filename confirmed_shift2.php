<?php
session_start();
$_SESSION['btn_pushed5'] = 1;
$id = $_SESSION['user_id'];
$year = $_SESSION['year'];
// $month = $_SESSION['month'];
$month = $_GET['month'];
$date = $_GET['date'];

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


  //startの初期値を設定
  $sql_start = sprintf("
  select start_time
  from shift
  where id = '%s' and
        year = '%s' and
        month = '%s' and
        day = '%s'
", $id, $year, $month, $date);

  $stmt2 = $dbh->prepare($sql_start);
  $stmt2->execute();
  $start = $stmt2->fetch();

  //endの初期値を設定
  $sql_end = sprintf("
    select end_time
    from shift
    where id = '%s' and
          year = '%s' and
          month = '%s' and
          day = '%s'
  ", $id, $year, $month, $date);

  $stmt3 = $dbh->prepare($sql_end);
  $stmt3->execute();
  $end = $stmt3->fetch();
$str2 = 
sprintf("%s　～　%s",$start['start_time'],$end['end_time'])
;


$SQL = sprintf("

select 
  name, 
  start_time, 
  end_time 
FROM 
	login 
	INNER JOIN shift ON login.id = shift.id 
where 
	login.id = shift.id 
	AND year = '%s' 
	AND month = '%s' 
	AND day = '%s' 
    AND login.id NOT IN ('%s')
order by start_time asc
",$year,$month,$date,$id);

$stmt1 = $dbh->prepare($SQL);
$stmt1->execute();
$member = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$str = "";
for($i = 0; $i < count($member); $i++){
  $str.= sprintf("<li>%s　%s～%s</li>",$member[$i]['name'],$member[$i]['start_time'],$member[$i]['end_time']);
}

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

  <div class="container">
    <br>
    <p style="font-size:50px; font-weight:bold;"><?php printf("%d月%d日",$month,$date); ?></p>
    <br>
    <br>
    <h1>あなたの出勤時間</h1>
    <br>
      <?php echo $str2; ?>
    <br>
    <br>
    <br>
    <h1>出勤メンバー</h1>
    <br>
    <ul>
      <?php echo $str; ?>
    </ul>
    <br>
  </div>

  <FORM ACTION="confirmed_shift.php" METHOD="GET">
    <div class="container">
        <INPUT TYPE="submit" VALUE="戻る" class="button" name="btn_error">
    </div>
  </form>
  <div class="container">
  </div>

</body>

</html>




