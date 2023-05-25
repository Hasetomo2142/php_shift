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
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>シュシュ勤怠管理システム</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>


<a href="index.php" style="text-decoration:none;">
  <header>
    <div class="container">
      <h1>シュシュ勤怠管理システム</h1>
    </div>
  </header>
  </a>

  <div class="container">
    <br>
  </form>
  
  <?php  echo $_SESSION['category'];?>
  <br>
  <?php  echo $_SESSION['error_msg'];?>

  <div class="container">
    <br>
  </form>

  <FORM ACTION="error_page2.php" METHOD="GET">
    <div class="container">
        <INPUT TYPE="submit" VALUE="戻る" class="button" name="btn_error">
    </div>
  </form>

  <div class="container">
    <br>
  </form>
  
</body>

</html>