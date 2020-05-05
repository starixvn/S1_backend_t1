<?php
	$current_template = $this->utils->getOperatorSetting('player_center_template');
	$default_template = $this->utils->getConfig('new_player_center_default_template');
?>


<style type="text/css">
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

		<!--
			Modification : Add field to upload system logo
			May 14, 2017
		-->
		<form id="frmUploadSBELogo" action="<?php echo site_url('system_management/upload_sbe_logo'); ?>" method="POST" enctype="multipart/form-data">
			<table class="table table-hover table-striped table-bordered">
				<tr><th colspan="2"><?= lang('SBE Logo'); ?></th></tr>
				<tr>
					<td class="col-md-4">
						<input type="checkbox" name="setDefaultLogo" id="setDefaultLogo" <?= $useSysDefault ?> >
						<label for="setDefaultLogo" class="control-label"> <?= lang('Use default system logo.'); ?></label>
					</td>
					<td class="col-md-8" rowspan="3"><?=lang('Current system logo')?> : <br/><br/>
						<?php // OGP-1509: all the conditionals are merged in controller method.  rupertc 9/22/2017
						/*
						<?php if ($this->utils->useSystemDefaultLogo() || !$this->utils->isUploadedLogoExist() || !$this->utils->isLogoOperatorSettingsExist() || !$this->utils->isLogoSetOnDB()): ?>
							(sbelogo)
							<img src="<?= $this->utils->getDefaultLogoUrl() ?>" alt="">
                        <?php else: ?>
                        	(logo icon)
                        	<img src="<?= $this->utils->setSBELogo() ?>" alt="">
                        <?php endif; ?>
                        */ ?>
                        <img src="<?= $logo_image ?>" alt="">
					</td>
				</tr>
				<tr>
					<td class="col-md-4">
						<?=lang('Upload File')?> :
						<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" class="form-control input-sm">
					</td>
				</tr>
				<tr>
					<td class="col-md-4"><button type="submit" class="btn btn-primary"><?= lang('Save'); ?></button></td>
				</tr>
			</table>
		</form>
		<!-- End of modificaton -->

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
            <input type="submit" class="btn btn-primary" value="<?php echo lang('Save'); ?>">
        </div>
    </div>
</form>
	</div>

	</div>

</div>

<script type="text/javascript">
	var  base_url = "<?=base_url()?>";

	$(document).ready(function(){
		window.URL = window.URL || window.webkitURL;

		$("#frmUploadSBELogo").submit( function( e ) {
			var useDefaultLogo = $("#setDefaultLogo");

			if (useDefaultLogo.is(":checked")) {
				return;
			}

		    var form = this;
		    e.preventDefault(); //Stop the submit for now
		                                //Replace with your selector to find the file input in your form
		    var fileInput = $(this).find("input[type=file]")[0],
		        file = fileInput.files && fileInput.files[0];

		    if( file ) {
		        var img = new Image();

		        img.src = window.URL.createObjectURL( file );

		        img.onload = function() {
		            var width = img.naturalWidth,
		                height = img.naturalHeight;

		            window.URL.revokeObjectURL( img.src );

		            if(width <= 400 && height <= 45) {
		                form.submit();
		            }
		            else {
		            	alert("Image height must not be greater than 45 pixels and width must not be greater than 400 pixels.");
		            }
		        };
		    }
		    else {
		    	alert("No file was input or browser doesn't support client side reading");
		        //form.submit();
		    }

		});

        $('.system_settings_form .nav-tabs li:first-child a').trigger('click');
	});
    // resizeSidebar();

</script>
<script type="text/javascript">
$('input#fileToUpload').on('change', function () {
	$('input#setDefaultLogo').removeAttr('checked');
});
</script>