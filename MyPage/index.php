<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet"><!-- googleフォント -->
    <link rel="stylesheet" href="index.css">
    <script src="jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css"><!-- Font Awesome -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
    <title>マイページ</title>
</head>
<body>
    <!--ヘッダー領域-->
    <?php require_once('../Header/header.php'); ?>
    
    <h1>マイページ</h1>

    <?php 
        //DB接続
        require_once('../DBInfo.php');
        $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
        $sql = "SELECT * FROM login WHERE id=1" ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        foreach($stmt as $row){
            print($row['name']);
        }
    ?>
    
</body>
</html>