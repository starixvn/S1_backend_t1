<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1" />
            <title><?php echo @$platformName; ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <style>
            body {
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body style="overflow:hidden"  onload="document.FRM.submit()" onresize="ResizeIFrame()">
        <form action="<?php echo $url ?>" id="FRM" method="POST" name="FRM" target="<?php echo @$platformName; ?>"></form>
        <iframe name="<?php echo @$platformName; ?>" src="about:blank" style="width:100%;height:100%; position:relative;border:0px;outline:none;overflow:hidden" id="iFrameGame"></iframe>
        <script>
            var iFrameGame = document.getElementById("iFrameGame");
            window.addEventListener("message", function (e) {
                if (e == null || e.data == null) {
                    return;
                }
                
                switch (e.data.event) {
                    case "ReadyToPlay": ResizeIFrame(); break;
                }
            }, false);
        
            ResizeIFrame = function () {
                iFrameGame.contentWindow.postMessage({ event: 'iFrameSize', innerWidth: window.innerWidth, clientWidth: document.body.clientWidth, innerHeight: window.innerHeight, clientHeight: document.body.clientHeight }, "*");
            }
        </script>        
    </body>
</html>