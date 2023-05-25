<?php
session_start();
// $username = $_SESSION['name'];
if (isset($_SESSION['user_id'])) { //ログインしているとき
  $msg = 'こんにちは' . htmlspecialchars($_SESSION['name'], \ENT_QUOTES, 'UTF-8') . 'さん';
  $msg2 = 'id:' . htmlspecialchars($_SESSION['user_id']);
  $link = '<a href="logout.php">ログアウト</a>';
} else { //ログインしていない時
  $msg = 'ログインしていません';
  $link = '<a href="login_horm.php">ログイン</a>';
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
if (isset($_SESSION['form_month']) && isset($_SESSION['form_year']) && isset($_SESSION['btn_pushed'])) {
  $month = $_SESSION['form_month'];
  $year = $_SESSION['form_year'];
  unset($_SESSION['btn_pushed']);
}

$_SESSION['form_month'] = $month;
$_SESSION['form_year'] = $year;

$id = $_SESSION['user_id'];



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

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>シュシュ勤怠管理システム</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>


  <a href="index_admin.php" style="text-decoration:none;">
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

  <FORM ACTION="request_close2.php" METHOD="GET">

  <div class="container">
      <p>※シフトは毎月25日に自動的に締め切られます。</p>
      <br>
  </div>
    <div class="container">
      <INPUT TYPE="submit" name="btn1" VALUE="締切" class="button">
    </div>
    <div class="container">
      <INPUT TYPE="submit" name="btn2" VALUE="解除" class="button">
    </div>
  </FORM>


  <div class="container" style="padding: 100px 15px">
    <?php
  if (isset($_COOKIE['log_msg'][$year][$month])) {
    printf("%sに", date("Y/m/d H:i:s", $_COOKIE['log_msg'][$year][$month][0]));
    printf("%sしました", $_COOKIE['log_msg'][$year][$month][1]);
  }
  ; ?>

  </div>


</body>

</html>