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
  }

  $year = $_GET['year'];
  $prev_year = $year - 1;
  $next_year = $year + 1;
  $now_month = $_GET['month'];
  
  if($now_month > 1){
    $pre_month = $now_month - 1;
  }elseif($now_month <= 1){
    $pre_month = 12;
  }

  if($now_month < 12){
    $aft_month = $now_month + 1;
  }elseif($now_month >= 12){
    $aft_month = 1;
  }
  
  $today = $_GET['day'];

  @$ini = 0;
  //月ごとの1日が何曜日から始まるかを取得
  $pre_space_day = date('D',strtotime("$year-$now_month-$today"));
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

  //前月の日数を求める  
  $pre_day_count = date( 't' , strtotime($year . "/" . $pre_month . "/01"));  

  //今月の日数を求める  
  $now_day_count = date( 't' , strtotime($year . "/" . $now_month . "/01"));  



  $bef_start_day = $today - $ini;
  $bef_max = $today - 1;
  $aft_max = $today + (6 - $ini);

  
  //区間月日表示の条件式(開始)
  if($today - $ini <= 0){
    if($now_month > 1){
      $start_year = $year;
      $start_month = $now_month - 1;
      $start_day = $pre_day_count + ($today - $ini);
    }elseif($now_month == 1){
      $start_year = $year - 1;
      $start_month = 12;
      $start_day = $pre_day_count + ($today - $ini);
    }
    // $start_year = $year;
    // $start_day = $pre_day_count + ($today - $ini);
  }else{
    $start_year = $year;
    $start_month = $now_month;
    $start_day = $today - $ini;
  }
  
  //区間月日表示の条件式(終了)
  if($today + (6 - $ini) > $now_day_count){
    if($now_month < 12){
      $end_year = $year;
      $end_month = $now_month + 1;
      $end_day = ($today + (6 - $ini)) - $now_day_count;
    }elseif($now_month == 12){
      $end_year = $year + 1;
      $end_month = 1;
      $end_day = ($today + (6 - $ini)) - $now_day_count;
    }
    // $end_year = $year;
    // $end_day = ($today + (6 - $ini)) - $now_day_count;
  }else{
    $end_year = $year;
    $end_month = $now_month;
    $end_day = $today + (6 - $ini);
  }
  
  


  //DB
  require_once('../DBInfo.php');
  $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
  $sql = "SELECT * FROM Memo_tags WHERE userid=? and year=? and month=? and day=? ORDER BY start_time";
  $stmt = $dbh->prepare($sql);
  
  function ConvertTime($time1,$time2){
    $S_time = preg_replace('/:00/','',$time1,1);
    $E_time = preg_replace('/:00/','',$time2,1); 

    $time = $S_time . '〜' .  $E_time;
    echo $time;
  }

  $week = array(  "月", "火", "水", "木", "金", "土" ,"日");
  $count = -1;
  
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

  <title>week</title>
