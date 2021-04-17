
<!--ログインデータ使うのでセッション開始-->
<?php
  session_start(); 
  if(isset($_SESSION['login']) == false){
    //このページのURLをコピーして他のブラウザで閲覧できないようにする
    header("location:error.html");
  }else{
    $userid = $_SESSION['login']['username'];
  }
?>

<?php
  date_default_timezone_set('Japan');
  //今現在の年を取得
  $TodayYear = date("Y");

  //今現在の月を"先頭の0"無しで取得
  $TodayMonth = date("n");

  //今現在の日付を取得
  $today = date("d");


  if(isset($_GET['year']) == true && isset($_GET['month']) == true){
    //押された画面下の月のボタンに応じてカレンダーを書き換える
    $TitleYear = $_GET['year'];
    $Titlemonth = $_GET['month'];

    //セキュリティ対策
    if(ctype_digit($TitleYear) == false || $TitleYear < 2019){
      //月に数字以外の値または2019年より前の年が入力されたら現在日時のページに強制的に移動
      header("location:calendar.php?year={$TodayYear}&month={$TodayMonth}");
    }else if(ctype_digit($Titlemonth) == false || $Titlemonth < 1 || $Titlemonth > 12){
      //日にちに数字以外の値または1以下、12以上の数字が入力されたら現在日時のページに強制的に移動
      header("location:calendar.php?year={$TodayYear}&month={$TodayMonth}");
    }

  }else if(isset($_GET['year']) == false && isset($_GET['month']) == false){
    //画面下の月のボタンが押されていない場合または不正に2019年より下の年が送られた場合は自動で現在の月を取得し、変数$monthに代入
    $TitleYear = $TodayYear;
    $Titlemonth = $TodayMonth;
    //画面下の月のボタンが押されていない場合は自動で現在の年月のページを表示
    header("location:calendar.php?year={$TodayYear}&month={$TodayMonth}");
  }else{
    //上記以外の想定外のリクエストパラメータが送られて来たら現在日時のページに強制的に移動
    header("location:calendar.php?year={$TodayYear}&month={$TodayMonth}");
  }
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="UTF-8">
  <!-- 曜日のフォント -->
  <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="index.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css">

  <!-- bootstrap -->
  <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">

  <!-- bootstrap -->
  <link href="../css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
  
  <!-- CNDでオンラインでjquery読み込み -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="./index.js"></script>
  
  <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
  <title>Calendar</title>
</head>

<body>




<?php 
  if(isset($_COOKIE["add"])){
    print("<div id=\"addMsg\">");
    print("<span>予定を追加しました</span>"); 
    // print("<script>window.alert('予定を追加しました');</script>");
    print("<button id=\"checkMsg\" class=\"btn btn-success btn-sm\">OK</button>");
    print("</div>");
  }
  setcookie("add", 'add', time()-1800); 
?>

<!--ヘッダー領域-->
<?php require_once('../Header/header.php'); ?>

