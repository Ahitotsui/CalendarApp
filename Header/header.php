<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css">

    <!-- bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">

    <!-- bootstrap -->
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet" media="screen">

    <title>Document</title>
    <style>

        header{
            display: flex;
            width:100%;
            height:40px;
            background-color:#000000;
        }

        .right{
            width: 200px;
        }

        .middle{
            width:100%;
            /* background-color:red; */
        }

        .left{
            width: 200px;
        }

        /* ヘッダーのロゴ画像 */
        #logo{
            width: 120px;
            display: block;
            margin-top: 2px;
            margin-left: auto;
            margin-right: auto;
        }

        /*ログインしたユーザー名のスタイル*/
        #login{
            font-size: 0.7em; 
            color:#FFFFFF;
        }

        /*ログインしたユーザー名を強調*/
        #username{
            color:#AEFFBD;
            margin-left:2px;
            margin-right:2px;
        }

        .dropdown-menu{
            margin-right:60px;
        }

    </style>
</head>
<body>

    <header>

        <div class="right">

        </div>

        <div class="middle">
            <a href="../Month/?year=<?php print($year) ?>&month=<?php print($month) ?>">
                <!-- ヘッダーのロゴ画像 -->
                <img src="../img/LOGO.png" alt="" id="logo">
            </a>
        </div>

        <div class="btn-group left">
            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="login">
                <i class="fas fa-user"></i><span id="username"><?php print($_SESSION['login']['name']); ?></span>さん
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#">ログイン中</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../MyPage">マイページ</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../logout.php">ログアウト</a>
            </div>
        </div>

    </header>

    <!-- bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- bootstrap -->


</body>
</html>