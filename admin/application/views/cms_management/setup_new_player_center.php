<style type="text/css">
	img {
		width: auto !important;
	}
</style>

<?php
	$initial_features = $this->utils->getConfig('initial_player_center_features');
	$system_features = [];// $this->utils->getConfig('initial_system_features');
	$player_center_logo = $this->utils->getSystemUrl("player").'/'. $this->utils->getPlayerCenterTemplate() . '/img/logo.png';

	$setupSteps = array(
		array(
			'step_no' => 1,
			'title' => 'Themes',
			'url' => base_url() . 'theme_management/index/true',
			'iframeHeight' => 1000,
		),
		array(
			'step_no' => 2,
			'title' => 'Header',
			'url' => base_url() . 'theme_management/HeaderIndex/true',
			'iframeHeight' => 1000,
		),
		array(
			'step_no' => 3,
			'title' => 'Footer',
			'url' => base_url() . 'theme_management/footerIndex/true',
			'iframeHeight' => 1000,
		),
		array(
			'step_no' => 4,
			'title' => 'Custom Registration',
			'url' => base_url() . 'theme_management/registerIndex/true',
			'iframeHeight' => 1000,
		),
		array(
			'step_no' => 5,
			'title' => 'Registration Settings',
			'url' => base_url() . 'marketing_management/viewRegistrationSettings/1/true',
			'iframeHeight' => 1600,
		),
	);

?>
<style type="text/css">
	.header-text-size {
		font-size: 14px;
	}

	.breadcrumb {
	    padding: 0;
	    background: transparent;
	    list-style: none;
	    overflow: hidden;
	    margin-top: 20px;
	    margin-bottom: 20px;
	    border-radius: 4px;
        border: none;
	}

	.breadcrumb>li {
	    display: table-cell;
	    vertical-align: top;
	    width: 1%;
	    font-size: 14px;
	    font-weight: bolder;
	}

	.breadcrumb>li+li:before {
	    padding: 0;
	}

	.breadcrumb li a {
	    color: white;
	    text-decoration: none;
	    padding: 10px 0 10px 45px;
	    position: relative;
	    display: inline-block;
	    width: calc( 100% - 10px );
	    background-color: hsla(0, 0%, 83%, 1);
	    text-align: center;
	    text-transform: capitalize;
	}

	.breadcrumb li.completed a {
	    background: brown;
	    background: hsla(153, 57%, 51%, 1);
	}

	.breadcrumb li.completed a:after {
	    border-left: 30px solid hsla(153, 57%, 51%, 1);
	}

	.breadcrumb li.active a {
	    background: #ffc107;
	}

	.breadcrumb li.active a:after {
	    border-left: 30px solid #ffc107;
	}

	.breadcrumb li:first-child a {
	    padding-left: 15px;
	}

	.breadcrumb li:last-of-type a {
	    width: calc( 100% - 38px );
	}

	.breadcrumb li a:before {
	    content: " ";
	    display: block;
	    width: 0;
	    height: 0;
	    border-top: 50px solid transparent;
	    border-bottom: 50px solid transparent;
	    border-left: 30px solid white;
	    position: absolute;
	    top: 50%;
	    margin-top: -50px;
	    margin-left: 1px;
	    left: 100%;
	    z-index: 1;
	}

	.breadcrumb li a:after {
	    content: " ";
	    display: block;
	    width: 0;
	    height: 0;
	    border-top: 50px solid transparent;
	    border-bottom: 50px solid transparent;
	    border-left: 30px solid hsla(0, 0%, 83%, 1);
	    position: absolute;
	    top: 50%;
	    margin-top: -50px;
	    left: 100%;
	    z-index: 2;
	}

</style>