</head>
<body>

  <!--ヘッダー領域-->
  <?php require_once('../Header/header.php'); ?>

  <a href="../Day/?userid=$userid&year=$TitleYear&month=$Titlemonth&day=$day&view=list">
    <button class="btn btn-secondary btn-sm" id="day_link">日</button>
  </a>
  <a href="../Month/?year=<?= $_GET['year'] ?>&month=<?= $_GET['month'] ?>">
    <button class="btn btn-secondary btn-sm" id="day_link">月</button>
  </a>

  <?php 
    
    // if($today - 7 < 1){
    //   $link_day_prev = $pre_day_count + ($today - 7);
    //   $link_month_prev = $now_month - 1;
    //   if($link_month_prev = 1){
    //     $link_month_prev = 12;
    //     $link_year_prev = $year - 1;
    //   }
    // }else{
    //   $link_year_prev = $year;
    //   $link_day_prev = $today - 7;
    //   $link_month_prev = $now_month;
    // }

    if($today - 7 < 1){
      if($now_month == 1){
        $link_year_prev = $year - 1;
        $link_month_prev = 12;
        $link_day_prev = $pre_day_count + ($today - 7);
      }elseif($now_month > 1){
        $link_year_prev = $year;
        $link_month_prev = $now_month - 1;
        $link_day_prev = $pre_day_count + ($today - 7);
      }
    }else{
      $link_year_prev = $year;
      $link_month_prev = $now_month;
      $link_day_prev = $today - 7;
    }


    if($today + 7 > $now_day_count){
      if($now_month == 12){
        $link_year_next = $year + 1;
        $link_month_next = 1;
        $link_day_next = ($today + 7) - $now_day_count;
      }elseif($now_month < 12){
        $link_year_next = $year;
        $link_month_next = $now_month + 1;
        $link_day_next = ($today + 7) - $now_day_count;
      }
    }else{
      $link_year_next = $year;
      $link_month_next = $now_month;
      $link_day_next = $today + 7;
    }
    
  
  
  ?>

  <div class="////text-center">
      <a class="" href="./?year=<?= $link_year_prev ?>&month=<?= $link_month_prev ?>&day=<?= $link_day_prev ?>">←</a>
      <h1 class="week_during_diplay"><?= $start_year ?>年<?= $start_month ?>月<?= $start_day ?>日〜<?= $end_year ?>年<?= $end_month ?>月<?= $end_day ?>日</h1>
      <a class="" href="./?year=<?= $link_year_next ?>&month=<?= $link_month_next ?>&day=<?= $link_day_next ?>">→</a>
  </div>

  <table border="1">

    <thead align="center">
      <?php for($b=$bef_start_day;$b<=$bef_max;$b++) : ?>
        <?php $count++ ?>
        <th class="<?php if($b <= 0) echo 'gray_scale'; ?>" > 
          <?php if($b <= 0) : ?>
            <?php $preday = $pre_day_count + $b ?>
              <div class="date"><?= $pre_month ?>/<?= $preday ?></div>
          <?php elseif($b >= 1) : ?>
              <div class="date"><?= $b ?></div>
          <?php endif ; ?>
          <div class="week"><?php echo $week[$count]; ?></div>
        </th>
      <?php endfor ; ?>


      <?php for($i=$today;$i<=$aft_max;$i++) : ?>
        <?php $count++ ?>
        <th class="<?php if($i > $now_day_count) echo 'gray_scale'; ?>">
          <?php if($i <= $now_day_count) : ?>
              <div class="date"><?= $i ?></div>
          <?php elseif($i > $now_day_count) : ?>
            <?php $aftday = $i -  $now_day_count?>
              <div class="date"><?= $aft_month ?>/<?= $aftday ?></div>
          <?php endif ; ?>
          <div class="week"><?php echo $week[$count]; ?></div>
        </th>
      <?php endfor ; ?>
    </thead>

    <tr>
      <?php for($b=$bef_start_day;$b<=$bef_max;$b++) : ?>
        <td class="<?php if($b <= 0) echo 'gray_scale'; ?>" > 
          <?php if($b <= 0) : ?>
            <?php $preday = $pre_day_count + $b ?>
              <div class="shcedule_wrap">
                <?php 
                  if($now_month > 1){
                    $stmt->execute([$_SESSION['login']['username'],$year,$pre_month,$preday]);
                  }elseif($now_month <= 1){
                    $stmt->execute([$_SESSION['login']['username'],$prev_year,$pre_month,$preday]);
                  }
                ?>
            
                <?php foreach($stmt as $row) : ?>
                  <div class="tags" style="background:<?= $row['color'] ?>">
                    <div class="time"><?php ConvertTime($row['start_time'],$row['end_time']); ?></div>
                    <div><?= $row['title'] ?></div>
                  </div>
                <?php endforeach ; ?>
              </div>
          <?php elseif($b >= 1) : ?>
              <div class="shcedule_wrap">
                <?php $stmt->execute([$_SESSION['login']['username'],$year,$now_month,$b]); ?>
                              
                <?php foreach($stmt as $row) : ?>
                    <div class="tags" style="background:<?= $row['color'] ?>">
                      <div class="time"><?php ConvertTime($row['start_time'],$row['end_time']); ?></div>
                      <div><?= $row['title'] ?></div>
                    </div>
                <?php endforeach ; ?>
              </div>
          <?php endif ; ?>
        </td>
      <?php endfor ; ?>


      <?php for($i=$today;$i<=$aft_max;$i++) : ?>
        <td class="<?php if($i > $now_day_count) echo 'gray_scale'; ?>">
          <?php if($i <= $now_day_count) : ?>
              <div class="shcedule_wrap">
                <?php $stmt->execute([$_SESSION['login']['username'],$year,$now_month,$i]); ?>
              
                <?php foreach($stmt as $row) : ?>
                  <div class="tags" style="background:<?= $row['color'] ?>">
                    <div class="time"><?php ConvertTime($row['start_time'],$row['end_time']); ?></div>
                    <div><?= $row['title'] ?></div>
                  </div>
                <?php endforeach ; ?>
              </div>
          <?php elseif($i > $now_day_count) : ?>
            <?php $aftday = $i -  $now_day_count?>
              <div class="shcedule_wrap">
                <?php 
                  if($now_month < 12){
                    $stmt->execute([$_SESSION['login']['username'],$year,$aft_month,$aftday]); 
                  }elseif($now_month >= 12){
                    $stmt->execute([$_SESSION['login']['username'],$next_year,$aft_month,$aftday]); 
                  }
                ?>
                              
                <?php foreach($stmt as $row) : ?>
                  <div class="tags" style="background:<?= $row['color'] ?>">
                    <div class="time"><?php ConvertTime($row['start_time'],$row['end_time']); ?></div>
                    <div><?= $row['title'] ?></div>
                  </div>
                <?php endforeach ; ?>
              </div>
          <?php endif ; ?>
        </td>
      <?php endfor ; ?>
    </tr>

  </table>
</body>
</html>