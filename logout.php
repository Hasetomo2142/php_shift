<?php
session_start();
$_SESSION = array();//セッションの中身をすべて削除
session_destroy();//セッションを破壊
setcookie('login_id',"", time() + 1);
setcookie('login_pass',"", time() + 1);
?>

<p>ログアウトしました。</p>
<a href="login_form.php">ログインへ</a>