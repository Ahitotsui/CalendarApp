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

//関数validation():終了時刻>開始時刻でデータが送られたらエラー出す
function validation($start,$end){
    $str1 = (int)str_replace(':00','',$start);
    $str2 = (int)str_replace(':00','',$end);
    if($str2-$str1 <= 0){
        //エラーあり
        return false;
    }else{
        //エラーなし
        return true;
    }
}

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
                if(validation($start,$end) == true){
                    $stmt->execute([$start,$end,$title,$memo,$progress,$color,$id]);
                    header("location:schedule.php?userid=$userid&year=$year&month=$month&day=$day");
                }else if(validation($start,$end) == false){
                    print('<h3>入力にエラーがあります。</h3>');
                    print('<p>・終了時刻と開始時刻を同じ値で登録することはできません。</p>');
                    print('<p>・終了時刻は、開始時刻よりも後の時刻でないと登録できません。</p>');
                    print("<a HREF=\"javascript:history.back()\">戻る</a>");
                }
            //進捗ボタンでの処理
            }else if(isset($progFlag) == true){
                $sql = "UPDATE Memo_tags SET progress = ? WHERE id = ?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$progFlag,$id]);
                header("location:schedule.php?userid=$userid&year=$year&month=$month&day=$day");
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


