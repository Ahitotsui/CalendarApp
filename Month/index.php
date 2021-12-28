
<!--ログインデータ使うのでセッション開始-->
<?php
  session_start(); 
  if(isset($_SESSION['login']) == false){
    //このページのURLをコピーして他のブラウザで閲覧できないようにする
    header("location:../Error");
  }else{
    $userid = $_SESSION['login']['username'];
  }

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
      header("location:./?year={$TodayYear}&month={$TodayMonth}");
    }else if(ctype_digit($Titlemonth) == false || $Titlemonth < 1 || $Titlemonth > 12){
      //日にちに数字以外の値または1以下、12以上の数字が入力されたら現在日時のページに強制的に移動
      header("location:./?year={$TodayYear}&month={$TodayMonth}");
    }

  }else if(isset($_GET['year']) == false && isset($_GET['month']) == false){
    //画面下の月のボタンが押されていない場合または不正に2019年より下の年が送られた場合は自動で現在の月を取得し、変数$monthに代入
    $TitleYear = $TodayYear;
    $Titlemonth = $TodayMonth;
    //画面下の月のボタンが押されていない場合は自動で現在の年月のページを表示
    header("location:./?year={$TodayYear}&month={$TodayMonth}");
  }else{
    //上記以外の想定外のリクエストパラメータが送られて来たら現在日時のページに強制的に移動
    header("location:./?year={$TodayYear}&month={$TodayMonth}");
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
  <!-- <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen"> -->

  <!-- bootstrap -->
  <!-- <link href="../css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> -->
  
  <!-- CNDでオンラインでjquery読み込み -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="./index.js"></script>
  
  <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
  <title>Calendar</title>
</head>

<body>


<?php if(isset($_COOKIE["add"])): ?>
  <div id="addMsg">
    <i class="fas fa-check-circle"></i>
    <span>予定を追加しました</span>
    <!-- <button id="checkMsg"><span class="cross"></span></button> -->
  </div>
  <?php setcookie("add", 'add', time()-1800);?>
<?php endif; ?>


   


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
  <div class="topParts">

    <p id="TodayDisp" class="inlineParts">
      <a href="index.php?year=<?php print($link_prevyear); ?>&month=<?php print($link_prevM); ?>" style=font-size:22px;color:#555;><i class="fas fa-chevron-left"></i></a>
      <?php print($TitleYear); ?>年 <?php print($Titlemonth); ?>月
      <a href="index.php?year=<?php print($link_nextyear); ?>&month=<?php print($link_nextM); ?>" style=font-size:22px;color:#555;><i class="fas fa-chevron-right"></i></a>
    </p>
   
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

  <?php
  //DB接続
  require_once('../DBInfo.php');
  $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
  $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? ORDER BY start_time";
  $stmt = $dbh->prepare($sql);

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

  $all_td_count = $ini + date( 't' , strtotime($_GET['year'] . "/" . $_GET['month'] . "/01"));  
?>

<table id="main_table" border="1" data-td="<?= $all_td_count ?>">

  <thead class="yobi_thead">
    <th class="thead_th">月</th>
    <th class="thead_th">火</th>
    <th class="thead_th">水</th>
    <th class="thead_th">木</th>
    <th class="thead_th">金</th>
    <th class="thead_th">土</th>
    <th class="thead_th">日</th>
  </thead>

  <tr>
    <?php
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
    ?>
    <?php for($i=0;$i<$ini;$i++): ?>
      <?php $prevDayDisp = ($pervMonthLast - ($ini - 1)) + $i;?>
      <td class="tdPreMon"><?=$prevMonth?>/<?=$prevDayDisp?></td>
    <?php endfor ; ?>

    <?php 
      //その月の日数を求める  
      $lastday = date( 't' , strtotime($TitleYear . "/" . $Titlemonth . "/01"));  

      //横に7個tdタグが並んだら改行するよう、$brを定義。また、初期値は前月の空白の数からスタートする    
      $br = $ini;

      //以下カレンダーの左上に表示する日付や、以下の日付マスの表示を構成するHTMLのタグのidやclassに用いるため$dayを定義 
      $day = 0;
    ?>

    <?php for($i=1;$i<=$lastday;$i++): ?>
      <?php
        $day++; 
        $br++;      
        require_once('../csv/csv.php');
        $syuku = laod_csv($TitleYear,$Titlemonth,$day);
      ?>
      <td class="day_td_tags"> 
        <div style="display:flex;" class="day_and_syuku_disp_area">

          <a href="../Day/?userid=<?=$userid?>&year=<?=$TitleYear?>&month=<?=$Titlemonth?>&day=<?=$day?>&view=list" style="z-index:90;">
            <div class="tdDays" id="<?php if($TitleYear == $TodayYear && $TodayMonth == $Titlemonth && $today == $day) echo 'tdTodayStr'?>"><?=$day?></div>
          </a>

          <div>
            <?php if(strlen(trim($syuku)) != 0): ?>
              <div class="eventDay"><?= $syuku ?></div>
            <?php endif; ?>
          </div>

        </div>
        <div class="memos add_form_triger" data-selectday="<?=$day?>">

          <?php $stmt->execute([$_SESSION['login']['username'],$TitleYear,$Titlemonth,$day]); ?>
            
          <?php foreach($stmt as $row) : ?>
            <?php if($row['logic_delete'] == "false"): ?>
              <?php 
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
              ?>
              <div class="tags" style="background:<?= $row['color'] ?>;">
                <span style="color:<?= $row['color'] ?>;filter: invert(100%) grayscale(100%) contrast(100);"><?= $title ?></span>
              </div>
            <?php endif; ?>
          <?php endforeach ; ?>
        </div>
      </td>
      <?php if($br%7 == 0) echo '</tr>'; ?>   
    <?php endfor ; ?>

  </tr>
</table>

<!--ポップアップ時の背景-->
<div id="popback"></div>

<!--==============================================新規登録のポップアップウィンドウ====================================================-->
<div id="Addform">
  <form id="insertform" action="../insert.php" method="post" name="insertform">
    
    <table class="form_frame">

      <!-- 登録する日時の表示 -->
      <tr>
        <td colspan="5">
          <div style="display:flex;">
              <div style="width:30px;"></div>
              <div style="width:100%;">
                <div id="AddConfirm" data-year="<?php print($TitleYear); ?>" data-month="<?php print($Titlemonth); ?>">
                  <?php print($TitleYear); ?>年<?php print($Titlemonth); ?>月<span id="AddDay"></span>日(<span id="yobi"></span>)
                </div>
              </div>
              <div>
                <button id="AddClose" type="reset">
                  <!-- <i class="fas fa-times" style="font-size:30px;"></i> -->
                  <span class="cross"></span>
                </button>
              </div>
          </div>
        </td>
      </tr>

      <!-- 開始時刻・終了時刻 -->
      <tr>
        <td width=12.5% align="left">開始</td>
        <td width=32.5% astyle="padding:2px;" align="right">
          <div class="selct_allow">
            <select id="timeSelect1" name="start" required>
              <option value="999" disabled selected style="display:none;">選択</option>
              <?php 
                for($i=0;$i<=23;$i++){
                  print("<option value=\"{$i}:00:00\">{$i}:00</option>");
                }
              ?>
            </select>
          </div>
        </td>
        <td width=10% align="center">〜</td>
        <td width=12.5% align="left">終了</td>
        <td width=32.5% astyle="padding:2px;" align="right">
          <div class="selct_allow">
            <select id="timeSelect2" name="end" required>
              <option value="999" disabled selected style="display:none;">選択</option>
              <?php 
                for($i=1;$i<=24;$i++){
                  print("<option value=\"{$i}:00:00\">{$i}:00</option>");
                }
              ?>
            </select>
          </div>
        </td>
      </tr>

      <!-- バリデーション1 -->
      <tr>
        <td colspan="5" style="padding:2px;">
          <p id="atention1"></p>
        </td>
      </tr>

      <!-- 予定のタイトル入力 -->
      <tr>
        <td colspan="5" style="padding:2px;">
          <div>タイトル</div>
          <input id="AddTitle" type="text" name="title" value="" placeholder="" required>
        </td>
      </tr>
      <!-- バリデーション2 -->
      <tr>
        <td colspan="5" style="padding:2px;">
          <p id="atention2"></p>
        </td>
      </tr>

      <!-- 詳細なコメント入力 -->
      <tr>
        <td colspan="5" style="padding:2px;">
          <div>詳細</div>
          <textarea id="AddPreviwe" name="memo" cols="11" rows="4" value="" placeholder=""></textarea>
        </td>
      </tr>
      
      <tr>
        <td colspan="5" style="">
          <div>カラー</div>
          <div class="selct_color_allow">
          <input type="color" name="color" list="color-list" class="select_color" value="#B0C4DE">
          </div>
          <datalist id="color-list">
            <option value="#66FF66">
            <option value="#FFFF88">
            <option value="#87CEFA">
            <option value="#C299FF">
            <option value="#FA8072">
            <option value="#FFA500">
          </datalist>
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
        <td colspan="5">
            <button id="AddBtn" type="submit">保存</button>
        </td>
      </tr>
      
    </table>
  </form>
</div>
<!--===========================================================================================================================-->

</main>

<footer class="footer">
  <!--前年のページネーション(月は12月に選択)-->
    <div style="width:10%;" class="left_year_block">
      <a class="PrevBtn" href="?year=<?php print($TitleYear - 1); ?>&month=12">&lt;&lt;<?php print($TitleYear - 1); ?></a>
    </div>

  <!--12ヶ月のページネーション-->
    <div class="middle_month_block">
      <div class="inner_months_wrap">
        <?php for($i=1;$i<13;$i++): ?>
          <a href="?year=<?= $TitleYear ?>&month=<?= $i ?>" class="month_links <?php if($i == $_GET['month']) echo 'now_select';?>"><?= $i ?></a>
        <?php endfor ;?>
      </div>
    </div>

  <!--翌年のページネーション(月は1月に選択)-->
    <div style="width:10%;" class="right_year_block">
      <a class="NextBtn" href="?year=<?php print($TitleYear + 1); ?>&month=1"><?php print($TitleYear + 1); ?>&gt;&gt;</a>
    </div>
</footer>

  <!-- bootstrap -->
  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <!-- bootstrap -->


</body>
</html>
