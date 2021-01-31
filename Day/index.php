<?php
    //セッション開始
    session_start();
    $userid = $_SESSION['login']['username']; 

    //ページ表示に必要なパラメータの受け取り
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    date_default_timezone_set('Japan');
    //今現在の年を取得
    $TodayYear = date("Y");

    //今現在の月を"先頭の0"無しで取得
    $TodayMonth = date("n");

    //今現在の日付を取得
    $today = date("d");

    // 今現在の時刻(h)を取得
    $todayTime = date("H");

    //セキュリティ対策
    function Security($userid,$year,$month,$day,$viewMode){

        if($userid != $_SESSION['login']['username']){
            return false;
        }else if($year < 2019 || ctype_digit($year) == false){
            return false;
        }else if($month < 1 || $month >12 || ctype_digit($month) == false){
            return false;
        }else if($day < 1 || $day > date( 't' , strtotime($year . "/" . $month . "/01")) || ctype_digit($day) == false){
            return false;
        }else if($_GET['view'] != "list" && $_GET['view'] != "table"){
            return false;
        }else{
            return true;
        }
    }

    if(isset($_SESSION['login']) == false){
        //このページのURLをコピーして他のブラウザで閲覧できないようにする
        header("location:error.html");
    }else if(Security($userid,$year,$month,$day,$viewMode) == false){
        //セキュリティ対策でエラー検知したら、強制的にデフォルト表示にする
        header("location:schedule.php?userid=$userid&year=$TodayYear&month=$TodayMonth&day=$today&view=list");
    }else{
        $userid = $_SESSION['login']['username'];
    }

    //検索条件のパラメータ受け取り
    if(isset($_POST['all_disp']) == true && $_POST['all_disp'] != ""){
        $dispMode =  $_POST['all_disp'];
        $selectedType1 = 'selectedType1';
    }else if(isset($_POST['unfini_disp']) == true && $_POST['unfini_disp'] != ""){
        $dispMode =  $_POST['unfini_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=0 ORDER BY start_time";
        $selectedType2 = 'selectedType2';
    }else if(isset($_POST['fini_disp']) == true && $_POST['fini_disp'] != ""){
        $dispMode =  $_POST['fini_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=1 ORDER BY start_time";
        $selectedType3 = 'selectedType3';
    }else if(isset($_POST['can_disp']) == true && $_POST['can_disp'] != ""){
        $dispMode =  $_POST['can_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=2 ORDER BY start_time";
        $selectedType4 = 'selectedType4';
    }else if(isset($_POST['keyword']) == true && $_POST['keyword'] != ""){
        $dispMode = "Keyword";
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and title like '%{$_POST['keyword']}%' ORDER BY start_time";
    }else{
        $dispMode = "All";
        $selectedType1 = 'selectedType1';
    }

    //ページ遷移に使うURLを変数で扱う
    $URL = "?userid=$userid&year=$year&month=$month&day=$day";

    //リスト・タイムテーブルの表示モードを選択するためのパラメータ
    $viewMode = $_GET['view'];

    //リスト・タイムテーブルの表示モードを選択ボタンのCSSで使うid名
    if($viewMode == "list"){
        $tabStyle1 = "tabStyle1";
    }else if($viewMode == "table"){
        $tabStyle2 = "tabStyle2";
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet"><!-- googleフォント -->
    <link rel="stylesheet" href="index.css">
    <script src="jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css"><!-- Font Awesome -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
    <title><?php print($year); ?>/<?php print($month); ?>/<?php print($day); ?></title>
</head>
<body>

    <!--ヘッダー-->
    <?php require_once('../Header/header.php'); ?>
    <div>
        <!-- フィルタ&検索機能 -->
        <div class="row" id="search">

            <div class="coll col-md-4">
                <p id="dispSelectedDay"><i class="fas fa-tasks"></i>　<?php print($year); ?>年<?php print($month); ?>月<?php print($day); ?>日 の予定 </p>
            </div>

            <div id="disp_filter" class="coll col-md-4">
                <!-- <div> -->
                <form class="searchBtns" action="<?php print($URL ."&view=". $viewMode); ?>" method="post">
                    <input type="hidden" name="all_disp" value="All">
                    <button type="submit" id="filBtn1" class="<?php print($selectedType1); ?>">全て</button>
                </form>
                <form class="searchBtns" action="<?php print($URL ."&view=". $viewMode); ?>" method="post">
                    <input type="hidden" name="unfini_disp" value="0">
                    <button type="submit" id="filBtn2" class="<?php print($selectedType2); ?>">未了</button>
                </form>
                <form class="searchBtns" action="<?php print($URL ."&view=". $viewMode); ?>" method="post">
                    <input type="hidden" name="fini_disp" value="1">
                    <button type="submit" id="filBtn3" class="<?php print($selectedType3); ?>">完了</button>
                </form>
                <form class="searchBtns" action="<?php print($URL ."&view=". $viewMode); ?>" method="post">
                    <input type="hidden" name="can_disp" value="2">
                    <button type="submit" id="filBtn4" class="<?php print($selectedType4); ?>">キャンセル</button>
                </form>
                <!-- </div> -->
            </div>

            <div class="coll col-md-4">
                <form action="<?php print($URL ."&view=". $viewMode); ?>" method="post">
                <div class="input-group">
                    <span class="input-group">
                        
                        <input class="form-control" type="text" placeholder="予定のタイトルでさがす" name="keyword" value="">
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </span>
                </div>
                </form>
            </div>
        </div>
    </div>

    <main>
        <div class="row">
            <div class="coll col-md-3">
            <?php 
                $link_prevM = $month - 1;
                $link_nextM = $month + 1;
                $link_prevyear = $year;
                $link_nextyear = $year;
                if($link_prevM == 0){
                    $link_prevM = 12;
                    $link_prevyear = $year - 1;
                }else if($link_nextM == 13){
                    $link_nextM = 1;
                    $link_nextyear = $year + 1;
                }
                print("<a id=\"prevDay\" href=\"?userid=$userid&year=$link_prevyear&month=$link_prevM&day=1&view=$viewMode\"><i class=\"fas fa-angle-double-left\"></i>前月</a>");
                
                print("<a id=\"nextDay\" href=\"?userid=$userid&year=$link_nextyear&month=$link_nextM&day=1&view=$viewMode\">翌月<i class=\"fas fa-angle-double-right\"></i></a>");
            ?>

            <hr style="width:100%; height:0.1px; background-color:gray;">

            <?php
                //DB接続
                require_once('../DBInfo.php');
                $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
                $sql_1 = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? ORDER BY start_time";
                $stmt = $dbh->prepare($sql_1);

                /*-------------------------------------------カレンダー表示領域---------------------------------------------------*/
                //テーブルヘッド
                print("<table id=\"mainTable\" border=\"0\">");
                print("<thead id=\"tbhead\">");
                print("<td class=\"tdtop\">月 <br/><span class=\"small\">-MON-</span></td>");
                print("<td class=\"tdtop\">火 <br/><span class=\"small\">-TUE-</span></td>");
                print("<td class=\"tdtop\">水 <br/><span class=\"small\">-WED-</span></td>");
                print("<td class=\"tdtop\">木 <br/><span class=\"small\">-THU-</span></td>");
                print("<td class=\"tdtop\">金 <br/><span class=\"small\">-FRI-</span></td>");
                print("<td class=\"tdtop\" id=\"sat\">土 <br/><span class=\"small\">-SAT-</span></td>");
                print("<td class=\"tdtop\" id=\"sun\">日 <br/><span class=\"small\">-SUN-</span></td>");
                print("</thead>");

                //前月の空白マスの数を決めるため、$iniを定義
                @$ini = 0;
                //月ごとの1日が何曜日から始まるかを取得
                $iniweek = date('D',strtotime("$year-$month-01"));
                //曜日ごとに、前月の空白マスを設定する
                if($iniweek == "Mon"){
                    $ini = 0;
                }else if($iniweek == "Tue"){
                    $ini = 1;
                }else if($iniweek == "Wed"){
                    $ini = 2;
                }else if($iniweek == "Thu"){
                    $ini = 3;
                }else if($iniweek == "Fri"){
                    $ini = 4;
                }else if($iniweek == "Sat"){
                    $ini = 5;
                }else if($iniweek == "Sun"){
                    $ini = 6;
                }

                //もし、1日が月曜からスタートでは無い場合は空白のtdタグを最初に作る
                print("<tr>");
                    //前の月の最終日が何日かを取得し、$pervMonthLastに格納
                    $prevMonth = $month - 1;
                    if($prevMonth == 0){
                    //1月は参照するのは前年なので　$refYear = $TitleYear - 1;　とする。
                    $refYear = $year - 1;
                    //1月は$prevMonth = 0になるので値を前月の12に直す
                    $prevMonth = 12;
                    $pervMonthLast = date( 't' , strtotime($refYear . "/" . $prevMonth . "/01"));
                    }else{
                    $pervMonthLast = date( 't' , strtotime($TitleYear . "/" . $prevMonth . "/01")); 
                    }
                    
                    for($i=0;$i<$ini;$i++){
                        //前の月の日付けを空白マスの数分算出し、$prevDayDispに格納
                        $prevDayDisp = ($pervMonthLast - ($ini - 1)) + $i;
                        // print("<td class=\"tdPreMon\" valign=\"top\" style=font-size:10px;text-align:center>$prevMonth / $prevDayDisp</td>");
                        print("<td></td>");
                    }

                //その月の日数を求める  
                $lastday = date( 't' , strtotime($year . "/" . $month . "/01"));  

                //横に7個tdタグが並んだら改行するよう、$brを定義。また、初期値は前月の空白の数からスタートする    
                $br = $ini;
                //以下カレンダーの左上に表示する日付や、以下の日付マスの表示を構成するHTMLのタグのidやclassに用いるため$dayを定義 
                $day_chara = 0;

                for($i=1;$i<=$lastday;$i++){

                    $br++;
                    $day_chara++;
                
                    //日にちを表示するtdタグ
                    if($year == $TodayYear && $month == $TodayMonth && $day_chara == $today){
                        //現在の日にちに背景色をつけるためのidをつけるため判別
                        // print("<td class=\"daysTd\" bgcolor=#DDDDDD>");
                        print("<td class=\"daysTd\" style=border:solid;border-width:1px;>");
                    }else{
                        print("<td class=\"daysTd\">");
                    }

                    $stmt->execute([$_SESSION['login']['username'],$year,$month,$day_chara,"false"]);
                    if($stmt->rowCount() > 0){
                        print("<a href=\"?userid=$userid&year=$year&month=$month&day=$day_chara&view=list\">{$day_chara}</a>");
                    }else{
                        print("<p style=display:inline>{$day_chara}</p>");
                    }

                    print("</td>");

                    //横に7個tdタグが並んだら改行
                    if($br%7 == 0){
                    print("</tr>");
                    }
                }
                print("</table>");

                $dbh = null;
                ?>
                <hr style="width:100%; height:0.1px; background-color:gray;">
                <button class="btn btn-primary btn-lg btn-block"><i class="fas fa-plus-circle"></i>　予定を追加</button>
            </div>

            <div class="coll col-md-9">
                <!-- 表示モード切替機能 -->
                <div id="dispTabs">
                    <a class="dispTab" id="<?php print($tabStyle1); ?>" href="<?php print($URL); ?>&view=list">リスト</a>
                    <a class="dispTab" id="<?php print($tabStyle2); ?>" href="<?php print($URL); ?>&view=table">タイムテーブル</a>
                </div>

                
                <?php 
                    //DB接続
                    require_once('../DBInfo.php');
                    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
                    if($dispMode ==  "All"){
                        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? ORDER BY start_time";
                    }

                    $stmt = $dbh->prepare($sql);
                    $stmt->execute([$userid,$year,$month,$day,'false']);
                ?>

                <p style=text-align:center;padding-top:5px;height:15px>[全 <?php print($stmt->rowCount())?> 件]</p>

                <div id="right_tags">
                <?php
                    if($stmt->rowCount() == 0 && $dispMode ==  "All"){
                        print("<div class=\"no_data\">まだ予定はありません</div>");
                    }else if($stmt->rowCount() != 0){
                        // <!-- 簡易リスト表示 -->
                        $num = -1;
                        foreach($stmt as $row){
                            $num++;
                            $S_time = preg_replace('/:00/','',$row['start_time'],1);
                            $E_time = preg_replace('/:00/','',$row['end_time'],1);
                            $title = htmlspecialchars($row['title']);
                            $memo = nl2br(htmlspecialchars($row['memo']));

                            // メモがない場合はメッセージ表示
                            if(strlen(trim($memo)) == 0){
                                $memo = '詳細メモはありません。';
                            }

                            //進捗ステータス文字列に変換
                            if($row['progress'] == 0){
                                $progress = "未了";
                            }else if($row['progress'] == 1){
                                $progress = "完了";
                            }else if($row['progress'] == 2){
                                $progress = "キャンセル";
                            }

                            //未了・完了のボタンの切替で使用
                            if($row['progress'] == 0){
                                $toggle = 1;
                                $disabled = "";
                                $class = "unfinish";
                            }else if($row['progress'] == 1){
                                $toggle = 0;
                                $disabled = "";
                                $class = "finish";
                            }else if($row['progress'] == 2){
                                $disabled = "disabled";
                                $class = "cancel";
                            }

                            $color = $row['color'];
                            $id = $row['id'];

                            //予定の数が１つの時とそうで無い時でpostで送るaタグのhrefを変える
                            if($stmt->rowCount() == 1){
                                //１つの時は、配列にするとpostできなかった
                                $href = "javascript:form1.submit()";
                            }else{
                                $href = "javascript:form1[{$num}].submit()";
                            }

                            

                            $list_view = <<<EOF
                            <a data-toggle="modal" href="#memo{$id}" style=text-decoration:none;color:#000;>
                                <div class="list_view" style="background-color:{$color};">

                                <div class="tagMenus" style="width:8%">
                                    <form class="progBtn" action="../edit.php" method="post" name="form1">
                                        <input type="hidden" name="userid" value="$userid">
                                        <input type="hidden" name="year" value="$year">
                                        <input type="hidden" name="month" value="$month">
                                        <input type="hidden" name="day" value="$day">
                                        <input type="hidden" name="view" value="$viewMode">
                                        <input type="hidden" name="id" value="$id">
                                        <input type="hidden" name="progFlag" value="$toggle">
                                        <a href="$href"><button class="$class" $disabled>{$progress}</button></a>
                                    </form>
                                </div>

                                    <div class="tagMenus" style="width:10%;font-size:12px">
                                        <p class="list_time"><i class="far fa-clock"></i>{$S_time}〜{$E_time}</p>
                                    </div>

                                    <div class="tagMenus" style="width:70%">
                                        <p class="list_title">{$title}</p>
                                    </div>

                                    <div class="tagMenus" style="width:10%;text-align:right">
                                        <!--<span data-toggle="tooltip" data-placement="bottom" title="詳細情報を見る">
                                            <a data-toggle="modal" href="#memo{$id}" class="detail"><i id="operate1" class="fas fa-info-circle"></i></a>
                                        </span>-->
                                        <span data-toggle="tooltip" data-placement="bottom" title="内容を編集する">
                                            <a href="../Edit/edit_form.php?ID={$id}&view=$viewMode" class="edit"><i id="operate2" class="fas fa-pen"></i></a>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="bottom" title="この予定を削除する">
                                            <a data-toggle="modal" href="#delete" class="delete" id="{$id}"><i id="operate3" class="fas fa-trash-alt"></i></a>
                                        </span>
                                    </div>
                                    
                                    <div class="modal" id="memo{$id}">
                                    <form class="deleteform" action="delete.php" method="post">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <p class="list_titles" style=background-color:{$color}>{$title}</p>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="Deleteform" class="deletepops">
                                                        <p class="modal-times"><i class="far fa-clock"></i>  {$S_time}〜{$E_time}</p>
                                                        <p class="modal_memo">{$memo}</p> 
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </a>
                            EOF;

                            if($viewMode == "list"){
                                print($list_view);
                            }
                        }


                        // <!-- タイムテーブル表示 -->
                        if($viewMode == "table"){
                            print("<table id=\"time_table\" border=1>");
                            print("<thead class=\"timesThead\">");
                            for($i=0;$i<=23;$i++){
                                
                                //現在時刻の時は背景色つける
                                if($year == $TodayYear && $month == $TodayMonth && $day == $today && $todayTime == $i){
                                    //時刻の文字がリンクになっていて、新規登録ページのモーダルを開く
                                    print("<th id=\"timesNow\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"クリックで予定追加\"><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add_now\" id=\"{$i}\">{$i}:00</a></th>");
                                }else{
                                    //時刻の文字がリンクになっていて、新規登録ページのモーダルを開く
                                    print("<th class=\"timesTh\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"クリックで予定追加\"><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">{$i}:00</a></th>");
                                }
                            }
                            print("</thead>");

                            $stmt->execute([$userid,$year,$month,$day,'false']);
                            foreach($stmt as $row){
                                $S_time = $row['start_time'];
                                $E_time = $row['end_time'];
                                $title = htmlspecialchars($row['title']);
                                $color = $row['color'];
                                $delete = $row['logic_delete'];
                                $id = $row['id'];

                                //開始時刻の前の空白マスを算出
                                $prev = (int)str_replace(':00','',$S_time);

                                //予定の期間を算出
                                $during = (int)str_replace(':00','',$E_time) - $prev;

                                //終了時刻の後の空白マスを算出
                                $back = 24 - ($prev + $during);
                                if($prev + $during < $todayTime){
                                    $nowMaker2 =  "";
                                }else if($prev + $during > $todayTime){
                                    $nowMaker2 =  $todayTime - ($prev + $during);
                                }

                                print("<tr>");
                                    //開始時刻の前の空白マス
                                    for($i=1;$i<=$prev;$i++){
                                        //現在時刻の時は背景色つける
                                        if($year == $TodayYear && $month == $TodayMonth && $day == $today && ($i-1)  == $todayTime){
                                            print("<td class=\"timesNow\"></td>");
                                        }else{
                                            print("<td class=\"tdView\"></td>");
                                        }
                                    }

                                    //予定の出力文字数が15を超える場合は一部のみをカットして表示する処理を行う
                                    if(mb_strlen($title) >= 15){
                                        // 10文字まで抜き出し、語末に...をつける
                                        $title = mb_substr($title,0,10) . '...';
                                    }
                                    //予定を出力
                                    print("<td class=\"tdSche\" colspan=\"{$during}\">");
                                    print("<div class=\"scheDiv\" style=\"background-color:{$color}\">{$title}</div>");
                                    print("</td>");

                                    //終了時刻の後の空白マス
                                    for($i=1;$i<=$back;$i++){
                                        //現在時刻の時は背景色つける
                                        if($year == $TodayYear && $month == $TodayMonth && $day == $today && ($i-1) == $todayTime - ($prev + $during)){
                                            print("<td class=\"timesNow\"></td>");
                                        }else{
                                            print("<td class=\"tdView\"></td>");
                                        }
                                    }
                                print("</tr>");
                            }
                            print("</table>");
                        }
                    }else{
                        print("<div class=\"no_data\">該当する予定はありません</div>");
                    }
                    
                ?>
                </div>
            </div>
        </div>
    </main>

    <!-- フッター -->
    <footer class="fixed-bottom">
    </footer>
    

    <!-- ここからはモーダルの表示内容 -->
    <!-- 削除 -->
    <div class="modal" id="delete">
        <form class="deleteform" action="../delete.php" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">削除の確認</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="Deleteform" class="deletepops">
                            <p class="confirmText">予定を削除します。<br/>本当によろしいですか？</p>

                            <!-- ページリダイレクトに必要なパラメータ -->
                            <input type="hidden" name="userid" value="<?php print($userid);?>">
                            <input type="hidden" name="year" value="<?php print($year);?>">
                            <input type="hidden" name="month" value="<?php print($month);?>">
                            <input type="hidden" name="day" value="<?php print($day);?>">
                            <input type="hidden" name="view" value="<?php print($viewMode);?>">

                            <input type="hidden" id="selectId" name="id" value="">
                            <input type="hidden" name="logic_delete" value="true">

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-danger">削除する</button>
                    </div>

                </div>
            </div>
        </form>
    </div>


    <!-- 新規登録 -->
    <div class="modal" id="add">
        <form id="insertform" action="../insert.php" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">新規登録 </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="Addform">
                            <b><span id="s_text"></span>-<span id="e_text"></span>に予定を追加します</b>
                            <input type="hidden" name="userid" value="<?php print($userid); ?>">
                            <input type="hidden" name="year" value="<?php print($year); ?>">
                            <input type="hidden" name="month" value="<?php print($month); ?>">
                            <input type="hidden" name="day" id="AddHiddenday" value="<?php print($day); ?>">
                            <input type="hidden" name="view" value="<?php print($viewMode);?>">
                            <input type="hidden" name="delete" value="false">
                            <input type="hidden" name="start" id="AddHiddenStart" value="">
                            <input type="hidden" name="end" id="AddHiddenEnd" value="">

                            <input type="hidden" name="progress" value="0">
                            <p>予定のタイトル</p>
                            <input id="subAddTitle" type="text" name="title" value="" placeholder="入力必須です" required>
                            <p>詳細なメモ</p>
                            <textarea id="subAddMemo" name="memo" cols="11" rows="4" value="" placeholder="ご自由にお書きください"></textarea>

                            <p id="AddSelectTdColor">背景色をカスタム</p>
                            <div id="AddTdColor">
                                <input type="radio" name="color" value="#66FF66" id="Addgreen"><label for="Addgreen" id="Addgreen"></label>
                                <input type="radio" name="color" value="#FFFF88" id="Addyellow"><label for="Addyellow" id="Addyellow"></label>
                                <input type="radio" name="color" value="#87CEFA;" id="Addbule"><label for="Addbule" id="Addbule"></label>
                                <input type="radio" name="color" value="#C299FF" id="Addpurple"><label for="Addpurple" id="Addpurple"></label>
                                <input type="radio" name="color" value="#FA8072" id="Addred"><label for="Addred" id="Addred"></label>
                                <input type="radio" name="color" value="#FFA500" id="Addorange"><label for="Addorange" id="Addorange"></label>
                                <input type="radio" name="color" value="#FFFFFF" id="Addwhite" checked><label for="Addwhite" id="Addwhite"></label>
                            </div>

                            
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">登録</button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- bootstrap -->

    <script>
        $(function(){

            $(".delete").click(function(){
                
                //対象のid番号を取得し変数idに代入
                var id = $(this).attr('id');
                //編集フォームの<input type=hidden>タグのvalueに日付を書く
                $("#selectId").val(id);
            });

            $(".sub_add").click(function(){
                
                //対象のid番号を取得し変数idに代入
                var id = $(this).attr('id');

                var val1 = id+":00";
                var val2 = (Number(id)+1)+":00";

                //新規登録フォームの<input type=hidden>タグのvalueに日付を書く
                $("#AddHiddenStart").val(val1);
                $("#AddHiddenEnd").val(val2);

                //新規登録フォームの確認コメントに日付を書く
                $("#s_text").text(val1);
                $("#e_text").text(val2);
            });

            //ツールチップ
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>