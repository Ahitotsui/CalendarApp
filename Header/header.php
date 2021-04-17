<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css">
    <title>Document</title>
    <style>
        /*ヘッダーの背景*/
        header,#header{
        width:100%;
        height:50px;
        background-color:#000000;
        }

        /*タイトルのリンク線消す*/
        #TitleBackLink{
        text-decoration: none;
        }

        /* ヘッダーのロゴ画像 */
        #logo{
        width: 150px;
        display: block;
        margin-top: 2px;
        margin-left: auto;
        margin-right: auto;
        }

        /*ログインしたユーザー名のスタイル*/
        #login{
        /* height:50px;*/
        font-size: 0.7em; 
        color:#FFFFFF;
        background:#555555;
        /*-------------------上下左右の中央寄せ------------------------*/
        /* display: flex;fFlexコンテナ化 */
        /* align-items: center;f縦方向の位置を中央に */
        /* justify-content: center;横方向のアイテム位置を中央に */
        /*-------------------上下左右の中央寄せ------------------------*/
        }

        /*ログインしたユーザー名を強調*/
        #username{
        color:#AEFFBD;
        margin-left:2px;
        margin-right:2px;
        }

        #logout{
        height: 50px;
        font-size: 13px;
        color:#FFFFFF;
        /*-------------------上下左右の中央寄せ------------------------*/
        display: flex;/*fFlexコンテナ化*/
            align-items: center;/*f縦方向の位置を中央に*/
        justify-content: center;/*横方向のアイテム位置を中央に*/
        /*-------------------上下左右の中央寄せ------------------------*/
        }

        #userInfo{
            margin-top: 10px;
            margin-left: 45px;
            width: 50px;
        }
    </style>
</head>
<body>
    <div id="header">
        <div id="top" class="row">
            <div id="Htitle" class="col col-md-2"></div>
            <div id="Htitle" class="col col-md-8">
            <a id="TitleBackLink" href="../Month/?year=<?php print($year) ?>&month=<?php print($month) ?>">
                <!-- ヘッダーのロゴ画像 -->
                <img src="../img/LOGO.png" alt="" id="logo">
            </a>
            </div>

            <!-- <div id="Hlogin" class="col col-md-1">
                <p id=login>
                    <i class="fas fa-user"></i><span id="username"><?php print($_SESSION['login']['name']); ?></span>さん
                </p>
            </div>
            <div id="Hlogout" class="col col-md-1"><a id="logout" href="logout.php">ログアウト</a></div> -->
            <!-- <div class="col col-md-1"></div> -->
            <div class="col col-md-2">
                <div class="btn-group" id="userInfo">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="login">
                        <i class="fas fa-user"></i><span id="username"><?php print($_SESSION['login']['name']); ?></span>さん
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">ログイン中</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">ログアウト</a>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</body>
</html>