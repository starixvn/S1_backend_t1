<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->utils->getPlayertitle();?></title>
    <?=$active_template->renderStyles(); ?>
    <?=$active_template->renderScripts(); ?>
</head>
<body class="announcements_list">
    <div style="overflow-y:auto; height:450px;">
        <?php foreach ($cms_list as $key => $list) : ?>
            <ul>
                <li>
                    <span><b><?= $list['title']; ?></b></span><br/>
                    <span><?= $list['content']; ?></span>
                </li>
            </ul>
        <?php endforeach; ?>
    </div>
</body>
</html>