<?php
//セッション開始
session_start();

/*同パソコンで過去に他人がログインした経歴があった場合、そのユーザーのセッションデータを
消しておき、他の誰かに使用されないようにする*/
unset($_SESSION['login']);

//ユーザー名&パスワード受け取り
$username = $_POST['username'];
$password = $_POST['password'];

//外部ファイルよりDB情報を取得
require_once('../DBInfo.php');

//DB接続
$pdo = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

try{
  $sql = "SELECT * FROM login WHERE username = ? and password = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username,$password]);

  foreach($stmt as $row){
    /*$sqlの検索で一致するものがあればユーザー名&パスワードをセッションデータにする
    (一致するものが無ければセッションデータは作成されない)*/
    $_SESSION['login']['username'] = $row['username'];
    $_SESSION['login']['password'] = $row['password'];
    $_SESSION['login']['name'] = $row['name'];
  }

}catch(Exception $e){
  $error = $e->getMessage();
  print($error);
}

//ログイン可否判定
if(isset($_SESSION['login']) == true){
  /*$_SESSION['login']が存在する場合は(=sqlの検索がヒットし、$_SESSION['login']にデータが書き込まれたら)
  メインコンテンツのページへ移動*/
  header("location:../Month");
}else{
  /*$_SESSION['login']が存在しない場合は(=sqlの検索がヒットせず、$_SESSION['login']にデータが書き込まれない)
  エラーページへ移動*/
  header("location:../Error");
}