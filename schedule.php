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
    $URL = "schedule.php?userid=$userid&year=$year&month=$month&day=$day";

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
    <link rel="stylesheet" href="schedule.css">
    <script src="jquery-3.4.1.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css"><!-- Font Awesome -->
    <link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico" />
    <title><?php print($year); ?>/<?php print($month); ?>/<?php print($day); ?></title>
</head>
<body>

    <!--ヘッダー-->
    <header>
        <div id="header">
            <div id="top" class="row">
                <div id="Htitle" class="col col-md-2"></div>
                <div id="Htitle" class="col col-md-8">
                <a id="TitleBackLink" href="calendar.php?year=<?php print($year) ?>&month=<?php print($month) ?>">
                    <h1><?php print($year); ?>年<?php print($month); ?>月<?php print($day); ?>日</h1>
                </a>
                </div>

                <div id="Hlogin" class="col col-md-1">
                    <p id=login>
                        <i class="fas fa-user"></i><span id="username"><?php print($_SESSION['login']['name']); ?></span>さん
                    </p>
                </div>
                <div id="Hlogout" class="col col-md-1"><a id="logout" href="logout.php">ログアウト</a></div>
            </div>
        </div>    

        <!-- 昨日　明日　ページ送り -->
        <div class="row" id="pagenation">
            <div class="col col-md-6">
                <?php 
                    $prev_day = $day - 1;
                    print("<h2><a id=\"prevDay\" href=\"schedule.php?userid=$userid&year=$TodayYear&month=$TodayMonth&day=$prev_day&view=$viewMode\"><i class=\"fas fa-angle-double-left\"></i>前日</a></h2>");
                ?>
            </div>

            <!-- <div class="coll col-md-10"></div> -->

            <div class="col col-md-6">
                <?php 
                    $next_day = $day + 1;
                    print("<h2><a id=\"nextDay\" href=\"schedule.php?userid=$userid&year=$TodayYear&month=$TodayMonth&day=$next_day&view=$viewMode\">翌日<i class=\"fas fa-angle-double-right\"></i></a></h2>");
                ?>
            </div>
        </div>

        

        <!-- フィルタ&検索機能 -->
        <div class="row" id="search">

            <div class="coll col-md-4"><h3><i class="fas fa-tasks"></i>　予定の一覧</h3></div>

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

        <!-- 表示モード切替機能 -->
        <div id="dispTabs">
            <a id="<?php print($tabStyle1); ?>" class="dispTab" href="<?php print($URL); ?>&view=list">簡易リスト表示</a>
            <a id="<?php print($tabStyle2); ?>" class="dispTab" href="<?php print($URL); ?>&view=table">タイムテーブル表示</a>
        </div>
    </header>

    <main>
    <?php 
        //DB接続
        require_once('DBInfo.php');
        $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
        if($dispMode ==  "All"){
            $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? ORDER BY start_time";
        }

        $stmt = $dbh->prepare($sql);
        $stmt->execute([$userid,$year,$month,$day,'false']);

        if($stmt->rowCount() == 0 && $dispMode ==  "All"){
            print("<div class=\"no_data\">まだ予定はありません</div>");
        }else if($stmt->rowCount() != 0){
            // <!-- 簡易リスト表示 -->
            foreach($stmt as $row){
                $S_time = preg_replace('/:00/','',$row['start_time'],1);
                $E_time = preg_replace('/:00/','',$row['end_time'],1);
                $title = htmlspecialchars($row['title']);
                $memo = nl2br(htmlspecialchars($row['memo']));

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

                

                $list_view = <<<EOF
                    <div class="list_view" style="background-color:{$color}">
                        <div class="row">
                            <div class="col col-md-11">
                                <p class="list_time"><i class="far fa-clock"></i>{$S_time}〜{$E_time}</p>
                            </div>
                            <div class="col col-md-1">
                                <a data-toggle="modal" href="#memo{$id}" class="detail"><i id="operate1" class="fas fa-info-circle"></i></a>
                                <a href="$URL&ID={$id}&view=$viewMode" class="edit"><i id="operate2" class="fas fa-pen"></i></a>
                                <a data-toggle="modal" href="#delete" class="delete" id="{$id}"><i id="operate3" class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>     
                        
                        <div class="row">
                            
                            <div class="col col-md-1">
                                <form class="progBtn" action="edit.php" method="post">
                                    <input type="hidden" name="userid" value="$userid">
                                    <input type="hidden" name="year" value="$year">
                                    <input type="hidden" name="month" value="$month">
                                    <input type="hidden" name="day" value="$day">
                                    <input type="hidden" name="view" value="$viewMode">
                                    <input type="hidden" name="id" value="$id">
                                    <input type="hidden" name="progFlag" value="$toggle">
                                    <button type="submit" class="$class" $disabled>{$progress}</button>
                                </form>
                            </div>

                            <div class="col col-md-11">
                                <p class="list_title">{$title}</p>
                            </div>
                        </div>
                        
                        <div class="modal" id="memo{$id}">
                        <form class="deleteform" action="delete.php" method="post">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>{$title}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="Deleteform" class="deletepops">
                                            <h5 class="modal-title">{$S_time}-{$E_time}</h5>
                                            <p>{$memo}</p>                
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
                        print("<th id=\"timesNow\"><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">{$i}:00</a></th>");
                    }else{
                        //時刻の文字がリンクになっていて、新規登録ページのモーダルを開く
                        print("<th class=\"timesTh\"><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">{$i}:00</a></th>");
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

                        //予定を出力
                        $table_view = <<<EOF
                            <td class="tdSche" colspan="{$during}" style="background-color:{$color}">
                                <div class="scheDiv">{$title}</div>
                            </td>
                        EOF;
                        print($table_view);

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
    </main>

    <!-- フッター -->
    <footer class="fixed-bottom">
    </footer>
    

    <!-- ここからはモーダルの表示内容 -->
    <!-- 編集フォーム -->
    <?php
        if(isset($_GET['ID']) == true){
            
            $f_upper = <<<EOF
            <div id="popback"></div>
            <div id="Editform">
            <form action="edit.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">編集</h4>
                    <button id="closeBtn" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
        
                
                    <!-- ページリダイレクトに必要なパラメータ -->
                    <input type="hidden" name="userid" value="$userid">
                    <input type="hidden" name="year" value="$year">
                    <input type="hidden" name="month" value="$month">
                    <input type="hidden" name="day" value="$day">
                    <input type="hidden" name="view" value="$viewMode">
                    <div class="modal-body">
            EOF;
            print($f_upper);


            $ID = $_GET['ID'];
            $sql = "SELECT * FROM Memo_tags WHERE id=?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$ID]);

            foreach($stmt as $row){
                //<ID番号>
                print("<input type=\"hidden\" id=\"selectId\" name=\"id\" value=\"$ID\">");

                //<開始時刻>
                print("<label for=\"start\">開始時刻</label>");
                print("<select id=\"selectTime1\" name=\"start\">");
                    print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                    for($i=0;$i<=23;$i++){
                        if($i < 10){
                            $s_time = "0{$i}:00:00";
                        }else{
                            $s_time = "{$i}:00:00";
                        }
                            if($s_time == $row['start_time']){
                                print("<option value=\"{$s_time}\" selected>{$i}:00</option>");
                            }else{
                                print("<option value=\"{$s_time}\">{$i}:00</option>");
                            }
                    }
                print("</select>");

                //<終了時刻>
                print("<label for=\"start\">終了時刻</label>");
                print("<select id=\"selectTime2\" name=\"end\">");
                    print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                    for($i=0;$i<=23;$i++){
                        if($i < 10){
                            $time = "0{$i}:00:00";
                        }else{
                            $time = "{$i}:00:00";
                        }
                            if($time == $row['end_time']){
                                print("<option value=\"{$time}\" selected>{$i}:00</option>");
                            }else{
                                print("<option value=\"{$time}\">{$i}:00</option>");
                            }
                    }
                print("</select>");

                //<タイトル>
                print("<p id=\"label_title\">タイトル</p>");
                print("<div><input type=\"text\" id=\"pre_title\" name=\"title\" value=\"{$row['title']}\"></div>");

                //<メモ>
                print("<p id=\"label_memo\">メモ</p>");
                print("<div><textarea id=\"EditPreviwe\" name=\"memo\" cols=\"11\" rows=\"4\" value=\"\">{$row['memo']}</textarea></div>");

                //<進捗ステータスのチェック判定>
                if($row['progress'] == 0){
                    $selected0 = "selected";
                }else if($row['progress'] == 1){
                    $selected1 = "selected";
                }else if($row['progress'] == 2){
                    $selected2 = "selected";
                }

                //<進捗ステータス>
                $f_progress = <<<EOF
                <p id="pre_progress">進捗</p>
                <select id="prog_select" name="progress">
                    <option value="" disabled selected style="display:none;">選択</option>
                    <option value="0" $selected0>未了</option>
                    <option value="1" $selected1>完了</option>
                    <option value="2" $selected2>キャンセル</option>
                </select>
                EOF;
                print($f_progress);

                //<カラーのチェック判定>
                if($row['color'] == "#66FF66"){
                    $checked0 = "checked";
                }else if($row['color'] == "#FFFF88"){
                    $checked1 = "checked";
                }else if($row['color'] == "#87CEFA"){
                    $checked2 = "checked";
                }else if($row['color'] == "#C299FF"){
                    $checked3 = "checked";
                }else if($row['color'] == "#FA8072"){
                    $checked4 = "checked";
                }else if($row['color'] == "#FFA500"){
                    $checked5 = "checked";
                }else if($row['color'] == "#FFFFFF"){
                    $checked6 = "checked";
                }

                $f_color = <<<EOF
                <p id="SelectTdColor">背景色をカスタム</p>
                <div id="EditTdColor">
                    <input type="radio" name="color" value="#66FF66" id="green" $checked0><label for="green" id="green"></label>
                    <input type="radio" name="color" value="#FFFF88" id="yellow" $checked1><label for="yellow" id="yellow"></label>
                    <input type="radio" name="color" value="#87CEFA" id="bule" $checked2><label for="bule" id="bule"></label>
                    <input type="radio" name="color" value="#C299FF" id="purple" $checked3><label for="purple" id="purple"></label>
                    <input type="radio" name="color" value="#FA8072" id="red" $checked4><label for="red" id="red"></label>
                    <input type="radio" name="color" value="#FFA500" id="orange" $checked5><label for="orange" id="orange"></label>
                    <input type="radio" name="color" value="#FFFFFF" id="white" $checked6><label for="white" id="white"></label>
                </div>
                EOF;
                print($f_color);

                $f_bottom = <<<EOF
                    </div>
                    <div class="modal-footer" id="editFormFoot">
                        <button type="submit" class="btn btn-primary">編集</button>
                    </div>
                    </form>
                </div>
                EOF;
                print($f_bottom);
            }
        }
    ?>

    <!-- 削除 -->
    <div class="modal" id="delete">
        <form class="deleteform" action="delete.php" method="post">
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
        <form id="insertform" action="insert.php" method="post">
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

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
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

            $("#closeBtn").click(function(){
                //閉じるボタンを押したら編集フォーム隠す
                $('#Editform').hide();
                $('#popback').hide();
            });
        });
    </script>
</body>
</html>