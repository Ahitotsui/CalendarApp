<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="logout.css">
<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico" />
<title>ログアウト</title>
</head>

<body>

<!--ヘッダー領域-->
<header>
  <h1>Calender</h1>
</header>
<!--ヘッダー領域(END)--->

<?php 
  session_start();
  unset($_SESSION['login']);
?>

<main>
  <div>
    <h2>ログアウト</h2>
    <h3>正常にログアウトしました。</h3>
    <p><a href="index.html">ログイン画面に戻る</a></p>
  </div>
</main>

<footer>
  
</footer>

</body>
</html>
