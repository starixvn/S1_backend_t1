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
	}
	.style-in-iframe {
		position: absolute;
		top: -30px;
		left: 20px;
		width: 99%;
	}
</style>

<div id="theme_main_content">
<h1 class="page-header"><span id="spnpPageTitle"><?=lang('Javascript Template')?></span>
	<?php if ($this->permissions->checkPermissions('add_javascript_template')) :?>
		<a type="button" id="btn-addJsTheme" name="btn-addJsTheme" class="btn btn-primary btn-sm pull-right" data-original-title="" title=""><i class="glyphicon glyphicon-plus" style="color:white;" data-placement="bottom"></i><?= lang('Add Javascript') ?></a>
	<?php endif; ?>
</h1>
<form id="form-theme" action="/theme_management/saveFooter" method="POST">
<div class="row">
	<?php if (!empty($files)) :?>
		<?php foreach ($files as $file): ?>
			<div class="col-md-5ths">
				<label class="btn btn-block btn-default  <?=$selected_footer == $file ? 'active current-header' : ''?>" style="height: 50px; margin-bottom: 30px;">
					<?php if($selected_footer != $file && $this->permissions->checkPermissions('delete_javascript_template')) :?>
						<a href="javascript:void(0);" onclick="deleteJs('<?= $file ?>');" class="pull-right" style="position: relative; font-size: 18px;">
							<i class="fa fa-trash" id="icon"></i>
						</a>
					<?php endif; ?>
					<a href="/theme_management/downloadJS/<?= $file ?>" class="pull-right" style="position: relative; font-size: 18px;">
						<i class="fa fa-download" id="icon"></i>
					</a>
					<input type="radio" name="js_template" value="<?=$file?>" autocomplete="off" <?=$selected_footer == $file ? 'checked' : ''?> style="display: none;">		
					<?=ucfirst($file)?>
				</label>
			</div>
		<?php endforeach ?>
	<?php endif; ?>
</div>
</form>

<?php if (empty($files)) :?>
	<div class="well text-center">
		<label><?= lang('js.template.msg7') ?></label>	
	</div>
<?php endif; ?>

</div>
<?php if ($this->permissions->checkPermissions('add_javascript_template')) :?>
	<div class="modal fade in" id="mdl-addJsTheme" tabindex="-1" role="dialog" aria-labelledby="label_player_notes">
	  <div class="modal-dialog" role="document">
	      <div class="modal-content">
	          <div class="modal-header">
	              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                  <span aria-hidden="true">&times;</span>
	              </button>
	              <h4 class="modal-title"><?= lang('Upload New Javascript') ?></h4>
	          </div>
	          <form id="form_upload_js" action="<?php echo site_url('/theme_management/upload_new_js/');?>" method="post" enctype="multipart/form-data">
		          <div class="modal-body">
		          	<div class="row">
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('Javascript Name') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="text" id="txtName" name="txtName">
				    		</div>
					  	</div>
			            <div class="form-group row">
					    	<label class="col-sm-3 control-label text-right">
			                	<?= lang('Javascript File') ?>
			                </label>
			                <div class="col-sm-9">
					    		<input type="file" id="file_js" name="file_js[]" hidden>
				    		</div>
					  	</div>
		            </div>
		          </div>
		          <div class="modal-footer">
		          	<div class="well text-center">
						<button type="submit" id="btn-save-new-js" form="form_upload_js" class="btn btn-default"><?=lang('Save')?></button>
						<button type="button" id="btn-reset" class="btn btn-default" data-dismiss="modal"><?=lang('Cancel')?></button>
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
	}

	$('input[name="js_template"]').change(function() {
		$('.row label').removeClass('active');
		$('input[name="js_template"]:checked').parent('label').addClass('active');
	});

	
	$('#btn-addJsTheme').click(function() {
		$('input').val('');
		$('#mdl-addJsTheme').modal('show');
	});

	function deleteJs(file) {
		if (confirm("<?=lang('js.template.msg4')?>")) {
			$.get('/theme_management/deleteJs/'+file, function() {
				location.reload();
			});
		}
	}

	$('#btn-setDefault').click(function() {
		if (confirm("<?=lang('template.msg1')?>")) {
			$.get('/theme_management/setToDefaultFooter/', function() {
				location.reload();
			});
		}
	});

</script>