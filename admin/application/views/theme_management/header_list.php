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

	#form-theme .btn.btn-block {
		line-height: 30px;
	}
	.btn.active {
		background: #a7a7a7;
		color: #fff;
	}
	.btn.active i {
		color: #fff;
	}
	.current-header:before {
		content: "\e013";
	    font-family: 'Glyphicons Halflings';
	    font-size: 18px;
	    position: absolute;
	    left: 15px;
	    background: #0fc5cc;
	    color: #fff;
	    height: 50px;
	    top: 0;
	    bottom: 0;
	    width: 16%;
		line-height: 50px;
		text-align:center;
	}

	.style-in-iframe {
		position: absolute;
		top: -30px;
		left: 20px;
		width: 98%;
	}

	.tooltip-inner {
		margin-top: -4px;
	}
	.tooltip .tooltip-arrow {
		margin-top: -4px;
	}
</style>
<div id="theme_main_content">
<h1 class="page-header"><span id="spnpPageTitle"><?=lang('Header Template')?></span>
	<a type="button" id="btn-setDefault" name="btn-setDefault" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" data-original-title="" title=""> <i class="fa fa-undo" style="color:white;" data-placement="bottom"></i> <?= lang('cashier.110') ?></a>
	<?php if ($this->permissions->checkPermissions('add_header_template')) :?>
		<a type="button" id="btn-addTheme" name="btn-addTheme" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter m-r-10' : 'btn-primary'?>" data-original-title="" title=""><i class="glyphicon glyphicon-plus" style="color:white;" data-placement="bottom"></i><?= lang('Add Header') ?></a>
	<?php endif; ?>
</h1>
<form id="form-theme" action="/theme_management/saveHeader" method="POST">
<div class="row">
    <?php foreach ($builtin_headers as $header): ?>
        <div class="col-md-5ths">
            	<label class="btn btn-block btn-default <?=$selected_header == $header ? 'active current-header' : ''?>" style="height: 50px; margin-bottom: 30px; text-align: left; box-sizing: border-box; padding-left:20%;" data-toggle="tooltip">
                <a href="/theme_management/downloadHeader/<?= $header ?>" class="pull-right" style="position: relative; font-size: 18px;" target="_blank">
                    <i class="fa fa-download" id="icon"></i>
                </a>
                <input type="radio" name="header_template" value="<?=$header?>" autocomplete="off" <?=$selected_header == $header ? 'checked' : ''?> style="display: none;">
				<span class="header_template_name" ><?=ucfirst($header)?></span>
				<span class="header_template_fullname hidden"><?=ucfirst($header)?></span>
            </label>
        </div>
    <?php endforeach ?>
	<?php if (!empty($headers)) :?>
		<?php foreach ($headers as $header): ?>
			<div class="col-md-5ths">
				<label class="btn btn-block btn-default  <?=$selected_header == $header ? 'active current-header' : ''?>" style="height: 50px; margin-bottom: 30px; text-align: left; box-sizing: border-box; padding-left:20%;" data-toggle="tooltip">
					<?php if($selected_header != $header && $this->permissions->checkPermissions('delete_header_template')) :?>
						<a href="javascript:void(0);" onclick="deleteHeader('<?= $header ?>');" class="pull-right" style="position: relative; font-size: 18px;">
							<i class="fa fa-trash" id="icon"></i>
						</a>
					<?php endif; ?>
					<a href="/theme_management/downloadHeader/<?= $header ?>" class="pull-right" style="position: relative; font-size: 18px;" target="_blank">
						<i class="fa fa-download" id="icon"></i>
					</a>
					<input type="radio" name="header_template" value="<?=$header?>" autocomplete="off" <?=$selected_header == $header ? 'checked' : ''?> style="display: none;">
					<span class="header_template_name" ><?=ucfirst($header)?></span>
					<span class="header_template_fullname hidden"><?=ucfirst($header)?></span>
				</label>
			</div>
		<?php endforeach ?>
	<?php endif; ?>
</div>
</form>

