<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $platformName;?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <style>
        html{
            height: 100%;
        }
        body{
            background: rgba(255,84,54,1);
            background: -moz-radial-gradient(center, ellipse cover, rgba(255,84,54,1) 0%, rgba(163,19,6,1) 100%);
            background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(255,84,54,1)), color-stop(100%, rgba(163,19,6,1)));
            background: -webkit-radial-gradient(center, ellipse cover, rgba(255,84,54,1) 0%, rgba(163,19,6,1) 100%);
            background: -o-radial-gradient(center, ellipse cover, rgba(255,84,54,1) 0%, rgba(163,19,6,1) 100%);
            background: -ms-radial-gradient(center, ellipse cover, rgba(255,84,54,1) 0%, rgba(163,19,6,1) 100%);
            background: radial-gradient(ellipse at center, rgba(255,84,54,1) 0%, rgba(163,19,6,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff5436', endColorstr='#a31306', GradientType=1 );

            background-size: cover;
            background-repeat: no-repeat;
            position: absolute;
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            font-family: Arial,Helvetica,"Microsoft Yahei",微软雅黑,STXihei,华文细黑,sans-serif;
            font-size: 14px;
            text-align: center;
        }

        .wrapper {
            text-align: center;
        }

        .wrapper > p{
            color: #ffff9a;
            font-weight: bold;
            text-transform: uppercase;
            margin: 45% 0 10px 0;
        }

        .button {
            position: relative;
            display: block;
        }

        .button img{
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            border-radius: 20px;
        }

        .download{
            border-radius: 20px;
            padding: 10px 0;
            display: inline-block;
            border: 1px solid #ffff9a;
            color: #ffff9a;
            text-transform: uppercase;
            text-decoration: none;
            position: absolute;
            bottom: 80px;
            min-width: 60%;
            left: 20%;
            text-align: center;
        }

        .download:hover{
            background-color: #ffff9a;
            color: #450202;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <p><?php echo $login_text ?></p>
        <a class="button" href="javascript:void(0);" onclick="loadApp()" ><img src="<?php echo $image_url ?>" width="100" /></a>
    </div>

   <!--  <form name="agin_form" method="POST" action="<?php echo $url;?>" target="agin_iframe">
        
    </form> -->
    <a href="javascript:void(0);" onclick="downloadApp()" class="download"><?php echo $download_text ?></a>
    <script>
        function loadApp() {
            document.location="<?php echo $url ?>";
        }

        function downloadApp($url) {
            document.location="http://agmbet.com/universal/AG_setup.apk";
            // window.location.assign("http://agmbet.com/universal/AG_setup.apk")
            // window.open("http://agmbet.com/universal/AG_setup.apk");
        }

    </script>

</body>
</html>