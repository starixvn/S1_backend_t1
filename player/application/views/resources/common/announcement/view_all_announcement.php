<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->utils->getPlayertitle();?></title>
	<link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />
    <?=$active_template->renderStyles(); ?>
    <?=$active_template->renderScripts(); ?>
    <script type="text/javascript" src="<?= $this->utils->thirdpartyUrl('jquery-marquee/1.5.0/jquery.marquee.js') ?>"></script>
</head>
<body class="all_announcement">
    <div style="cursor:pointer" class="marquee" onclick="announcementPopup();">
        <?php foreach ($announcements as $a): ?>
            <?php $title = strip_tags($a['title']); ?>
            <?php $content = strip_tags($a['content']); ?>
            <span><b><?=$title;?></b> <?=$content;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <?php endforeach;?>
    </div>
    <script type="text/javascript">
        $('.marquee').marquee({
            //speed in milliseconds of the marquee
            //duration: 15000,
            speed: 60,
            //gap in pixels between the tickers
            gap: 50,
            //time in milliseconds before the marquee will start animating
            delayBeforeStart: 0,
            //'left' or 'right'
            direction: 'left',
            //true or false - should the marquee be duplicated to show an effect of continues flow
            duplicated: false // Do not enable this, because it will affects the speed.
        });

        $(".marquee").on("mouseover mouseout", function(e){
            $(this).marquee("toggle");
        });

        <?php 
            $hostNameArr = explode('.', $_SERVER['HTTP_HOST']); 
            unset($hostNameArr[0]);
        ?>
        document.domain = "<?= implode('.', $hostNameArr); ?>";

        function announcementPopup() {
            if (<?= $this->operatorglobalsettings->getSettingIntValue('announcement_option') ?> == 1) {
                window.open("<?php echo site_url('pub/announcement_popup'); ?>", "_blank", "toolbar=yes, scrollbars=yes, resizable=no, top=100, left=10, width=600, height=450");
            } else {
                parent.postMessage(JSON.stringify({
                    "act": "announcement_popup",
                    "success": true
                }),"*");
            }
        }
    </script>
</body>
</html>