<main>
  <?php 
    $link_prevM = $Titlemonth - 1;
    $link_nextM = $Titlemonth + 1;
    $link_prevyear = $TitleYear;
    $link_nextyear = $TitleYear;
    if($link_prevM == 0){
      $link_prevM = 12;
      $link_prevyear = $TitleYear - 1;
    }else if($link_nextM == 13){
      $link_nextM = 1;
      $link_nextyear = $TitleYear + 1;
    }
  ?>
  <div id="topParts">
    <p id="TodayDisp" class="inlineParts"><?php print($TitleYear); ?>年 <?php print($Titlemonth); ?>月</p>
    <a href="index.php?year=<?php print($link_prevyear); ?>&month=<?php print($link_prevM); ?>" style=font-size:13px>&lt;&lt;前月ー</a>
    <a href="index.php?year=<?php print($link_nextyear); ?>&month=<?php print($link_nextM); ?>" style=font-size:13px>翌月&gt;&gt;</a>
    <!-- <a href="schedule.php?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list">
      <button class="inlineParts" id="day_link">日</button>
    </a> -->
    
  </div>

  <?php
  //DB接続
  require_once('../DBInfo.php');
  $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
  $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? ORDER BY start_time";
  $stmt = $dbh->prepare($sql);

  /*-------------------------------------------カレンダー表示領域---------------------------------------------------*/
  //テーブルヘッド
  print("<table id=\"mainTable\" border=\"1\"　class=\"table\">");
  print("<thead id=\"tbhead\">");
  print("<td class=\"tdtop\">月 <span class=\"small\">-MON-</span></td>");
  print("<td class=\"tdtop\">火 <span class=\"small\">-TUE-</span></td>");
  print("<td class=\"tdtop\">水 <span class=\"small\">-WED-</span></td>");
  print("<td class=\"tdtop\">木 <span class=\"small\">-THU-</span></td>");
  print("<td class=\"tdtop\">金 <span class=\"small\">-FRI-</span></td>");
  print("<td class=\"tdtop\" id=\"sat\">土 <span class=\"small\">-SAT-</span></td>");
  print("<td class=\"tdtop\" id=\"sun\">日 <span class=\"small\">-SUN-</span></td>");
  print("</thead>");

  //前月の空白マスの数を決めるため、$iniを定義
  @$ini = 0;
  //月ごとの1日が何曜日から始まるかを取得
  $iniweek = date('D',strtotime("$TitleYear-$Titlemonth-01"));
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
    $prevMonth = $Titlemonth - 1;
    if($prevMonth == 0){
      //1月は参照するのは前年なので　$refYear = $TitleYear - 1;　とする。
      $refYear = $TitleYear - 1;
      //1月は$prevMonth = 0になるので値を前月の12に直す
      $prevMonth = 12;
      $pervMonthLast = date( 't' , strtotime($refYear . "/" . $prevMonth . "/01"));
    }else{
      $pervMonthLast = date( 't' , strtotime($TitleYear . "/" . $prevMonth . "/01")); 
    }
    
    for($i=0;$i<$ini;$i++){
      //前の月の日付けを空白マスの数分算出し、$prevDayDispに格納
      $prevDayDisp = ($pervMonthLast - ($ini - 1)) + $i;
      print("<td class=\"tdPreMon\" valign=\"top\">$prevMonth / $prevDayDisp</td>");
    }

  //その月の日数を求める  
  $lastday = date( 't' , strtotime($TitleYear . "/" . $Titlemonth . "/01"));  

  //横に7個tdタグが並んだら改行するよう、$brを定義。また、初期値は前月の空白の数からスタートする    
  $br = $ini;
  //以下カレンダーの左上に表示する日付や、以下の日付マスの表示を構成するHTMLのタグのidやclassに用いるため$dayを定義 
  $day = 0;

  for($i=1;$i<=$lastday;$i++){

    $br++;
    $day++;
  
      //日にちを表示するtdタグ
      print("<td id=td{$day} class=\"tddays\">");

      // 祝日を読み込む関数を外部から使用
      require_once('../csv/csv.php');
      $syuku = laod_csv($TitleYear,$Titlemonth,$day);

      //現在の日にちに背景色をつけるためのidをつけるため判別
      if($TitleYear == $TodayYear && $TodayMonth == $Titlemonth && $today == $day){
        print("<div id=\"tdToday\"><p id=\"tdTodayStr\">{$day}</p></div>");
        print("<div class=\"eventDiv\">");
        if(strlen(trim($syuku)) != 0){
          print("<span class=\"eventDay\">{$syuku}</span>");
        }
        print("</div>");
      }else{
        print("<p class=\"tdDays\">{$day}</p>");
        print("<div class=\"eventDiv\">");
        if(strlen(trim($syuku)) != 0){
          print("<span class=\"eventDay\">{$syuku}</span>");
        }
        print("</div>");
      }

      //新規登録ボタン
      print("<div class=\"addbtns\" id=\"{$day}\"><i class=\"fas fa-plus-circle\"></i></div>");

      //詳細画面に飛ばすためaタグ囲む
      print("<a class=\"linkSche\" href=\"../Day?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list\">");
        //日にちごとにメモを表示
        print("<div id=\"memo{$day}\" class=\"memos\">");
          $stmt->execute([$_SESSION['login']['username'],$TitleYear,$Titlemonth,$day]);
          foreach($stmt as $row){
            if($row['logic_delete'] == "false"){
              $title = htmlspecialchars($row['title']);

              //予定の出力文字数が14を超える場合は一部のみをカットして表示する処理を行う
              if(mb_strlen($title) >= 14){

                $half_chara = 0;
                $full_chara = 0;
                for($j=0;$j<=12;$j++){
                  $check_chara = mb_substr($title,$j,$j+1);
                  if(strlen($check_chara) - mb_strlen($check_chara) == 0){
                    $half_chara += 2;
                  }else if(strlen($check_chara) - mb_strlen($check_chara) != 0){
                    $full_chara += 1; 
                  }
                }
                $max_chara = $half_chara + $full_chara;

                // $max_chara文字まで抜き出し、語末に...をつける
                $title = mb_substr($title,0,$max_chara) . '...';

              }

              print("<p class=\"tags\" style=background-color:{$row['color']};>・{$title}</p>");
            }
          }
        print("</div>");
      print("</a>");

      print("</td>");

    //横に7個tdタグが並んだら改行
    if($br%7 == 0){
      print("</tr>");
    }
  }
print("</table>");
/*------------------------------------カレンダー表示領域(END)-------------------------------------*/
?>

<!--ポップアップ時の背景-->
<div id="popback"></div>

