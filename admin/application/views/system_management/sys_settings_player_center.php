<style type="text/css">
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?= lang('Player Center') ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

<form class="system_settings_form" action="<?php echo site_url('system_management/save_system_settings'); ?>" method="POST">
    <ul class="nav nav-tabs">
        <?php foreach ($settings as $category_name => $category_settings): ?>
        <li><a data-toggle="tab" href="#<?=$category_name?>_settings"><?=$category_settings['name']?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content panel-default">
        <?php foreach ($settings as $category_name => $category_settings): ?>
        <div id="<?=$category_name?>_settings" class="tab-pane fade">
            <table class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                    <th class="col-md-4"><?php echo lang('aff.al36'); ?></th>
                    <th class="col-md-8"><?php echo lang('Value'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($category_settings['options'] as $setting_name => $setting_info) { ?>
                    <tr>
                        <td>
                        <?php
                        if (isset($setting_info['params']['label_lang']) && !empty($setting_info['params']['label_lang'])) {
                            echo lang($setting_info['params']['label_lang']);
                        } elseif (isset($setting_info['note']) && !empty($setting_info['note'])) {
                            echo $setting_info['note'];
                        } else {
                            echo $setting_name;
                        }
                        ?>
                        </td>
                    <td>
                    <div class="form-group">
                        <?=(isset($setting_info['params'])) ? Operatorglobalsettings::renderFormElement($setting_name, $setting_info) : ''?>
                    </div>
                    </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>

        <div class="row">
            <input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save'); ?>">
        </div>
    </div>
</form>
	</div>

	</div>

</div>

<script type="text/javascript">
    $("#collapseSubmenu_sys_settings").addClass("in");
    $("a#system_settings").addClass("active");
    $("a#sys_settings_player_center").addClass("active");

	var  base_url = "<?=base_url()?>";

	$(document).ready(function(){
		window.URL = window.URL || window.webkitURL;

        $('.system_settings_form .nav-tabs li:first-child a').trigger('click');
	});
    // resizeSidebar();

</script>
