<style type="text/css">
.disabled{
	color: lightgray;
	outline: none;
}
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?= lang('sys_settings_smart_backend') ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

    <ul class="nav nav-tabs">
    	<li><a data-toggle="tab" href="#__sbe_settings"><?= lang('sys_settings_smart_backend') ?></a></li>
        <?php foreach ($settings as $category_name => $category_settings): ?>
        <li><a data-toggle="tab" href="#<?=$category_name?>_settings"><?=$category_settings['name']?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content panel-default">
    	<div id="__sbe_settings" class="tab-pane fade">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
						<th class="col-md-4"><?php echo lang('aff.al36'); ?></th>
						<th class="col-md-8"><?php echo lang('Value'); ?></th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td><?= lang('SBE Logo'); ?></td>
						<td>
							<form id="frmUploadSBELogo" action="<?php echo site_url('system_management/upload_sbe_logo'); ?>" method="POST" enctype="multipart/form-data">
								<input type="checkbox" name="setDefaultLogo" id="setDefaultLogo" <?= $useSysDefault ?> >
								<label for="setDefaultLogo" class="control-label"> <?= lang('Use default system logo.'); ?></label>
								<?=lang('Upload File')?> :
								<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" class="form-control input-sm">
								<?=lang('Current system logo')?> :
								<img src="<?= $logo_image ?>" alt="" id="uploadLogo">
								<div>
								<button type="submit" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?= lang('Save'); ?></button>
								</div>
							</form>
						</td>
					</tr>

					<?php //auto_logout_admin_account?>
					<tr>
						<td>
							<?php echo lang('operator_settings.admin_sess_expire'); ?>
						</td>
						<td>
							<form id="frmAutoLogout" action="<?php echo site_url('system_management/setting_admin_auto_logout'); ?>" method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<div class="radio">
										<label for="enable_admin_auto_logout">
											<input type="radio" name="enable_admin_auto_logout" id="enable_admin_auto_logout" value='on' <?= $enable_admin_auto_logout ? "checked" : ''; ?>>
											<?php echo lang('operator_settings.admin_sess_expire.1'); ?>
										</label>
									</div>
									<div>
										<input type="number" value="<?= $auto_logout_sess_expiration ?: $this->utils->getConfig('default_auto_logout_sess_expiration') ?>" name="auto_logout_sess_expiration" id="auto_logout_sess_expiration" min='0' class="input-sm user-success <?= $enable_admin_auto_logout ? '' : 'disabled'; ?>" <?= $enable_admin_auto_logout ? '' : 'readonly'; ?> required> <?= lang('minutes auto logout if no operation') ?>
										<div style="color: red;font-size: 12px;">
											<?php echo lang('operator_settings.admin_sess_expire.alert'); ?>
										</div>
									</div>
									<div class="radio">
										<label for="disable_admin_auto_logout">
											<input type="radio" name="enable_admin_auto_logout" id="disable_admin_auto_logout" value='off' <?= $enable_admin_auto_logout ? "" : 'checked'; ?>>
											<?php echo lang('operator_settings.admin_sess_expire.2'); ?>
										</label>
									</div>
								</div>
								<div>
									<button type="submit" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?= lang('Save'); ?></button>
								</div>
							</form>
						</td>
					</tr>
					</tbody>

				</table>
    	</div>



        <?php foreach ($settings as $category_name => $category_settings): ?>
        <div id="<?=$category_name?>_settings" class="tab-pane fade">
        	<form class="system_settings_form" action="<?php echo site_url('system_management/save_system_settings'); ?>" method="POST">
	            <table class="table table-hover table-striped table-bordered">
	                <thead>
	                    <tr>
	                    <th class="col-md-4"><?php echo lang('aff.al36'); ?></th>
	                    <th class="col-md-8"><?php echo lang('Value'); ?></th>
	                    </tr>
	                </thead>
	                <tbody>
	                <?php foreach ($category_settings['options'] as $setting_name => $setting_info) : ?>
	                    <tr>
	                        <td>
	                        <?php
							if (lang($setting_name) != $setting_name) {
                                echo lang($setting_name);
							} else {
								if (isset($setting_info['params']['label_lang']) && !empty($setting_info['params']['label_lang'])) {
									echo lang($setting_info['params']['label_lang']);
								} elseif (isset($setting_info['note']) && !empty($setting_info['note'])) {
									echo $setting_info['note'];
								} else {
									echo $setting_name;
								}
							}
	                        ?>
	                        </td>
	                    <td>
	                    <div class="form-group">
	                        <?=(isset($setting_info['params'])) ? Operatorglobalsettings::renderFormElement($setting_name, $setting_info) : ''?>
	                    </div>
	                    </td>
	                    </tr>
	                <?php endforeach; ?>
	                </tbody>
	            </table>
	            <div class="row">
		            <input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save'); ?>">
		        </div>
	        </form>
        </div>
        <?php endforeach; ?>


    </div>

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
		$('.panel_main .nav-tabs li:first-child a').trigger('click');

	});
    // resizeSidebar();

	$('#enable_admin_auto_logout').on("click",function(){
		$('#auto_logout_sess_expiration').removeAttr('readonly');
		$('#auto_logout_sess_expiration').removeClass('disabled');
	});
	$('#disable_admin_auto_logout').on("click",function(){
		$('#auto_logout_sess_expiration').attr('readonly','readonly');
		$('#auto_logout_sess_expiration').addClass('disabled');
	});

	$("#collapseSubmenu_sys_settings").addClass("in");
	$("a#system_settings").addClass("active");
	$("a#sys_settings_smart_backend").addClass("active");

	$('input#fileToUpload').on('change', function () {
		$('input#setDefaultLogo').removeAttr('checked');
	});
</script>