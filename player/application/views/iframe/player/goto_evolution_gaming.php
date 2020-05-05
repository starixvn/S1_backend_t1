<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo @$platformName; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="/<?=$this->utils->getPlayerCenterTemplate()?>/fav.ico" type="image/x-icon" />
    <style>
        *{padding:0;margin:0;}
        html , body {height:100%;overflow:hidden;}
        iframe{border:none;}
    </style>
    <script type="text/javascript">
        <?php
            $loggedPlayerUsername=$this->authentication->getUsername();
            echo $this->utils->generateStatCode($loggedPlayerUsername);
        ?>
    </script>
    <script type="text/javascript">
        window.addEventListener('message', function(e) {
            var frameID = 'evoFrame';
            var frame = document.getElementById(frameID);
            if (e.data == 'changeForStandart') {
                frame.width="800";
                frame.height="680";
            } else if (e.data == 'changeForImmersive') {
                frame.width="100%";
                frame.height="100%";
            } else if (e.data == 'changeForMultiWindow') {
                frame.width="100%";
                frame.height="100%";
            }
        }, false);
    </script>
</head>
<body>

<?php if(isset($empty) && $empty) : ?>
    <?php echo lang('goto_game.sysMaintenance');  ?>
<?php else : ?>
    <iframe id="evoFrame" allowfullscreen <?php echo isset($iframeName) && !empty($iframeName) ? 'name="' . $iframeName . '"' : ''; ?>
            width="100%" height="100%" src="<?php echo $url; ?>"></iframe>
<?php endif; ?>

</body>
</html>