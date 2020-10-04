<?php
$userid = $_POST['userid'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
$start = $_POST['start'];
$end = $_POST['end'];
$title = $_POST['title'];
//メモはテキストデータ入力のためサニタイズ処理する
$memo = htmlspecialchars($_POST['memo']);
$progress = $_POST['progress'];
$color = $_POST['color'];
$delete = $_POST['delete'];

//詳細ページから追加された場合はこの値を受け取り、リダイレクトの判定に使用
$redirect = $_POST['redirect'];

//外部ファイル読み込み
require_once('DBInfo.php');

try{
    //DB接続
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

    //エラー処理
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //トランザクション開始
    $dbh->beginTransaction();
        $sql = "INSERT INTO Memo_tags VALUES(Null,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$userid,$year,$month,$day,$start,$end,$title,$memo,$progress,$color,$delete]);

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

//リダイレクト
if($redirect == "true"){
    header("location:schedule.php?userid=$userid&year=$year&month=$month&day=$day");
}else{
    header("location:calendar.php?year={$year}&month={$month}");
}