<div class="panel panel-primary panel_main">
	<div class="panel-heading">
		<div class="row">
			<div class="col-md-6">
				<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?= lang('Setup New Player Center'); ?> (<?=ucfirst($setupType)?>)
				<!-- <a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a> -->
				</h4>
			</div>

			<div class="col-md-6">
				<span class="pull-right">
					<button type="button" id="btnPrevious" class="btn btn-default">Previous</button>
					<button type="button" id="btnNext" class="btn btn-default">Next</button>
					<button type="button" id="btnFinish" class="btn btn-success hidden">Finish</button>
				</span>
			</div>
		</div>

	</div>

	<?php if ($setupType === "auto") : ?>
		<div id="main_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="progress">
				  <div id="setupProgressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
				    <span class="sr-only">0% Complete</span>
				  </div>
				</div>

				<button type="button" id="btnRunSetup" class="btn btn-success">Run Recommended Setup</button>

			</div>
		</div>

		<input type="radio" name="rdbLanguage" class="player-center-language hidden" value="2" checked="checked">

		<table class="table list_features hidden">
			<?php
				foreach ($system_feature as $idx => $feature) {
					$enabled_feature = "";

					if (in_array($feature['name'], $initial_features)) {

						$enabled_feature = 'checked="checked"';
					}

					// if (in_array($feature['name'], $system_features)) {
					// 	$enabled_feature = 'checked="checked"';
					// }
			?>
				<tr>
					<td>
					<?=$feature['name']?>
					</td>
					<td>
						<div class="form-group">
							<input type="checkbox" id="item_<?=$feature['id']?>" name="enabled[]" value="<?=$feature['id']?>" <?=$enabled_feature?>>
						</div>
					</td>
				</tr>
			<?php }?>
		</table>

	<?php endif; ?>

	<?php if ($setupType === "manual") : ?>
		<div id="main_panel" class="panel-collapse collapse in ">
			<div class="panel-body">

			<div class="container">
				<div class="row">
					<ul class="breadcrumb">
						<?php foreach ($setupSteps as $key => $value) : ?>
							<li id="liStep<?=$value['step_no']?>" <?=$value['step_no'] == 1 ? 'class="active"' : '' ?>><a href="javascript:void(0);">Step <?=$value['step_no']?></a></li>
						<?php endforeach; ?>
						<li id="liStep6" ><a href="javascript:void(0);">Step 6</a></li>
					</ul>
				</div>
			</div>
				<?php foreach ($setupSteps as $key => $value) : ?>
					<div id="tblStep<?=$value['step_no']?>" class="setup-step">
						<iframe id="iframeSetup<?=$value['step_no']?>" src="<?=$value['url']?>" class="frame_setup_player" width="100%" height="<?=$value['iframeHeight']?>" scrolling="no" style="border:0;"></iframe>
					</div>
				<?php endforeach; ?>

				<div id="tblStep<?=count($setupSteps)+1?>" class="setup-step">
					<!-- Set player center default language -->
					<table class="table table-hover table-striped table-bordered">
						<tr><th><?= lang('player.center.lang'); ?></th></tr>
						<tr>
							<td>
								<label class="col-md-2" for="chkLanguageDefault">
									<input type="radio" name="rdbLanguage" id="chkLanguageDefault" class="player-center-language" value="0" <?= $player_current_language == "0" ? "checked" : "" ?>><?=lang('use.default.lang')?>
								</label>

								<label class="col-md-1" for="chkLanguageEN">
									<input type="radio" name="rdbLanguage" id="chkLanguageEN" class="player-center-language" value="1" <?= $player_current_language == "1" ? "checked" : "" ?>>
									<img src="<?= $this->utils->imageUrl('en-icon.png'); ?>"  alt="<?=lang('English')?>"> <?=lang('English')?>
								</label>

								<label class="col-md-1" for="chkLanguageCN">
									<input type="radio" name="rdbLanguage" id="chkLanguageCN" class="player-center-language" value="2" <?= $player_current_language == "2" ? "checked" : "" ?>>
									<img src="<?= $this->utils->imageUrl('cn-icon.png'); ?>" alt="<?=lang('Chinese')?>"> <?=lang('Chinese')?>
								</label>

								<label class="col-md-1" for="chkLanguageID">
									<input type="radio" name="rdbLanguage" id="chkLanguageID" class="player-center-language" value="3" <?= $player_current_language == "3" ? "checked" : "" ?>>
									<img src="<?= $this->utils->imageUrl('id-icon.jpg'); ?>" alt="<?=lang('Indonesian')?>"> <?=lang('Indonesian')?>
								</label>

								<label class="col-md-1" for="chkLanguageVN">
									<input type="radio" name="rdbLanguage" id="chkLanguageVN" class="player-center-language" value="4" <?= $player_current_language == "4" ? "checked" : "" ?>>
									<img src="<?= $this->utils->imageUrl('vn-icon.jpg'); ?>" alt="<?=lang('Vietnamese')?>"> <?=lang('Vietnamese')?>
								</label>

								<label class="col-md-1" for="chkLanguageKR">
									<input type="radio" name="rdbLanguage" id="chkLanguageKR" class="player-center-language" value="5" <?= $player_current_language == "5" ? "checked" : "" ?>>
									<img src="<?= $this->utils->imageUrl('kr-icon.jpg'); ?>" alt="<?=lang('Korean')?>"> <?=lang('Korean')?>
								</label>
							</td>
						</tr>
					</table>

					<form id="frmUploadPlayerLogo" action="<?php echo site_url('cms_management/upload_player_logo'); ?>" method="POST" enctype="multipart/form-data">
						<table class="table table-hover table-striped table-bordered">
							<tr><th colspan="2"><?= lang('Player Logo'); ?></th></tr>
							<tr>
								<td class="col-md-4">
									<input type="checkbox" name="setDefaultPlayerLogo" id="setDefaultPlayerLogo" checked="checked" >
									<label for="setDefaultPlayerLogo" class="control-label"> <?= lang('Use default player logo.'); ?></label>
								</td>
								<td class="col-md-8" rowspan="2"><?=lang('Current system logo')?> : <br/><br/>
									<img src="<?= $player_center_logo ?>" alt="">
			                    </td>
							</tr>
							<tr>
								<td class="col-md-4">
									<?=lang('Upload File')?> :
									<input type="file" name="fileToUpload[]" id="fileToUpload" accept="image/*" class="form-control input-sm">
									<button type="submit" id="btnSavePlayerCenterLogo" class="btn btn-primary hidden"><?= lang('Save'); ?></button>
								</td>
							</tr>
						</table>
					</form>

					<table class="table table-hover table-striped table-bordered list_features">
						<tr><th colspan="2"><?= lang('Features'); ?></th></tr>
						<tr>
							<th class="col-md-4"><?php echo lang('aff.al36'); ?></th>
							<th class="col-md-8"><?php echo lang('Value'); ?></th>
						</tr>
						<?php
							foreach ($system_feature as $idx => $feature) {
								$hide_feature = 'class="hidden"';
								$enabled_feature = "";

								if (in_array($feature['name'], $initial_features)) {
									$hide_feature = '';
									$enabled_feature = 'checked="checked"';
								}

								if (in_array($feature['name'], $system_features)) {
									$enabled_feature = 'checked="checked"';
								}
						?>
							<tr <?=$hide_feature?>>
								<td>
								<?=$feature['name']?>
								</td>
								<td>
									<div class="form-group">
										<input type="checkbox" id="item_<?=$feature['id']?>" name="enabled[]" value="<?=$feature['id']?>" <?=$enabled_feature?>>
									</div>
								</td>
							</tr>
						<?php }?>
					</table>
				</div>
				<!--  End of Step 5 -->

			</div>
		</div>
	<?php endif; ?>

