<?php
    date_default_timezone_set('Japan');
    //今現在の年を取得
    $TodayYear = date("Y");

    //今現在の月を"先頭の0"無しで取得
    $TodayMonth = date("n");

    //今現在の日付を取得
    $today = date("d");
?>

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


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300&display=swap" rel="stylesheet">


    <title>Document</title>
    <style>

        body{
            /* background: #000; */
        }

        button{
            background-color: transparent;
            border: none;
            cursor: pointer;
            outline: none;
            padding: 0;
            appearance: none;
        }

        button:focus{
            outline:none;
        }

        .common_header{
            position: relative;
            display: flex;
            width:100%;
            height:40px;
            border-bottom:solid 1px #888;
        }

        .logo_text{
            display: block;
            font-family: 'Corinthia', cursive;
            color:#444444;
            font-size:30px;
            padding-top:-10px;
        }

        .left{
            width: 200px;
        }

        .middle{
            width:100%;
        }

        .right{
            display: flex;
            width: 140px;
        }

        /* ヘッダーのロゴ画像 */
        #logo{
            width: 120px;
            display: block;
            margin-top: 2px;
            margin-left: auto;
            margin-right: auto;
        }

        .login_user_name{
            width: 100px;
            font-size: 0.7em; 
            text-align:center;
            line-height:40px;
        }

        .menu_btn{
            display: block;
            width: 38px;
            height:38px;
            padding:6px;
            margin:0 0 0 auto;
            background: #fff;
            border:none;
        }

        .bar1,.bar2,.bar3{
            width: 100%;
            height:5px;
            background:#000;
            margin-bottom:5px;
            border-radius:2.5px;
        }

        .drawer-menu {
            position: fixed;
            top: 0;
            right: -310px;
            height: 100%;
            width: 300px;
            transition: .5s;
            border-left:solid 1px #888;
            background-color:#fff;
            box-shadow: 5px 10px 10px 10px rgba(0,0,0,0.4);
            z-index: 9999;
        }

        .menu_toggle_close{
            width: 40px;
            line-height: 38px;
            text-align:center;
            border:solid 1px #888;
            border-left:none;
            font-size:28px;
            color:#888;
            cursor: pointer;
        }

        .display_setting_select{
            display: block;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: solid 1px #555;
            border-radius:3px;
            outline: none;
            background: transparent;
            width: 90%;
            height:50px;
            margin-left: auto;
            margin-right: auto;
            text-align:center;
            position:relative;
        }

        .display_setting_select:after{
            contents:'X';
            left:0px;
        }

        .inner_menu_ul{
            list-style:none;
            margin-top:20px;
        }

        .inner_menu_ul li{
            width: 100%;
            line-height:40px;
            text-align:center;
        }

        .inner_menu_ul li a{
            color: #555;
        }


        @media screen and ( max-width:800px ){
        }
    </style>
</head>
<body>

    <header class="common_header">

        <div class="left">
            <a href="../Month/?year=<?php print($year) ?>&month=<?php print($month) ?>" class="logo_text">
                <!-- ヘッダーのロゴ画像 -->
                <!-- <img src="../img/LOGO.png" alt="" id="logo"> -->
                Calendar
            </a>
        </div>

        <div class="middle">
        </div>

        <div class="right">

            <div class="login_user_name">
                <i class="fas fa-user"></i><span id="username"><?php print($_SESSION['login']['name']); ?></span>さん
            </div>
            <button type="button" class="menu_btn" id="menu_btn">
                <div class="bar1"></div>
                <div class="bar2"></div>
                <div class="bar3"></div>
            </button>
                    <!-- <a class="dropdown-item" href="#">ログイン中</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../MyPage">マイページ</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../logout.php">ログアウト</a> -->
                    <div style="display:none;">
                    <a href="../Day/?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list">
                        <button class="page_select_btn" id="day_link">日</button>
                    </a>
                    <a href="../Week/?year=<?= $TitleYear ?>&month=<?= $Titlemonth ?>&day=<?= date("j") ?>">
                        <button class="page_select_btn" id="day_link">週</button>
                    </a>
                    <a href="../Day/?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list">
                        <button class="page_selected_now_btn" id="day_link">月</button>
                    </a>
                    <a href="../Day/?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list">
                        <button class="page_select_btn" id="day_link">年</button>
                    </a>
                    </div>        
            
            <div class="drawer-menu" id="drawer-menu">
                <div id="menu_toggle_close" class="menu_toggle_close">×</div>

                <div>
                    <a href="../Day/?userid=<?=print($_SESSION['login']['userid']);?>&year=<?=date("Y")?>&month=<?=date("n")?>&day=<?=date("d")?>&view=list">
                        <button>今日</button>
                    </a>
                </div>

                <form id="display_setting">
                    <select name="select" class="display_setting_select">
                        <option value="0"><i class="far fa-sun"></i> ライト</option>
                        <option value="1"><i class="fas fa-moon"></i> ダーク</option>
                    </select>
                </form>

                <ul class="inner_menu_ul">
                    <li><a href="../MyPage">マイページ</a></li>
                    <li><a href="">メニュー2</a></li>
                    <li><a href="">メニュー3</a></li>
                    <li><a href="">メニュー4</a></li>
                    <li><a href="">メニュー5</a></li>
                </ul>
            </div>

        </div>
        

    </header>

   

    

    <!-- bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- bootstrap -->

    <script>
        window.onload = function(){
            document.getElementById('menu_btn').onclick = function(){
                document.getElementById('drawer-menu').style.right = '0px';
            }

            document.getElementById('menu_toggle_close').onclick = function(){
                document.getElementById('drawer-menu').style.right = '-310px';
            }

            document.getElementById('display_mode_target').style.backgroundColor =  localStorage.getItem('backgroundcolor');
            document.getElementById('display_setting').select.onchange = function(){
                var display_mode = document.getElementById('display_setting').select.value;
                if(display_mode == 0){
                    localStorage.removeItem('backgroundcolor');
                    localStorage.setItem('backgroundcolor', '#fff');
                    document.getElementById('display_mode_target').style.backgroundColor =  localStorage.getItem('backgroundcolor');
                }else if(display_mode == 1){
                    localStorage.removeItem('backgroundcolor');
                    localStorage.setItem('backgroundcolor', '#000');
                    document.getElementById('display_mode_target').style.backgroundColor =  localStorage.getItem('backgroundcolor');
                }
            }
            

        }
    </script>

</body>
</html>