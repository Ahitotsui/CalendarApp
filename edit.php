<?php
//ページリダレクトに必要なパラメータ
$userid = $_POST['userid'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];

//DBの内容を書き換えるパラメータ
$id = $_POST['id'];
$start = $_POST['start'];
$end = $_POST['end'];
$title = $_POST['title'];
$memo = $_POST['memo'];
$progress = $_POST['progress'];
$color = $_POST['color'];

//進捗ボタンで編集する際に受け取るパラメータ
$progFlag = $_POST['progFlag'];

//外部ファイル読み込み
require_once('DBInfo.php');

try{
    //DB接続
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

    //エラー処理
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //トランザクション開始
    $dbh->beginTransaction();
        //編集フォームでの処理
        if(isset($progFlag) == false){
            $sql = "UPDATE Memo_tags SET start_time = ? , end_time = ? , title = ? , memo = ? , progress = ? , color = ? WHERE id = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$start,$end,$title,$memo,$progress,$color,$id]);
        //進捗ボタンでの処理
        }else if(isset($progFlag) == true){
            $sql = "UPDATE Memo_tags SET progress = ? WHERE id = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$progFlag,$id]);
        }
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
