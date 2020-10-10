<?php
    //セッション開始
    session_start(); 
    if(isset($_SESSION['login']) == false){
      //このページのURLをコピーして他のブラウザで閲覧できないようにする
      header("location:error.html");
    }else{
      $userid = $_SESSION['login']['username'];
    }

    //ページ表示に必要なデータの受け取り
    $userid = $_GET['userid'];
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];
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
        $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$userid,$year,$month,$day]);

        if($stmt->rowCount() == 0){
            print("<div>まだ本日の予定はありません</div>");
        }

        // <!-- 簡易リスト表示 -->
        foreach($stmt as $row){
            $S_time = $row['start_time'];
            $E_time = $row['end_time'];
            $title = $row['title'];
            $memo = nl2br($row['memo']);

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
            }else if($row['progress'] == 1){
                $toggle = 0;
            }


            $color = $row['color'];
            $delete = $row['logic_delete'];
            $id = $row['id'];

            

            $list_view = <<<EOF
                <div class="list_view" style="background-color:{$color}">
                    <p class="list_time">{$S_time}〜{$E_time}</p>
                    <p class="list_title">{$title}</p>
                    <form action="edit.php" method="post">
                        <input type="hidden" name="userid" value="$userid">
                        <input type="hidden" name="year" value="$year">
                        <input type="hidden" name="month" value="$month">
                        <input type="hidden" name="day" value="$day">
                        <input type="hidden" name="id" value="$id">
                        <input type="hidden" name="progFlag" value="$toggle">
                        <button type="submit">{$progress}</button>
                    </form>
                    <a data-toggle="modal" href="#memo{$id}" class="">詳細</a>
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

            if($delete == "false"){
                print($list_view);
            }
        }


        // <!-- タイムテーブル表示 -->
        print("<table id=\"table_view\" border=1>");
        print("<thead class=\"timesTh\">");
        for($i=0;$i<=23;$i++){
            print("<th><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">{$i}:00</a></th>");
            // print("<td><a data-toggle=\"modal\" href=\"#add\" class=\"sub_add\" id=\"{$i}\">新規</a></td>");
        }
        print("</thead>");

        $stmt->execute([$userid,$year,$month,$day]);
        foreach($stmt as $row){
            $S_time = $row['start_time'];
            $E_time = $row['end_time'];
            $title = $row['title'];
            $color = $row['color'];
            $delete = $row['logic_delete'];
            $id = $row['id'];

            if($delete == "false"){
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
                // print("<td class=\"tdSche\" colspan=\"{$during}\" style=\"background-color:{$color}\">{$row['title']}</td>");
                $table_view = <<<EOF
                    <td class="tdSche" colspan="{$during}" style="background-color:{$color}">
                        <div class="scheDiv">{$row['title']}</div>
                    </td>
                EOF;
                print($table_view);

                //終了時刻の後の空白マス
                for($i=1;$i<=$back;$i++){
                    print("<td class=\"tdView\"></td>");
                }
            }
            print("</tr>");
        }
        print("</table>");
    ?>

    

    <!-- 編集フォーム -->
    <?php
        if(isset($_GET['ID']) == true){
            
            $f_upper = <<<upper
            <div id="popback"></div>
            <!--編集のポップアップウィンドウ-->
            <div id="Editform">
        
                <!-- Bootstrapでヘッダーをデザイン -->
                <div class="modal-header">
                    <h4 class="modal-title">ヘッダー</h4>
                    <button id="closeBtn" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
        
                <form id="editform" action="edit.php" method="post">
                    <!-- ページリダイレクトに必要なパラメータ -->
                    <input type="hidden" name="userid" value="$userid">
                    <input type="hidden" name="year" value="$year">
                    <input type="hidden" name="month" value="$month">
                    <input type="hidden" name="day" value="$day">
            upper;
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
                print("<select name=\"start\">");
                    print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                    for($i=0;$i<=23;$i++){
                        $s_time = "{$i}:00";
                            if($s_time == $row['start_time']){
                                print("<option value=\"{$s_time}\" selected>{$s_time}</option>");
                            }else{
                                print("<option value=\"{$s_time}\">{$s_time}</option>");
                            }
                    }
                print("</select>");

                //<終了時刻>
                print("<label for=\"start\">終了時刻</label>");
                print("<select name=\"end\">");
                    print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                    for($i=0;$i<=23;$i++){
                        $time = "{$i}:00";
                            if($time == $row['end_time']){
                                print("<option value=\"{$time}\" selected>{$time}</option>");
                            }else{
                                print("<option value=\"{$time}\">{$time}</option>");
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

                $f_progress = <<<progress
                <p id="pre_progress">進捗</p>
                <select name="progress">
                    <option value="" disabled selected style="display:none;">選択</option>
                    <option value="0" $selected0>未了</option>
                    <option value="1" $selected1>完了</option>
                    <option value="2" $selected2>キャンセル</option>
                </select>
                progress;
                print($f_progress);

                //<カラーのチェック判定>
                if($row['color'] == "#66FF66"){
                    $checked0 = "checked";
                }else if($row['color'] == "#FFFF88"){
                    $checked1 = "checked";
                }else if($row['color'] == "#75A9FF"){
                    $checked2 = "checked";
                }else if($row['color'] == "#C299FF"){
                    $checked3 = "checked";
                }else if($row['color'] == "#FF4F50"){
                    $checked4 = "checked";
                }else if($row['color'] == "#FFA500"){
                    $checked5 = "checked";
                }else if($row['color'] == "#FFFFFF"){
                    $checked6 = "checked";
                }

                $f_color = <<<color
                <p id="SelectTdColor">背景色をカスタム</p>
                <div id="EditTdColor">
                    <input type="radio" name="color" value="#66FF66" id="green" $checked0><label for="green" id="green"></label>
                    <input type="radio" name="color" value="#FFFF88" id="yellow" $checked1><label for="yellow" id="yellow"></label>
                    <input type="radio" name="color" value="#75A9FF" id="bule" $checked2><label for="bule" id="bule"></label>
                    <input type="radio" name="color" value="#C299FF" id="purple" $checked3><label for="purple" id="purple"></label>
                    <input type="radio" name="color" value="#FF4F50" id="red" $checked4><label for="red" id="red"></label>
                    <input type="radio" name="color" value="#FFA500" id="orange" $checked5><label for="orange" id="orange"></label>
                    <input type="radio" name="color" value="#FFFFFF" id="white" $checked6><label for="white" id="white"></label>
                </div>
                color;
                print($f_color);

                $f_bottom = <<<bottom
                        <div id="EdiOut"><input id="EditBtn" type="submit" value="この内容で編集する"></div>
                    </form>
                </div>
                bottom;
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
                            <p><span id="s_text"></span>-<span id="e_text"></span></p>
                                <input type="hidden" name="userid" value="<?php print($userid); ?>">
                                <input type="hidden" name="year" value="<?php print($year); ?>">
                                <input type="hidden" name="month" value="<?php print($month); ?>">
                                <input type="hidden" name="day" id="AddHiddenday" value="<?php print($day); ?>">

                                <input type="hidden" name="start" id="AddHiddenStart" value="">
                                <input type="hidden" name="end" id="AddHiddenEnd" value="">

                                <input type="hidden" name="progress" value="0">
                                <div><input type="text" name="title" value=""></div>
                                <p><textarea id="AddPreviwe" name="memo" cols="11" rows="4" value="" placeholder="ここに書いてください"></textarea></p>

                                <p id="AddSelectTdColor">背景色をカスタム</p>
                                <div id="AddTdColor">
                                    <input type="radio" name="color" value="#66FF66" id="Addgreen"><label for="Addgreen" id="Addgreen"></label>
                                    <input type="radio" name="color" value="#FFFF88" id="Addyellow"><label for="Addyellow" id="Addyellow"></label>
                                    <input type="radio" name="color" value="#75A9FF" id="Addbule"><label for="Addbule" id="Addbule"></label>
                                    <input type="radio" name="color" value="#C299FF" id="Addpurple"><label for="Addpurple" id="Addpurple"></label>
                                    <input type="radio" name="color" value="#FF4F50" id="Addred"><label for="Addred" id="Addred"></label>
                                    <input type="radio" name="color" value="#FFA500" id="Addorange"><label for="Addorange" id="Addorange"></label>
                                    <input type="radio" name="color" value="#FFFFFF" id="Addwhite" checked><label for="Addwhite" id="Addwhite"></label>
                                </div>

                                <input type="hidden" name="delete" value="false">
                                <input type="hidden" name="redirect" value="true">

                                <div id="AddOut"><input id="AddBtn" type="submit" value="新規メモ登録"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">削除する</button>
                    </div>

                </div>
            </div>
        </form>
    </div>





               
    <!-- <footer>
    </footer> -->
    


    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(function(){

            $("#list_view").click(function(){
                $('.table_view').hide();
                $('.list_view').show();
            });

            $("#table_view").click(function(){
                $('.list_view').hide();
                $('.table_view').show();
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

                //編集フォームの<input type=hidden>タグのvalueに日付を書く
                $("#AddHiddenStart").val(val1);
                $("#AddHiddenEnd").val(val2);

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