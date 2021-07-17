<?php
// ----個別でデータを削除----
//ページリダレクトに必要なパラメータ
$userid = $_GET['userid'];
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
// $view = $_GET['view'];

//DBの内容を書き換えるパラメータ
$id = (int)$_GET['id'];
// ----個別でデータを削除----


// ----複数まとめて削除----
//ページリダレクトに必要なパラメータ
$userid = $_POST['userid'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
// $view = $_POST['view'];
// ----複数まとめて削除----

$view = 'delete';


//外部ファイル読み込み
require_once('../DBInfo.php');

try{
    //DB接続
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);

    //エラー処理
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //トランザクション開始
    $dbh->beginTransaction();
        // $sql = "UPDATE Memo_tags SET logic_delete = ? WHERE id = ?";
        $sql = "DELETE FROM Memo_tags WHERE id = ?";


        $stmt = $dbh->prepare($sql);
       

        if(isset($_POST['id'])){
            // 複数まとめて削除
            for ($i = 0; $i < count(@$_POST["id"]); $i++){
                $stmt->execute(array($_POST["id"][$i]));
            }
        }else{
            // 個別でデータを削除
            $stmt->execute(array($id));
            // header("location:../Day?userid=$userid&year=$year&month=$month&day=$day&view=$view");
        }
    //コミットで、テーブルの書き換え処理を確定  
    $dbh->commit();
    header("location:../Day?userid=$userid&year=$year&month=$month&day=$day&view=$view");
}catch(Exception $e){
    //DBとの接続ができていて、かつトランザクション中であればロールバックする
    if(isset($dbh) == true && $dbh->inTransaction() == true){
        //ロールバックで、テーブルの書き換え処理をキャンセル
        $dbh->rollBack();
    }
    print($e->getMessag());
}
$dbh = null;

