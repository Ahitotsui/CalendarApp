<?php
//ページリダレクトに必要なパラメータ
$userid = $_POST['userid'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];

//DBの内容を書き換えるパラメータ
$id = $_POST['id'];
$logic_delete  = $_POST['logic_delete'];


//外部ファイル読み込み
require_once('DBInfo.php');

try{
    //DB接続
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

    //エラー処理
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //トランザクション開始
    $dbh->beginTransaction();
        $sql = "UPDATE Memo_tags SET logic_delete = ? WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$logic_delete,$id]);
    //コミットで、テーブルの書き換え処理を確定  
    $dbh->commit();
    
}catch(Exception $e){
    //DBとの接続ができていて、かつトランザクション中であればロールバックする
    if(isset($dbh) == true && $dbh->inTransaction() == true){
        //ロールバックで、テーブルの書き換え処理をキャンセル
        $dbh->rollBack();
    }
    print($e->getMessag());
}
$dbh = null;

header("location:schedule.php?userid=$userid&year=$year&month=$month&day=$day");