</div>

<script type="text/javascript">
	var current_step = 1;
	var min_step = 1;
	var max_step = $(".setup-step").length;
	var conf_message = '<?=lang("sys.sure")?>';

	$(document).ready(function(){
		$('#view_system_settings').addClass('active');
		$('.fa-download').hide();

		initializePage();

		$("#btnPrevious").click(function(){
			if (current_step > min_step) {
				$(".setup-step").hide();
				$('#btnFinish').addClass('hidden');
				$('#btnNext').removeClass('hidden');
				current_step--;
			}
			var prevStep = current_step + 1;

			$("#tblStep"+current_step).show();
			$("#liStep"+current_step).addClass('active');

			$("#liStep"+prevStep).removeClass('active');
			//$("#liStep"+prevStep).removeClass('completed');
		});


		$("#btnNext").click(function(){
			if (current_step < max_step) {
				$(".setup-step").hide();
				$('#btnFinish').addClass('hidden');
				current_step++;
			}

			if (current_step == max_step) {
				$('#btnNext').addClass('hidden');
				$('#btnFinish').removeClass('hidden');
			}
			$("#tblStep"+current_step).show();
			$("#liStep"+current_step).addClass('active');

			var prevStep = current_step - 1;
			$("#liStep"+prevStep).removeClass('active');
			$("#liStep"+prevStep).addClass('completed');
		});
	});

	$(function(){
		var  base_url = "<?=base_url()?>";

		/** save system initial setup */
		$('#btnFinish').on('click', function(e){
			e.preventDefault();
			var conf = confirm(conf_message);

			if (conf == true) {
				saveDefaultFeaturesAndLanguage(1);
			}
		});
		/** End of code */

		$('#btnRunSetup').click(function(){
			var conf = confirm(conf_message);

			if (conf == true) {
				runAutomaticSetup();
			}
		});

	});

	function initializePage() {
		$(".setup-step").hide();
		$("#tblStep1").show();
	}

	function updateProgress(percentage) {
	    $('.progress-bar').animate({ width: percentage + "%" },50);
	}

	function runAutomaticSetup() {
		saveDefaultFeaturesAndLanguage(2);
	}

	// setup_type
	// 		1 = automatic
	// 		2 = manual
	function saveDefaultFeaturesAndLanguage(setup_type) {
		var item = [];
		var selectedLanguage = $("input[name='rdbLanguage']:checked").val();

		$('.list_features').find('input[type="checkbox"]').each(function(){
			var enabled = 0;
			if( $(this).is(':checked') ) enabled = 1;

			var json = {
				'id' : $(this).val(),
				'enabled' : enabled
			}
			item.push(json);
		});

		/** Setup collection account */
		$.ajax({
			url: base_url + 'cms_management/autoSetupCollectionAccount',
			type: 'GET',
			async:false,
			success: function(data){
				if (setup_type == 2) {
					updateProgress(25);
				}
			}
		});

		$.ajax({
			url: base_url + 'cms_management/setNewPlayeCenterLanguage',
			type: 'POST',
			async:false,
			data: {
				language: selectedLanguage
			},

			success: function(data){
				if (setup_type == 2) {
					updateProgress(75);
				}
			}
		});

		/** Update player center features */
		$.ajax({
			url: base_url + 'cms_management/saveSystemFeatures',
			type: 'POST',
			async:false,
			data: {
				enabled: item
			},
			success: function(data){
				if (setup_type == 2) {
					updateProgress(100);
					window.location = base_url + 'cms_management/player_center_settings';
				} else {
					$("#btnSavePlayerCenterLogo").click();
				}
			}
		});
		/** End of  */
	}


</script>