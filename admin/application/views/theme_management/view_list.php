<style type="text/css">
	.text-right {
		text-align: right !important;
	}
	.col-xs-5ths,
	.col-sm-5ths,
	.col-md-5ths,
	.col-lg-5ths {
	    position: relative;
	    min-height: 1px;
	    padding-right: 15px;
	    padding-left: 15px;
	}

	.col-xs-5ths {
	    width: 20%;
	    float: left;
	}

	@media (min-width: 768px) {
	    .col-sm-5ths {
	        width: 20%;
	        float: left;
	    }
	}

	@media (min-width: 992px) {
	    .col-md-5ths {
	        width: 20%;
	        float: left;
	    }
	}

	@media (min-width: 1200px) {
	    .col-lg-5ths {
	        width: 20%;
	        float: left;
	    }
	}
	.btn.active {
		border: 3px solid #ff0000;
	}

    .style-in-iframe {
        position: absolute;
        left: 0;
        width: 100%;
        top: -30px;
        overflow-y: scroll;
        height: 1050px;
    }
    .style-in-iframe>.panel-primary{
        border: none;
    }
    /*.custom-container{
        display: none;
    }*/

</style>
<div id="theme_main_content">
    <div class="panel panel-primary">
<h1 class="page-header <?=$this->utils->getConfig('use_new_sbe_color') ? 'p-l-15 p-r-15' : ''?>"><span id="spnpPageTitle"><?=lang('Theme')?></span>
	<?php if ($this->permissions->checkPermissions('add_theme')) :?>
		<a type="button" id="btn-addTheme" name="btn-addTheme" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" data-original-title="" title=""><i class="glyphicon glyphicon-plus" style="color:white;" data-placement="bottom"></i><?= lang('Add Theme') ?></a>
	<?php endif; ?>
</h1>

        <div class="panel-body" id="player_panel_body">
<form id="form-theme" action="/theme_management/save" method="POST">
<div class="row">
	<?php foreach ($themes as $theme): ?>
		<div class="col-md-5ths">
			<label class="btn btn-block btn-default <?=$selected_theme == $theme ? 'active' : ''?>" style="height: 283px; margin-bottom: 30px;">
				<input type="radio" name="theme" value="<?=$theme?>" autocomplete="off" <?=$selected_theme == $theme ? 'checked' : ''?> style="display: none;">
				<?php if(file_exists($this->utils->getThemesTemplatePath() .'/img_themes/playercenter_'.$theme.'.jpg')) :  ?>
					<img src="<?php echo base_url().'upload/themes/'.$this->utils->getPlayerCenterTemplate().'/img_themes/playercenter_'.$theme.'.jpg' ?>" alt="<?=ucfirst($theme)?>" height="230" width="100%" style="display: block; margin-bottom: 10px;"/>
				<?php else: ?>
					<img src="/resources/images/themes/playercenter_<?=$theme?>.jpg" alt="<?=ucfirst($theme)?>" height="230" width="100%" style="display: block; margin-bottom: 10px;"/>
				<?php endif;?>
				<a href="/theme_management/downloadThemes/<?= $theme ?>" class="pull-right" style="position: relative; font-size: 18px;">
						<i class="fa fa-download" id="icon"></i>
				</a>
				<?=ucfirst($theme)?>
			</label>

		</div>
	<?php endforeach ?>
</div>
</form>

<div class="well text-center">
	<button type="button" id="btn-preview" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('Preview')?></button>
	<button type="button" id="btn-reset" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('Reset')?></button>
	<button type="submit" id="btn-save" form="form-theme" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><?=lang('Save')?></button>
</div>
        </div>
    </div>
</div>
<?php if ($this->permissions->checkPermissions('add_theme')) :?>
	<div class="modal fade in" id="mdl-addTheme" tabindex="-1" role="dialog" aria-labelledby="label_player_notes">
	  <div class="modal-dialog" role="document">
	      <div class="modal-content">
	          <div class="modal-header">
	              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                  <span aria-hidden="true">&times;</span>
	              </button>
	              <h4 class="modal-title"><?= lang('Upload New Theme') ?></h4>
	          </div>
	          <form id="form_upload_themes" action="<?php echo site_url('/theme_management/upload_new_themes/');?>" method="post" enctype="multipart/form-data">
		          <div class="modal-body">
		          	<div class="row">
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
                                <font style="color:red;">*</font> <?= lang('Theme Name') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="text" id="txtName" name="txtName">
				    		</div>
					  	</div>
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
                                <font style="color:red;">*</font> <?= lang('CSS Themes') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_css_theme" name="file_css_theme[]" hidden>
				    		</div>
					  	</div>
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
                                <font style="color:red;">*</font> <?= lang('Image') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_image" name="file_image[]" hidden>
				    		</div>
					  	</div>
		            </div>
		          </div>
		          <div class="modal-footer">
		          	<div class="well text-center">
						<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal"><?=lang('Cancel')?></button>
						<button type="submit" id="btn-save-new-theme" form="form_upload_themes" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><?=lang('Save')?></button>
					</div>
		          </div>
	          </form>
	      </div>
	  </div>
	</div>
<?php endif;?>
<script type="text/javascript">
	var preview_expiry;

	if ( self !== top ) {
		$('nav').remove();
		$('#sidebar-wrapper').remove();
		$('#theme_main_content').addClass('style-in-iframe');
        $('#main_content').css({ height: "1000px"});
	}

	$(document).ready(function(){

	});

	$('input[name="theme"]').change(function() {
		$('.row label').removeClass('active');
		$('input[name="theme"]:checked').parent('label').addClass('active');
	});

	$('#btn-preview').click(function() {
		var theme = $('input[name="theme"]:checked').val();
		window.open('/theme_management/preview/' + theme, 'preview');

		clearInterval(preview_expiry);
		var preview_expiry_countdown = 60;
		$('#btn-preview').text('<?=lang('Preview')?> (' + preview_expiry_countdown-- + ')');
		preview_expiry = setInterval(function() {
			$('#btn-preview').text('<?=lang('Preview')?> (' + preview_expiry_countdown-- + ')');
			if (preview_expiry_countdown < 0) {
				$('#btn-preview').text('<?=lang('Preview')?>');
				clearInterval(preview_expiry);
			}
		}, 1000);
	});

	$('#btn-save').click(function(e) {
		return confirm("<?=lang('sys.sure')?>");
	});

	$('#btn-reset').click(function() {
		$('#form-theme')[0].reset();
		$.get('/theme_management/reset', function() {
			$('input[name="theme"]').trigger('change');
		});
	});

	$('#btn-addTheme').click(function() {
		$('input').val('');
		/*$('#file_image').val('');
		$('#txtName').val('');*/
		$('#mdl-addTheme').modal('show');
	});

</script>