<div class="well text-center">
    <button type="button" id="btn-preview" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('Preview')?></button>
    <button type="button" id="btn-reset" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('Reset')?></button>
    <button type="submit" id="btn-save" form="form-theme" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><?=lang('Save')?></button>
    <?php if ($this->permissions->checkPermissions('generate_to_site_header')) :?>
        <button type="button" id="btn-gen-ftp" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-default'?>"><?=lang('Generate to Site')?></button>
    <?php endif; ?>
</div>
</div>

<?php if ($this->permissions->checkPermissions('add_header_template')) :?>
	<div class="modal fade in" id="mdl-addTheme" tabindex="-1" role="dialog" aria-labelledby="label_player_notes">
	  <div class="modal-dialog" role="document">
	      <div class="modal-content">
	          <div class="modal-header">
	              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                  <span aria-hidden="true">&times;</span>
	              </button>
	              <h4 class="modal-title"><?= lang('Upload New Theme') ?></h4>
	          </div>
	          <form id="form_upload_themes" action="<?php echo site_url('/theme_management/upload_new_header/');?>" method="post" enctype="multipart/form-data">
		          <div class="modal-body">
		          	<div class="row">
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('Header Name') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="text" id="txtName" name="txtName">
				    		</div>
					  	</div>
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('Header File') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_php_header" name="file_php_header[]" hidden>
				    		</div>
					  	</div>
					  	<!--<div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('cms.headerlogo') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_header_logo" name="file_header_logo[]" hidden>
				    		</div>
					  	</div>-->
			            <!--<div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('Image') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_image" name="file_image[]" hidden>
				    		</div>
					  	</div>-->
		            </div>
		          </div>
		          <div class="modal-footer">
		          	<div class="well text-center">
						<button type="button" id="btn-reset" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal"><?=lang('Cancel')?></button>
						<button type="submit" id="btn-save-new-theme" form="form_upload_themes" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><?=lang('Save')?></button>
					</div>
		          </div>
	          </form>
	      </div>
	  </div>
	</div>
<?php endif;?>
<script type="text/javascript">
	$('.header_template_name').each(function(i) {
		var header_template_text = ($(this).text());
		if( header_template_text.length > 12){
			var header_template_str = (header_template_text.substr(0,12)) + '...';
			$(this).text(header_template_str);
		}
	});

	$('label').each(function(i) {
		$(this).attr('title',$(this).find('.header_template_fullname').text());
		$(this).attr('data-placement','bottom');
	});


	var preview_expiry;

	if ( self !== top ) {
		$('nav').remove();
		$('#sidebar-wrapper').remove();
		$('#lhc_status_container').hide();
		$('#theme_main_content').addClass('style-in-iframe');
        $('#main_content').css({ height: "1000px"});
	}

	$('input[name="header_template"]').change(function() {
		$('.row label').removeClass('active');
		$('input[name="header_template"]:checked').parent('label').addClass('active');
	});

	$('#btn-preview').click(function() {
		var header_template = $('input[name="header_template"]:checked').val();
		window.open('/theme_management/previewHeader/' + header_template, 'preview');

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
		$.get('/theme_management/resetHeader', function() {
			$('input[name="header_template"]').trigger('change');
		});
	});

	$('#btn-addTheme').click(function() {
		$('input').val('');
		/*$('#file_image').val('');
		$('#txtName').val('');*/
		$('#mdl-addTheme').modal('show');
	});

	$('#btn-setDefault').click(function() {
		if (confirm("<?=lang('template.msg1')?>")) {
			$.get('/theme_management/setToDefault/', function() {
				location.reload();
			});
		}
	});

	function deleteHeader(header) {
		if (confirm("<?=lang('header.template.msg4')?>")) {
			$.get('/theme_management/deleteHeader/'+header, function() {
				location.reload();
			});
		}
	}

	$('#btn-gen-ftp').click(function() {
		var header_template = $('input[name="header_template"]:checked').val();
		if (confirm("<?=lang('header.template.msg8')?>")) {
			$.get('/theme_management/transferFileToSite/'+header_template, function() {
				location.reload();
			});
		}
	});

</script>