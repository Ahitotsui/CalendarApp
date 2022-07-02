<?php 

    require_once('../DBInfo.php');
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
    $sql = "SELECT * FROM Memo_tags WHERE year=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([2021]);

    foreach($stmt as $row){
        $data .='<div style="display:flex;">
                    <div>' .$row['title']. '</div>
                    <div>' .$row['color']. '</div>
                 </div>';
    }

    // var_dump($array);
    $json=json_encode(['data'=>$data],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    echo $json;

?>