<!--==============================================新規登録のポップアップウィンドウ====================================================-->
<div id="Addform">
  <form id="insertform" action="../insert.php" method="post">
    <button id="AddClose" type="reset">&times;</button>
    <table>

      <!-- 登録する日時の表示 -->
      <tr>
        <td  colspan="2">
          <p id="AddConfirm"><?php print($TitleYear); ?>年<?php print($Titlemonth); ?>月<span id="AddDay"></span>日</p>
        </td>
      </tr>

      <!-- 開始時刻・終了時刻 -->
      <tr>
        <td>
          <label for="timeSelect1">開始時刻</label>
          <select id="timeSelect1" name="start" required>
            <option value="999" disabled selected style="display:none;">選択</option>
            <?php 
              for($i=0;$i<=23;$i++){
                print("<option value=\"{$i}:00:00\">{$i}:00</option>");
              }
            ?>
          </select>
        </td>

        <td>
          <label for="timeSelect2">終了時刻</label>
          <select id="timeSelect2" name="end" required>
            <option value="999" disabled selected style="display:none;">選択</option>
            <?php 
              for($i=1;$i<=24;$i++){
                print("<option value=\"{$i}:00:00\">{$i}:00</option>");
              }
            ?>
          </select>
        </td>

        
      </tr>

      <!-- バリデーション1 -->
      <tr>
        <td colspan="2">
          <p id="atention1"></p>
        </td>
      </tr>

      <!-- 予定のタイトル入力 -->
      <tr>
        <td colspan="2">
          <label for="title">予定のタイトル入力</label>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input id="AddTitle" type="text" name="title" value="" placeholder="入力必須です" required>
        </td>
      </tr>
      <!-- バリデーション2 -->
      <tr>
        <td colspan="2">
          <p id="atention2"></p>
        </td>
      </tr>

      <!-- 詳細なコメント入力 -->
      <tr>
        <td colspan="2">
          <label for="memo">詳細なコメント入力</label>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <textarea id="AddPreviwe" name="memo" cols="11" rows="4" value="" placeholder="ご自由に書いてください"></textarea>
        </td>
      </tr>
      
      <!-- カラー選択  -->
      <tr>
        <td colspan="2">
          <label>カラー選択</label>
        </td>
      </tr>

      <!-- カラー選択  -->
      <tr>
        <td colspan="2">
          <div id="AddTdColor">
            <input type="radio" name="color" value="#66FF66" id="Addgreen"><label for="Addgreen" id="Addgreen"></label>
            <input type="radio" name="color" value="#FFFF88" id="Addyellow"><label for="Addyellow" id="Addyellow"></label>
            <input type="radio" name="color" value="#87CEFA" id="Addbule"><label for="Addbule" id="Addbule"></label>
            <input type="radio" name="color" value="#C299FF" id="Addpurple"><label for="Addpurple" id="Addpurple"></label>
            <input type="radio" name="color" value="#FA8072" id="Addred"><label for="Addred" id="Addred"></label>
            <input type="radio" name="color" value="#FFA500" id="Addorange"><label for="Addorange" id="Addorange"></label>
            <input type="radio" name="color" value="#FFFFFF" id="Addwhite" checked><label for="Addwhite" id="Addwhite"></label>
          </div>
        </td>
      </tr>

      <!-- 新規登録フォームでは入力しないパラメータ -->
      <input type="hidden" name="userid" value="<?php print($_SESSION['login']['username']); ?>">
      <input type="hidden" name="year" value="<?php print($TitleYear); ?>">
      <input type="hidden" name="month" value="<?php print($Titlemonth); ?>">
      <input type="hidden" name="day" id="AddHiddenday" value="">
      <input type="hidden" name="progress" value="0">
      <input type="hidden" name="delete" value="false">

      <!-- 新規登録ボタン -->
      <tr>
        <td colspan="2">
            <button id="AddBtn" type="submit">予定を追加</button>
        </td>
      </tr>
      
    </table>
  </form>
</div>
<!--===========================================================================================================================-->

</main>

<footer class="fixed-bottom">
  <div id="top" class="row">
  <!--前年のページネーション(月は12月に選択)-->
    <div class="col col-md-2">
    <?php
      $prev = $TitleYear - 1;
      if($prev >= 2019){
        print("<a id=\"PrevBtn\" href=\"?year={$prev}&month=12\">&lt;&lt;{$prev}</a>");
      }else{
        print("<div id=\"Dummy\"></div>");
      }
    ?>  
    </div>

  <!--12ヶ月のページネーション-->
    <div class="col col-md-8">
      <div id="months">  
      <?php
      for($i=1;$i<13;$i++){
        if($i == $_GET['month']){
          print("<p id=\"SelectBtn\" class=\"Nowselect\">{$i}</p>");
        }else{
          print("<a href=\"?year={$TitleYear}&month={$i}\" class=\"monthsend\">{$i}</a>");
        }
      }
      ?>
      </div>
    </div>

  <!--翌年のページネーション(月は1月に選択)-->
    <div class="col col-md-2">
    <?php
      $next = $TitleYear + 1;
      print("<a id=\"NextBtn\" href=\"?year={$next}&month=1\">{$next}&gt;&gt;</a>");
    ?>  
    </div>
  </div>
</footer>

  <!-- bootstrap -->
  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <!-- bootstrap -->


</body>
</html>
