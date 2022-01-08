<?php
    session_start(); 
    if(isset($_SESSION['login']) == false){
        //このページのURLをコピーして他のブラウザで閲覧できないようにする
        header("location:../Error");
    }else{
        $userid = $_SESSION['login']['id'];
        $username = $_SESSION['login']['username'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet"><!-- googleフォント -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css"><!-- Font Awesome -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
    <style>
        .wrap{
            display: block;
            width: 350px;
            margin-top:20px;
            margin-left:auto;
            margin-right:auto;
            padding:20px;
            border:solid 1px #DDDDDD;
            border-radius:3px;
            box-shadow: 0px 2px 7px #3338;
            background:#fcfcfa;
            border:none;
        }

        /* スマホ表示 */
        @media (max-width:800px) {

            .wrap{
                width: 80%;
            }

        }
    </style>
    <title>マイページ</title>
</head>
<body>
    <!--ヘッダー領域-->
    <?php require_once('../Header/header.php'); ?>

    
    

    <?php 
        //DB接続
        require_once('../DBInfo.php');
        $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
        $sql = "SELECT * FROM login WHERE id=?" ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$userid]);

        // {
        // }
    ?>

    <div class="wrap">
        <h3 style="text-align:center;">マイページ</h3>
        <?php foreach($stmt as $row): ?>
            <div><?= $userid ?></div>
            <div><?= $row['name'] ?></div>
            <div><?= $row['username'] ?></div>
        <?php endforeach; ?>
    </div>
    
</body>
</html>