
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
  });


  /*------------------------------------------編集フォーム---------------------------------------------*/
  $(".editbtns").click(function(){
      
      //日付を取得し変数idに代入
      var id = $(this).attr('id');
      //編集フォームのspanタグと<input type=hidden>タグのvalueに日付を書く
      $("#EditDay").text(id);
      $("#hiddenday").val(id);

      //メモのテキストを取得して変数daymemoに格納
      var Memo = '#memo' + id;
      var daymemo = $(Memo).text();
      //編集フォームのtextareaタグにメモを書く
      $("#EditPreviwe").text(daymemo);

      //背景色を取得して変数Tdcolorに格納
      var Td = '#td' + id;
      var Tdcolor = $(Td).css('background-color');

      //チェックをつける判定のために各色の変数を作りカラーコードを格納
      var green = 'rgb(102, 255, 102)';
      var yellow = 'rgb(255, 255, 136)';
      var bule = 'rgb(117, 169, 255)';
      var purple = 'rgb(194, 153, 255)';
      var red = 'rgb(255, 79, 80)';
      var orange ='rgb(255, 165, 0)';
      var white ='rgb(255, 255, 255)';

      //背景色に選択されているものと同じ色にラジオのチェック入れる
      if(Tdcolor == green){
        $('input:radio[name="td_color"]').val(["#66FF66"]);
      }else if(Tdcolor == yellow){
        $('input:radio[name="td_color"]').val(["#FFFF88"]);
      }else if(Tdcolor == bule){
        $('input:radio[name="td_color"]').val(["#75A9FF"]);
      }else if(Tdcolor == purple){
        $('input:radio[name="td_color"]').val(["#C299FF"]);
      }else if(Tdcolor == red){
        $('input:radio[name="td_color"]').val(["#FF4F50"]);
      }else if(Tdcolor == orange){
        $('input:radio[name="td_color"]').val(["#FFA500"]);
      }else if(Tdcolor == white){
        $('input:radio[name="td_color"]').val(["#FFFFFF"]);
      }else{
        //デフォルトで白になるよう上記条件以外では白にチェック入れる
        $('input:radio[name="td_color"]').val(["#FFFFFF"]);
      }

      //文字色を取得して変数Memocolorに代入
      var Memocolor = $(Memo).css('color');

      //チェックをつける判定のために各色の変数を作りカラーコードを格納
      var Tred = 'rgb(238, 0, 0)';
      var Tbule = 'rgb(0, 0, 255)';
      var Tgreen = 'rgb(0, 153, 0)';
      var Twhite = 'rgb(255, 255, 255)';
      var Tblack = 'rgb(0, 0, 0)';

      //文字色に選択されているものと同じ色にラジオのチェック入れる
      if(Memocolor == Tred){
        $('input:radio[name="text_color"]').val(["#EE0000"]);
      }else if(Memocolor == Tbule){
        $('input:radio[name="text_color"]').val(["#0000FF"]);
      }else if(Memocolor == Tgreen){
        $('input:radio[name="text_color"]').val(["#009900"]);
      }else if(Memocolor == Twhite){
        $('input:radio[name="text_color"]').val(["#FFFFFF"]);
      }else if(Memocolor == Tblack){
        $('input:radio[name="text_color"]').val(["#000000"]);
      }else{
        //デフォルトで黒になるよう上記条件以外では白にチェック入れる
        $('input:radio[name="text_color"]').val(["#000000"]);
      }


      //編集ボタン押したらポップアップ開く
      $("#popback").fadeIn("fast");
      $("#Editform").fadeIn("fast");

    });

    //閉じるボタン押したらポップアップ閉じる  
    $(".closebtn").click(function(){
     $("#Editform").fadeOut("fast");
      $("#popback").fadeOut("fast");
    });

  /*------------------------------------------削除フォーム---------------------------------------------*/
  $(".deletebtns").click(function(){
    
    /*
    //変数popを定義し、削除ボタンで押された日付と同じ削除divタグのidを生成して代入する
    var pop ='#delete'+id;
    $(pop).fadeIn("fast");
    $("#popback").fadeIn("fast");*/


    //画面下の月のセレクトボタンより月を取得しMonth変数に格納
    var Month = $("#SelectBtn").text();
    console.log(Month);
    //削除フォームのspanタグに月を書く
    $("#jMonth").text(Month);

    //日付を取得し変数idに代入
    var id = $(this).attr('id');
    //削除フォームのspanタグと<input type=hidden>タグのvalueに日付を書く
    $("#jDay").text(id);
    $("#Hiddenday").val(id);

    //削除するメモ(カラム)のid番号を取得し、<input type=hidden>タグのvalueにそのid番号を書く
    var DivClassName = '#hiddenID' + id;
    var DeleteID = $(DivClassName).attr('class');
    $("#tableID").val(DeleteID);


    //削除ボタン押したらポップアップ開く
    $("#Deleteform").fadeIn("fast");
    $("#popback").fadeIn("fast");


  });

  //閉じるボタン押したらポップアップ閉じる
  $(".closebtn2,.CancelBtn").click(function(){
    $("#Deleteform").fadeOut("fast");
    $("#popback").fadeOut("fast");
  });


});
