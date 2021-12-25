<?php 

    require_once('../DBInfo.php');
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
    $sql = "SELECT * FROM Memo_tags WHERE month=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(12);

    foreach($stmt as $row){
        $array = [
            'yaer' => $row['year'],
        ];
    }

    var_dump($array);

?>


