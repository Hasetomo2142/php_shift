<?php
// セッションの開始
session_start();

// セッション変数に現在のパスワード、新しいパスワード、確認用パスワードを保存する
if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
}

//データベースに接続
$dsn = 'mysql:dbname=*******;host=localhost';
$user = '*******';
$password = '********';

try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードの設定
} catch (PDOException $e) {
  exit('Error:' . $e->getMessage());
}

// セッションに保存されたユーザーIDを取得
$id = $_SESSION['user_id'];

// 現在のパスワードを取得
$sql = sprintf("SELECT pass FROM login WHERE id = '%s'",$id);
print($sql);
$stmt = $dbh->prepare($sql);
$stmt->execute();
$pass = $stmt->fetch(PDO::FETCH_COLUMN);

// 現在のパスワードが合っているかチェック
if ($pass != $current_password) {
  $_SESSION['destination'] = "setting.php";
  $_SESSION['category'] = "ERROR";
  $_SESSION['error_msg'] = "現在のパスワードが正しくありません。";
  
  header('Location: ./error_page.php');
  exit;
}

// 確認用のパスワードが一致しているか
if ($new_password !== $confirm_password) {
  $_SESSION['destination'] = "setting.php";
  $_SESSION['category'] = "ERROR";
  $_SESSION['error_msg'] = "新しいパスワードが確認用のパスワードと一致しません。再度入力してください。";
  header('Location: ./error_page.php');
  exit;
}

// パスワードが要件を満たしているかチェック
if (strlen($new_password) < 6 || strlen($new_password) > 12 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
  $_SESSION['destination'] = "setting.php";
  $_SESSION['category'] = "ERROR";
  $_SESSION['error_msg'] = "パスワードが要件を満たしていません。英数字を含む6文字以上12文字以下のパスワードを設定してください。";
  header('Location: ./error_page.php');
  exit;
}


// SQL文を作成する
$sql = "UPDATE login SET pass='$new_password' WHERE id='$id'";
$stmt2 = $dbh->prepare($sql);
$stmt2->bindValue(":id", $id, PDO::PARAM_INT);
$stmt2->execute();


echo "パスワードを変更しました。";

$_SESSION['destination'] = "index.php";
$_SESSION['category'] = "";
$_SESSION['error_msg'] = "パスワードを変更しました。";
header('Location: ./error_page.php');
exit;

?>