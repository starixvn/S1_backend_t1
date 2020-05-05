<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo @$platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="shortcut icon" href="/<?=$this->utils->getPlayerCenterTemplate()?>/fav.ico" type="image/x-icon" /> -->
<link rel="shortcut icon" href="<?= !empty($favicon_brand) ? $favicon_brand : $this->utils->getPlayerCenterFaviconURL(); ?>" type="image/x-icon" />
<link rel="stylesheet" href="/stable_center2/css/font-awesome.min.css?v=<?php echo $this->CI->utils->getCmsVersion(); ?>" />
<style>

*{padding:0;margin:0;}
    html , body {height:100%;overflow:hidden;}
    iframe{border:none;}
    body {
        background-color: #001e5a;
        font-family: Tahoma, Helvetica, Arial, "Microsoft Yahei", 微软雅黑, STXihei, 华文细黑, sans-serif;
        margin: 0;
    }
    .container {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }
    .row {
        margin-right: -15px;
        margin-left: -15px;
    }
    .content {
        height: 100%;
    }
    .container .row.content>.fishing-content {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
    .fishing-size {
        width: 70%;
    }
    .img-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: -100px;
    }
    a.fishing-playbtn {
        background-color: #004595;
        margin: 0 auto;
        border: 0;
        height: 55px;
        width: 85%;
        line-height: 56px;
        color: #fff;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        font-size: 15px;
        text-align: center;
        display: block;
        padding: 0 0px;
        text-transform: uppercase;
    }
    a.fishing-downloadbtn {
        background-color: #004595;
        margin: 0 auto;
        border: 0;
        height: 55px;
        width: 85%;
        color: #fff;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        font-size: 15px;
        text-align: center;
        display: block;
        padding: 0 0px;
        line-height: 56px;
        text-transform: uppercase;
    }
    .fa-gamepad:before {
        content: "\f11b";
        margin-right: 15px;
        font-size: 25px;
    }
    .fa-download:before {
        content: "\f019";
        font-size: 25px;
        margin-right: 15px;
    }
    .download-content {
        margin-top: 30px;
    }
    
    .play-content {
        margin-top: -40px;
    }
</style>
<script type="text/javascript">
function launch_mobile_game(){
    window.location = '<?php echo $url; ?>';
}
</script>
</head>
<body onload="launch_mobile_game()">
<!-- <?php if(isset($empty) && $empty) { echo lang('goto_game.sysMaintenance'); } else { ?> -->
    <div class="container">
        <div class="row content">
            <div class="fishing-content">
                <div class="fishing-size">
                    <div class="img-logo">
                        <img class="img-logo-image" src="<?php echo $logo_url; ?>">
                    </div>
                    <div class="play-content">
                        <a href="<?php echo $url; ?>" class="fishing-playbtn"><i class="fa fa-gamepad"></i><?php echo lang('goto_game.tapToPlay') ?></a>
                    </div>
                    <div class="download-content">
                        <a href="<?php echo $download_url; ?>" class="fishing-downloadbtn"><i class="fa fa-download"></i><?php echo lang('goto_game.downloadApp'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
</html>
