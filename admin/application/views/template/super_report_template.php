<?php
$isSuperModeOnMDB=$this->utils->isSuperModeOnMDB();
?>
<!DOCTYPE html>
<html lang='en'>
    <head>

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="renderer" content="webkit" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="<?php echo isset($description) ? $description : ''; ?>"/>
    <meta name="keywords" content="<?php echo isset($keywords) ? $keywords : ''; ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <title>
        <?php if($this->utils->isEnabledFeature('include_company_name_in_title')) : ?>
            <?=htmlspecialchars($company_title);?> -
        <?php endif; ?>
        <?=$title?>
    </title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

    <?=$_styles?>
    <?=$_scripts?>

    </head>

    <body>

        <div id="main_content">
            <?=$main_content?>
        </div>

<!-- customize admin css -->
<style type="text/css">
<?php echo isset($admin_css) ? $admin_css : '';?>
</style>

    </body>
</html>
