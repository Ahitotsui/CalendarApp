<?php 

    require_once('../DBInfo.php');
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
    $sql = "SELECT * FROM Memo_tags WHERE year=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([2021]);

    foreach($stmt as $row){
        $i++;
        $array[$i] = [
            'yaer' => $row['year'],
            'title' => $row['title'],
            'color' => $row['color'],
        ];
        // print("<div>{$row['title']}</div>");
    }

    // var_dump($array);
    $json=json_encode($array,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    echo $json;

?>


