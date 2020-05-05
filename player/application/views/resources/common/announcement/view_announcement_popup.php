<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->utils->getPlayertitle();?></title>
	<link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />
    <?=$active_template->renderStyles(); ?>
    <?=$active_template->renderScripts(); ?>
</head>
<body class="announcement_detail">
<div class="announcements_popup">
    <?php foreach ($announcements as $a): ?>
        <h2><?=strip_tags($a['title']);?></h2><br/>
        <span><?=$a['date'];?></span>
        <p>&nbsp&nbsp&nbsp&nbsp<?=strip_tags($a['content']);?></p>
        <br/>
        <hr>
    <?php endforeach;?>
</div>
</body>
</html>