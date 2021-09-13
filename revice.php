<!--ログインデータ使うのでセッション開始-->
<?php
    session_start(); 
    if(isset($_SESSION['login']) == false){
        //このページのURLをコピーして他のブラウザで閲覧できないようにする
        header("location:../Error");
    }else{
        $userid = $_SESSION['login']['username'];
    }

    if(!$_GET['year'] && !$_GET['month']){
        $year = date('Y');
        $month = date('n');
        header("location:/calendarapp/revice.php?year=$year&month=$month");
    }

    $year = $_GET['year'];
    $now_month = $_GET['month'];
    $pre_month = $now_month -1;

    //DB
    require_once('./DBInfo.php');
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
    $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? ORDER BY start_time";
    $stmt = $dbh->prepare($sql);


    //前月の空白マスの数を決めるため、$iniを定義
    @$ini = 0;
    //月ごとの1日が何曜日から始まるかを取得
    $pre_space_day = date('D',strtotime("$year-$now_month-01"));
    //曜日ごとに、前月の空白マスを設定する
    if($pre_space_day == "Mon"){
        $ini = 0;
    }else if($pre_space_day == "Tue"){
        $ini = 1;
    }else if($pre_space_day == "Wed"){
        $ini = 2;
    }else if($pre_space_day == "Thu"){
        $ini = 3;
    }else if($pre_space_day == "Fri"){
        $ini = 4;
    }else if($pre_space_day == "Sat"){
        $ini = 5;
    }else if($pre_space_day == "Sun"){
        $ini = 6;
    }

    $br = $ini;


    date_default_timezone_set('Japan');

    function now($Y,$M,$D){
        if($Y == date("Y") && $M == date("n") && $D == date("d")){
            echo 'today';
        }        
    }
  

    function DayOfWeek($Y,$M,$D){
        $judge = date('D',strtotime("$Y-$M-$D"));
        
        if($judge == "Sat"){
            echo 'saturday';
        }else if($judge == "Sun"){
            echo 'sunday';
        }    
    }

    //前月の日数を求める  
    $pre_day_count = date( 't' , strtotime($year . "/" . $pre_month . "/01"));  

    //今月の日数を求める  
    $now_day_count = date( 't' , strtotime($year . "/" . $now_month . "/01"));  

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaisei+Opti&family=Klee+One&display=swap" rel="stylesheet">


    <style>
        body{
            margin:0;
            font-family: 'Kaisei Opti', serif;
            font-family: 'Klee One', cursive;
            color:#555555;
        }

        header{
            display: flex;
            width: 100%;
            height:40px;
            box-shadow: 0px 1px 6px #555555;
        }

        header .inner_left{
            width:40px;
            height:40px;
        }

        header .inner_middle{
            width: 100%;
            text-align:center;
        }

        header .inner_right{
            width:40px;
            height:40px;
        }

        main{
            padding-left:50px;
            padding-right:50px;
        }

        table{
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse; 
            border:solid 0.1px #888888;
        }

        thead th{
            font-size:10px;
            line-height:10px;
        }

        .day_cell_wrap{
            /* width: 30%; */
            height:100px;
        }

        .day_inner_wrap{
            width: 100%;
            height:80px;
            overflow:hidden;
            overflow-y: scroll;
        }

        .today{
            background: #000;
            color: #fff;
        }

        .saturday{
            background: #B0C4DE;
            /* border-color:#B0C4DE; */
        }

        .sunday{
            background: #FFC0CB;
        }


        .day_wrap{
            width: 20px;
            height:20px;
            font-size: 10px;
            border-radius: 11px;
            /*-------------------上下左右の中央寄せ------------------------*/
            display: flex;/*fFlexコンテナ化*/
            align-items: center;/*f縦方向の位置を中央に*/
            justify-content: center;/*横方向のアイテム位置を中央に*/
            /*-------------------上下左右の中央寄せ------------------------*/
        }

        .tags{
            font-size:8px;
            width: 100%;
            height:15px;
            background: #CCCCCC;
            overflow:hidden;
            margin-top:0px;
            margin-bottom:1px;
            border-radius:2px;
        }

        .month_links_wrap{
            display: flex;
            width: 480px;
            /* background: #CCCCCC; */
            margin-left:auto;
            margin-right:auto;
            margin-top:30px;
        }

        .month_links{
            display: block;
            width: 20px;
            line-height:20px;
            font-size: 12px;
            text-align:center;
            border-radius: 11px;
            /* background:#000;
            color:#fff; */
            margin:10px;
            text-decoration: none;
        }

    </style>
    <title>Document</title>
</head>
<body>

    <header>
        <div class="inner_left">left</div>
        <div class="inner_middle">LOGO</div>
        <div class="inner_right">user</div>
        <div class="inner_left">menu</div>
    </header>

    

    <main>
        <h5><?= $year ?>年<?= $now_month ?>月</h5>

        <table border=1>

            <thead>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
                <th>日</th>
            </thead>

            <tr>
            <?php for($pre=1;$pre<=$ini;$pre++) : ?>
                <?php $pre_day = $pre_day_count - ($ini - $pre) ; ?>
                <td class="day_cell_wrap <?php DayOfWeek($year,$pre_month,$pre_day); ?> ">
                    <div class="day_inner_wrap">
                        <div>
                            <small>
                                <?= $pre_month ?>/<?= $pre_day ?>
                            </small>
                        </div>                   
                    </div>
                </td>
            <?php endfor ; ?>


            <?php for($i=1;$i<=$now_day_count;$i++) : ?>
                <?php $br++ ; ?>
                    
                <td class="day_cell_wrap <?php DayOfWeek($year,$now_month,$i); ?>">

                    <div>
                        <div class="day_wrap <?php now($year,$now_month,$i); ?>"><?= $i ?></div>
                    </div>
                    
                    <div class="day_inner_wrap">

                        <?php $stmt->execute([$_SESSION['login']['username'],$year,$now_month,$i]); ?>
                        
                        <?php foreach($stmt as $row) : ?>
                            <p class="tags" style="background:<?= $row['color'] ?>"><?= $row['title'] ?></p>
                        <?php endforeach ; ?>

                    </div>
                </td>
                    
                <?php if($br%7 == 0) echo '</tr>'; ?>         
            <?php endfor ; ?>
            
        </table>
    </main>

    <div class="month_links_wrap">
        <?php for($m=1;$m<=12;$m++) : ?>
            <a class="month_links" href="/calendarapp/revice.php?year=<?= $year ?>&month=<?= $m ?>"><?= $m ?></a>
        <?php endfor ; ?>
    </div>
    
</body>
</html>

