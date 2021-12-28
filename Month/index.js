
$(function(){


  /*------------------------------------------新規登録フォーム---------------------------------------------*/
  $(".add_form_triger").click(function(){
    
    //日付を取得し変数SelecteDayに代入
    var SelecteDay = $(this).data('selectday');

    //編集フォームのspanタグと<input type=hidden>タグのvalueに日付を書く
    $("#AddDay").text(SelecteDay);
    $("#AddHiddenday").val(SelecteDay);

    var year = $("#AddConfirm").data('year');
    var month = $("#AddConfirm").data('month');

    var getToday = year + '/' + month + '/' + SelecteDay;

    
    // 新規フォームに曜日を書く
    var today = new Date( getToday ) ;
    var weekday = [ "日", "月", "火", "水", "木", "金", "土" ] ;
    var wday = weekday[ today.getDay() ];
    $("#yobi").text(wday);


    //新規登録ボタン押したらポップアップ開く
    $("#Addform").fadeIn("fast");
    $("#popback").fadeIn("fast");
    $("TodayDisp").css('color', 'red');
  });

  //閉じるボタン押したらポップアップ閉じる  
  $("#AddClose").click(function(){
    $("#Addform").fadeOut("fast");
    $("#popback").fadeOut("fast");

    // バリデーションも一緒に初期
    $("#atention1").html('');
    $("#atention2").html('');
    $("#AddBtn").prop("disabled", false);
  });


  /*-------------------------------------------バリデーション--------------------------------------------*/

  // 開始時刻
  document.getElementById("timeSelect1").onchange = function(){
    // 終了時刻を自動設定
    var checkRef = Number(document.getElementById("timeSelect1").value.replace(":00:00", ""));
    var AutoEnd = checkRef + 1;
    document.insertform.end.selectedIndex = AutoEnd;

    var input_value = document.getElementById("AddTitle").value;
    var select1 = Number(document.getElementById("timeSelect1").value.replace(":00:00", ""));
    var select2 = Number(document.getElementById("timeSelect2").value.replace(":00:00", ""));

    if(select1 >= select2 && select2 != 999){
      document.getElementById("atention1").innerHTML = '※開始時刻に誤りがあります';
      document.getElementById("timeSelect1").style.borderColor = 'red';
      document.getElementById("AddBtn").disabled = true;
    }else if(select2 == 999){
      document.getElementById("AddBtn").disabled = true;
    }else if(select1 < select2 && input_value == ''){
      document.getElementById("atention1").innerHTML = '';
    }else if(select1 < select2 && input_value != ''){
      document.getElementById("atention1").innerHTML = '';
      document.getElementById("atention2").innerHTML = '';
      document.getElementById("AddBtn").disabled = false;
    }
  }

  // 終了時刻
  document.getElementById("timeSelect2").onchange = function(){
    var input_value = document.getElementById("AddTitle").value;
    var select1 = Number(document.getElementById("timeSelect1").value.replace(":00:00", ""));
    var select2 = Number(document.getElementById("timeSelect2").value.replace(":00:00", ""));

    if(select1 >= select2 && select1 != 999){
        document.getElementById("atention1").innerHTML = '※終了時刻に誤りがあります';
        document.getElementById("timeSelect2").style.borderColor = 'red';
        document.getElementById("AddBtn").disabled = true;
    }else if(select1 == 999){
      document.getElementById("AddBtn").disabled = true;
    }else if(select1 < select2 && input_value == ''){
        document.getElementById("atention1").innerHTML = '';
    }else if(select1 < select2 && input_value != ''){
        document.getElementById("atention1").innerHTML = '';
        document.getElementById("atention2").innerHTML = '';
        document.getElementById("AddBtn").disabled = false;
    }
  }

　// 予定のタイトル
  document.getElementById("AddTitle").onchange = function(){
    var input_value = document.getElementById("AddTitle").value;
    var select1 = Number(document.getElementById("timeSelect1").value.replace(":00:00", ""));
    var select2 = Number(document.getElementById("timeSelect2").value.replace(":00:00", ""));

    if(input_value == ''){
        document.getElementById("atention2").innerHTML = '※予定のタイトルは入力必須です';
        document.getElementById("AddBtn").disabled = true;
    }else if(input_value != '' && select1 == 999 || select2 == 999){
        document.getElementById("AddBtn").disabled = true;
        document.getElementById("atention1").innerHTML = '※時刻を入力してください';
    }else if(input_value != '' && select1 > select2){
        document.getElementById("atention2").innerHTML = '';
    }else if(input_value != '' && select1 < select2){
        document.getElementById("atention1").innerHTML = '';
        document.getElementById("atention2").innerHTML = '';
        document.getElementById("AddBtn").disabled = false;
    }
  }


  //読み込み時にtd内のdivの高さ調整
  function marginTop(){
          
    // tdタグの合計を取得
    var td_count = $('#main_table').data('td');

    if(td_count >= 36){
      // 36個以上なら６行
      var height_line = 6;
    }else if(td_count >= 29){
      // tdタグが29個以上で５行に確定
      var height_line = 5;
    }else if(td_count <= 28){
      // tdタグが何行になるか計算
      var height_line = Math.round(td_count / 7);
    }

    // 画面高さ取得
    var client_height = window.innerHeight;

    // 各パーツの高さ取得
    var common_header = $('.common_header').height();
    var topParts = $('.topParts').height();
    var yobi_thead = $('.yobi_thead').height();
    var td_padding = 4 * height_line;
    var day_and_syuku_disp_area = $('.day_and_syuku_disp_area').height();
    var footer = $('.footer').height();
     

    // 不用高さ排除
    var delete_other_height = common_header + topParts + yobi_thead + td_padding + (day_and_syuku_disp_area * height_line) + footer;

    // tdのちょうどいい高さを算出 40px 15px 50px 50px 20px*height_line
    var td_fit_height = (client_height - delete_other_height) / height_line;

    $(".memos").height(td_fit_height + 'px');
  }

  marginTop();    

  //読み込み時にtd内のdivの高さ調整
  function resize() { 
      marginTop();    
  }

  window.onresize = resize

});