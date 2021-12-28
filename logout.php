<!DOCTYPE html>
<html lang="ja">
  <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
      <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300&display=swap" rel="stylesheet">
      <style>
        body{
          margin:0;
          padding:0;
          font-family: 'Noto Sans JP', sans-serif;
        }

        .wrap{
            display: block;
            width: 350px;
            margin-top:20px;
            margin-left:auto;
            margin-right:auto;
            padding:20px;
            border:solid 1px #DDDDDD;
            border-radius:3px;
            box-shadow: 0px 2px 7px #3338;
            background:#fcfcfa;
            border:none;
        }

        .logout_btn{
            width: 100%;
            height:40px;
            border:none;
            border-radius:3px;
            background:#000;
            color:#fff;
            font-size:15px;
            margin-top:30px;
            cursor: pointer;
        }


        /* PC表示 */
        @media (min-width:800px) {
            .login{
                text-align:center;
                font-size:30px;
                padding:30px;
            }

            .logo_img{
                display: block;
                width: 200px;
                margin-left:auto;
                margin-right:auto;
            }

        }

        /* スマホ表示 */
        @media (max-width:800px) {

            .wrap{
                width: 80%;
            }

            .logo_img{
                display: block;
                width: 200px;
                margin-left:auto;
                margin-right:auto;
            }
        }
      </style>
      <title>ログアウト</title>
  </head>
  <body>
      
      <?php 
        session_start();
        unset($_SESSION['login']);
      ?>

      <main>
        <div class="wrap">
          <div style="font-size:30px;text-align:center;">Calendar</div>
          <div>正常にログアウトしました。</div>
          <a href="./Login/">
            <button class="logout_btn">ログイン画面に戻る</button>
          </a>
        </div>
      </main>

  </body>
</html>