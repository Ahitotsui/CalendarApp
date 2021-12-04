<?php

//DBの内容を書き換えるパラメータ
$progFlag = $_POST['progFlag'];
$id = $_POST['id'];

//外部ファイル読み込み
require_once('../DBInfo.php');

try{
    //DB接続
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

    //エラー処理
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //トランザクション開始
    $dbh->beginTransaction();
        
        //進捗ボタンでの処理
        $sql = "UPDATE Memo_tags SET progress = ? WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$progFlag,$id]);
        // header("location:./Day?userid=$userid&year=$year&month=$month&day=$day&view=$view");
        
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


