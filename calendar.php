
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
<html>
<head>
<meta charset="UTF-8">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre&family=Lora:wght@600&family=Noto+Sans+JP:wght@300;400;500&display=swap" rel="stylesheet"> -->
  <link rel="stylesheet" href="calendar.css">
  <!-- <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="../css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> -->
  <script src="jquery-3.4.1.min.js"></script>
  <script src="calendar.js"></script>
  <title>Calendar</title>
</head>

<body>

<!--ヘッダー領域-->
<header>
  <div id="top">
    <div id="Htitle">
      <a id="TitleBackLink" href="calendar.php">
        <h1><?php print($TitleYear); ?>年  <span id=TitleMonth> <?php print($Titlemonth); ?>月</span></h1>
      </a>
    </div>

    <div id="Hlogin"><p id=login>ようこそ<span id=username><?php print($_SESSION['login']['name']); ?></span>さん</p></div>
    <div id="Hlogout"><a id="logout" href="logout.php">ログアウト</a></div>
  </div>
</header>
<!--ヘッダー領域(END)--->

<main>
  
  <?php
  //DB接続
  require_once('DBInfo.php');
  $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
  $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=?";
  $stmt = $dbh->prepare($sql);

  /*-------------------------------------------カレンダー表示領域---------------------------------------------------*/
  //テーブルヘッド
  print("<table border=1>");
  print("<thead id=tbhead>");
  print("<td class=tdtop>月 MON</td>");
  print("<td class=tdtop>火 TUE</td>");
  print("<td class=tdtop>水 WED</td>");
  print("<td class=tdtop>木 THU</td>");
  print("<td class=tdtop>金 FRI</td>");
  print("<td class=tdtop>土 SAT</td>");
  print("<td class=tdtop>日 SUN</td>");
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
    for($i=0;$i<$ini;$i++){
      print("<td class=tdPreMon></td>");
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
      print("<td id=td{$day} class=tddays>");

      //現在の日にちに背景色をつけるためのidをつけるため判別
      if($TitleYear == $TodayYear && $TodayMonth == $Titlemonth && $today == $day){
        print("<p id=tdToday>{$day}</p>");
      }else{
        print("<p class=tdDays>{$day}</p>");
      }

      //新規登録ボタン
      print("<button class=\"addbtns\" id=\"{$day}\">+</button>");

      // print("<div id=\"hiddenID{$day}\" class=\"{$id}\"></div>");

      //詳細画面に飛ばすためaタグ囲む
      print("<a class=\"linkSche\" href=\"schedule.php?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day\">");
        //日にちごとにメモを表示
        print("<div id=\"memo{$day}\" class=\"memos\">");
          $stmt->execute([$_SESSION['login']['username'],$TitleYear,$Titlemonth,$day]);
          foreach($stmt as $row){
            if($row['logic_delete'] == "false"){
              $title = htmlspecialchars($row['title']);
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
  <button id="AddClose">&times;</button>
  <form id="insertform" action="insert.php" method="post">
    <p id="AddConfirm"><?php print($TitleYear); ?>年<?php print($Titlemonth); ?>月<span id="AddDay"></span>日</p>

    <input type="hidden" name="userid" value="<?php print($_SESSION['login']['username']); ?>">
    <input type="hidden" name="year" value="<?php print($TitleYear); ?>">
    <input type="hidden" name="month" value="<?php print($Titlemonth); ?>">
    <input type="hidden" name="day" id="AddHiddenday" value="">

    <select name="start" required>
      <option value="" disabled selected style="display:none;">選択</option>
      <?php 
        for($i=0;$i<=23;$i++){
          print("<option value=\"{$i}:00:00\">{$i}:00</option>");
        }
      ?>
    </select>

    <select name="end" required>
      <option value="" disabled selected style="display:none;">選択</option>
      <?php 
        for($i=1;$i<=24;$i++){
          print("<option value=\"{$i}:00:00\">{$i}:00</option>");
        }
      ?>
    </select>

    <input type="hidden" name="progress" value="0">
    <div><input id="AddTitle" type="text" name="title" value=""></div>
    <p><textarea id="AddPreviwe" name="memo" cols="11" rows="4" value="" placeholder="詳細なコメント"></textarea></p>

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

    <div id="AddOut"><input id="AddBtn" type="submit" value="新規メモ登録"></div>
  </form>
</div>
<!--===========================================================================================================================-->

</main>

<footer>
  <div id="monthSelectBtn">

  <!--前年のページネーション-->
    <?php
      $prev = $TitleYear - 1;
      if($prev >= 2019){
        print("<a id=\"PrevBtn\" href=\"calendar.php?year={$prev}&month={$Titlemonth}\">&lt;&lt;{$prev}</a>");
      }else{
        print("<div id=\"Dummy\"></div>");
      }
    ?>  

  <!--12ヶ月のページネーション-->
    <?php
    for($i=1;$i<13;$i++){
      if($i == $_GET['month']){
        print("<a href=\"calendar.php?year={$TitleYear}&month={$i}\" id=\"SelectBtn\" class=\"Nowselect\">{$i}</a>");
      }else{
        print("<a href=\"calendar.php?year={$TitleYear}&month={$i}\" class=\"monthsend\">{$i}</a>");
      }
    }
    ?>

  <!--翌年のページネーション-->
    <?php
      $next = $TitleYear + 1;
      print("<a id=\"NextBtn\" href=\"calendar.php?year={$next}&month={$Titlemonth}\">{$next}&gt;&gt;</a>");
    ?>  
  
  </div>
</footer>

<!-- <script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script> -->
</body>
</html>
