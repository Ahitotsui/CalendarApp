<?php
    //セッション開始
    session_start(); 
    if(isset($_SESSION['login']) == false){
      //このページのURLをコピーして他のブラウザで閲覧できないようにする
      header("location:error.html");
    }else{
      $userid = $_SESSION['login']['username'];
    }

    //ページ表示に必要なパラメータの受け取り
    // $userid = $_GET['userid'];
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    //検索条件のパラメータ受け取り
    if(isset($_POST['all_disp']) == true && $_POST['all_disp'] != ""){
        $dispMode =  $_POST['all_disp'];
    }else if(isset($_POST['unfini_disp']) == true && $_POST['unfini_disp'] != ""){
        $dispMode =  $_POST['unfini_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=0 ORDER BY start_time";
    }else if(isset($_POST['fini_disp']) == true && $_POST['fini_disp'] != ""){
        $dispMode =  $_POST['fini_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=1 ORDER BY start_time";
    }else if(isset($_POST['can_disp']) == true && $_POST['can_disp'] != ""){
        $dispMode =  $_POST['can_disp'];
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and progress=2 ORDER BY start_time";
    }else if(isset($_POST['keyword']) == true && $_POST['keyword'] != ""){
        $dispMode = "Keyword";
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? and logic_delete=? and title like '%{$_POST['keyword']}%' ORDER BY start_time";
    }else{
        $dispMode = "All";
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
    <title><?php print($year); ?>/<?php print($month); ?>/<?php print($day); ?></title>
</head>
<body>
    <header class="fixed-top">
        <div id="top">
            <div id="Htitle">
            <a id="TitleBackLink" href="calendar.php?year=<?php print($year) ?>&month=<?php print($month) ?>">
                <h1>Calendar&gt;&gt;<span id=TitleMonth>Day</span></h1>
            </a>
            </div>

            <div id="Hlogin"><p id=login>ようこそ<span id="username"><?php print($_SESSION['login']['name']); ?></span>さん</p></div>
            <div id="Hlogout"><a id="logout" href="logout.php">ログアウト</a></div>
        </div>
    </header>
    <!--ヘッダー領域(END)--->

    <h2 class="container">
        <?php print($year); ?>年<?php print($month); ?>月<?php print($day); ?>日
    </h2>

    <!-- フィルタ&検索機能 -->
    <div class="row" id="search">
        <div id="disp_filter" class="coll col-md-4">
            <!-- <p id="filer_text">表示する予定の絞り込み</p> -->
            <div class="btn-group" role="group">
            <form action="schedule.php?userid=<?php print($userid); ?>&year=<?php print($year); ?>&month=<?php print($month); ?>&day=<?php print($day); ?>" method="post">
                <input type="hidden" name="all_disp" value="All">
                <button type="submit" id="filBtn1" class="btn btn-secondary">全て</button>
            </form>
            <form action="schedule.php?userid=<?php print($userid); ?>&year=<?php print($year); ?>&month=<?php print($month); ?>&day=<?php print($day); ?>" method="post">
                <input type="hidden" name="unfini_disp" value="0">
                <button type="submit" id="filBtn2" class="btn btn-secondary">未了</button>
            </form>
            <form action="schedule.php?userid=<?php print($userid); ?>&year=<?php print($year); ?>&month=<?php print($month); ?>&day=<?php print($day); ?>" method="post">
                <input type="hidden" name="fini_disp" value="1">
                <button type="submit" id="filBtn3" class="btn btn-secondary">完了</button>
            </form>
            <form action="schedule.php?userid=<?php print($userid); ?>&year=<?php print($year); ?>&month=<?php print($month); ?>&day=<?php print($day); ?>" method="post">
                <input type="hidden" name="can_disp" value="2">
                <button type="submit" id="filBtn4" class="btn btn-secondary">キャンセル</button>
            </form>
            </div>
        </div>

        <div class="coll col-md-4">
            <form action="schedule.php?userid=<?php print($userid); ?>&year=<?php print($year); ?>&month=<?php print($month); ?>&day=<?php print($day); ?>" method="post">
            <div class="input-group">
                <span class="input-group">
                    
                    <input class="form-control" type="text" placeholder="予定のタイトルでさがす" name="keyword" value="">
                    <button class="btn btn-secondary" type="submit">検索</button>
                </span>
            </div>
            </form>
        </div>
    </div>

    <!-- 表示モード切替機能 -->
    <ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" id="list_view">簡易リスト表示</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" id="table_view">タイムテーブル表示</a>
    </li>
    </ul>

   
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
            print("<div>まだ本日の予定はありません</div>");
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
                        <p class="list_time">{$S_time}〜{$E_time}</p>
                        <p class="list_title">{$title}</p>
                        <form class="progBtn" action="edit.php" method="post">
                            <input type="hidden" name="userid" value="$userid">
                            <input type="hidden" name="year" value="$year">
                            <input type="hidden" name="month" value="$month">
                            <input type="hidden" name="day" value="$day">
                            <input type="hidden" name="id" value="$id">
                            <input type="hidden" name="progFlag" value="$toggle">
                            <button type="submit" class="$class" $disabled>{$progress}</button>
                        </form>
                        <a data-toggle="modal" href="#memo{$id}" class="detail">詳細</a>
                        <a href="schedule.php?ID={$id}&userid=$userid&year=$year&month=$month&day=$day" class="edit">編集</a>
                        <a data-toggle="modal" href="#delete" class="delete" id="{$id}">削除</a>
                        
                        <div class="modal" id="memo{$id}">
                        <form class="deleteform" action="delete.php" method="post">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{$S_time}-{$E_time}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="Deleteform" class="deletepops">
                                            <h4>【{$title}】</h4>
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

                print($list_view);
            }


            // <!-- タイムテーブル表示 -->
            print("<table id=\"time_table\" border=1>");
            print("<thead class=\"timesThead\">");
            for($i=0;$i<=23;$i++){
                //時刻の文字がリンクになっていて、新規登録ページのモーダルを開く
                print("<th class=\"timesTh\"><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">{$i}:00</a></th>");
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

                print("<tr>");
                    //開始時刻の前の空白マス
                    for($i=1;$i<=$prev;$i++){
                        print("<td class=\"tdView\"></td>");
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
                        print("<td class=\"tdView\"></td>");
                    }
                print("</tr>");
            }
            print("</table>");
        }else{
            print("<div>該当する予定はありません</div>");
        }
        
    ?>

    

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

                            <input type="hidden" name="delete" value="false">
                            <input type="hidden" name="redirect" value="true">
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

            // 簡易リスト表示切替
            $("#list_view").click(function(){
                $('#time_table').hide();
                $('.list_view').show();
            });

            // タイムテーブル表示切替
            $("#table_view").click(function(){
                $('.list_view').hide();
                $('#time_table').show();
            });

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