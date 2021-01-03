
$(function(){


  /*------------------------------------------新規登録フォーム---------------------------------------------*/
  $(".addbtns").click(function(){

    //日付を取得し変数idに代入
    var id = $(this).attr('id');
    //編集フォームのspanタグと<input type=hidden>タグのvalueに日付を書く
    $("#AddDay").text(id);
    $("#AddHiddenday").val(id);

    //新規登録ボタン押したらポップアップ開く
    $("#Addform").fadeIn("fast");
    $("#popback").fadeIn("fast");
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
    var input_value = document.getElementById("AddTitle").value;
    var select1 = Number(document.getElementById("timeSelect1").value.replace(":00:00", ""));
    var select2 = Number(document.getElementById("timeSelect2").value.replace(":00:00", ""));

    if(select1 >= select2 && select2 != 999){
      document.getElementById("atention1").innerHTML = '※開始時刻に誤りがあります';
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

});