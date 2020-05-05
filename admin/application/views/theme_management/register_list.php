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
		width: 98%;
	}
</style>
<div id="theme_main_content">
<h1 class="page-header"><?=lang('Registration Template')?></h1>
<form id="form-theme" action="/theme_management/saveRegistration" method="POST">
<div class="row">
	<?php if (!empty($registers)) :?>
		<?php foreach ($registers as $register): ?>
			<div class="col-md-5ths">
				<label class="btn btn-block btn-default  <?=$selected_registers == $register ? 'active current-header' : ''?>" style="height: 50px; margin-bottom: 30px;">
					<input type="radio" name="registration_template" value="<?=$register?>" autocomplete="off" <?=$selected_registers == $register ? 'checked' : ''?> style="display: none;">
					<?=ucfirst($register)?>
				</label>
			</div>
		<?php endforeach ?>
	<?php endif; ?>
</div>
</form>

<div class="well text-center">
	<?php if (!empty($registers)) :?>
		<button type="button" id="btn-preview" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('Preview')?></button>
		<button type="button" id="btn-reset" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('Reset')?></button>
		<button type="submit" id="btn-save" form="form-theme" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><?=lang('Save')?></button>
	<?php else: ?>
		<label><?= lang('registration.template.msg7') ?></label>
	<?php endif; ?>
</div>
</div>

<script type="text/javascript">
	var preview_expiry;

	if ( self !== top ) {
		$('nav').remove();
		$('#sidebar-wrapper').remove();
		$('#theme_main_content').addClass('style-in-iframe');
        $('#main_content').css({ height: "1000px"});
	}

	$('input[name="registration_template"]').change(function() {
		$('.row label').removeClass('active');
		$('input[name="registration_template"]:checked').parent('label').addClass('active');
	});

	$('#btn-preview').click(function() {
		var registration_template = $('input[name="registration_template"]:checked').val();
		window.open('/theme_management/previewRegistration/' + registration_template, 'preview');

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
		$.get('/theme_management/resetRegistration', function() {
			$('input[name="registration_template"]').trigger('change');
		});
	});

</script>