<?php
//祝日読み込み関数laod_csv()を作成

function laod_csv($year,$month,$day){
    
    // ファイルの読み込み
    $f = fopen("./csv/syukujitsu.csv", "r");

    $newAry = array();

    // 1行ずつCSVを配列に変換して $newAry に格納。
    while ($line = fgetcsv($f)) {

        // 文字化け対策
        $line = mb_convert_encoding($line, 'UTF-8', 'sjis-win');

        $newAry[] = $line;
    }


    for ($i=1;$i<count($newAry);$i++) {
            
        //年・月・日をスラッシュ/で分解する
        $YMD = explode('/',$newAry[$i][0]);

        $Y = $YMD[0]; //年
        $M = $YMD[1]; //月
        $D = $YMD[2]; //日
        $syuku = $newAry[$i][1]; //祝日名

        if($year == $YMD[0] && $month == $YMD[1] && $day == $YMD[2]){
            return $syuku;
        }
    }

    // ファイルを閉じる
    fclose($f);

}

?>
    
    
