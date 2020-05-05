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
<body class="announcements_nav_list">
    <?php $activeId = (isset($cms_category_list[0]) ? $cms_category_list[0]['id'] : ""); ?>
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($cms_category_list as $key => $list) : ?>
        <?php  ?>
        <li class="Tabli <?= ($list['id'] == $activeId) ? "active" : "" ?>" role="presentation"><a href="<?= "#cms".$list['id'] ?>" role="tab" data-toggle="tab"><?= strip_tags($list['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php foreach ($cms_category_list as $key => $list) : ?>
        <div role="tabpanel" class="tab-pane <?= ($list['id'] == $activeId) ? "active" : "" ?>" id="<?= "cms" . $list['id'] ?>">
            <ul class="acontentUL">
                <?php foreach ($cms_list as $_list) : ?>
                <?php if ($_list['categoryId'] == $list['id']) : ?>
                <li><?= strip_tags($_list['content']); ?></li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>