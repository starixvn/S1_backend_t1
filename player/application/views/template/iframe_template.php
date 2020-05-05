<?php

$standard_js=[
    $this->utils->thirdpartyUrl('bootstrap/3.3.7/bootstrap.min.js'),
    $this->utils->jsUrl('jquery.slimscroll.min.js'),
    $this->utils->jsUrl('snackbar.min.js'),
    $this->utils->jsUrl('nod.js'),
    $this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js'),
    $this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js'),

    $this->utils->thirdpartyUrl('bootstrap-notify/bootstrap-notify.min.js'),
    $this->utils->thirdpartyUrl('bootstrap-dialog/js/bootstrap-dialog.min.js'),
    $this->utils->thirdpartyUrl('numeral/numeral.min.js'),
    $this->utils->thirdpartyUrl('webshim/1.15.8/polyfiller.min.js'),
    $this->utils->playerResUrl('player_center_utils.js'),
    //for template public
    site_url('/pub/variables?v='.PRODUCTION_VERSION),
    $this->utils->playerResUrl('template_header.js'),

];

$site=$this->utils->getSystemUrl('www');

$standard_css=[
	$this->utils->cssUrl($this->config->item('default_player_bootstrap_css')),
	$this->utils->cssUrl('template/customized-css.css'),
	$this->utils->cssUrl('snackbar.min.css'),
	$this->utils->cssUrl('daterangepicker.css'),
];

$lang = $this->config->item('default_player_language');
$lang_code = $this->language_function->langStrToInt($lang);
$this->language_function->setCurrentLanguage($lang_code);

$loggedPlayerUsername=$this->authentication->getUsername();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- add lang data table translation-->
    <script type="text/javascript">
        var DATATABLES_COLUMNVISIBILITY = "<?php echo lang('Column visibility'); ?>";
        var DATATABLES_RESTOREVISIBILITY = "<?php echo lang('Restore Visibility'); ?>";
    </script>
    <!-- end of data table translation-->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="renderer" content="webkit" />

	<title><?=$title?></title>
	<link rel="shortcut icon" href="<?php echo $this->config->item('fav_icon')==""?"/favicon.ico":$this->config->item('fav_icon');?>" type="image/x-icon" />

	<meta name="description"    content="<?=$description?>" />
	<meta name="keywords"       content="<?=$keywords?>" />
	<meta name="viewport"       content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />

	<!-- ###################################################################################### STYLES ###################################################################################### -->

<?php
        foreach ($standard_css as $css_url) {
            echo '<link href="'.$css_url.'" rel="stylesheet"/>';
        }
?>

	<?=$_styles?>

	<style type="text/css">
		.custom-container .panel{
			margin-bottom: 0px;
		}

		.input-group-addon{
			padding: 2px 12px;
		}
	</style>

	<!-- ###################################################################################### SCRIPTS ###################################################################################### -->

	<?php if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {?>
		<script type="text/javascript">
			<?=$this->utils->getFileFromCache(APPPATH . '/../public/resources/js/jquery-1.11.1.min.js')?>
		</script>
	<?php } else {?>
		<script type="text/javascript">
			<?=$this->utils->getFileFromCache(APPPATH . '/../public/resources/js/jquery-2.1.4.min.js')?>
		</script>
	<?php
		}

        foreach ($standard_js as $js_url) {
            echo '<script type="text/javascript" src="'.$js_url.'"></script>';
        }
    ?>

	<?php echo $_scripts; ?>

<script>
    <?php echo $this->utils->generateStatCode($loggedPlayerUsername);?>
</script>

</head>
<body>

<?php if ($this->session->userdata('result')) {?>
	<div class="alert alert-<?=$this->session->userdata('result')?> alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<span id="message"><?=$this->session->userdata('message')?></span>
	</div>
<?php }?>

<?php
$this->session->unset_userdata('result');
$this->session->unset_userdata('promoMessage');
$nopanel = isset($nopanel) && $nopanel;
?>

	<!-- Main content -->
	<?php if ($nopanel) { ?>
        <div class="main_content"><?=$main_content?></div>
	<?php } else { ?>
	<style>
		.navbar-brand {
		    height: 0;
		}
	</style>
	<div class="panel panel-primary" style="margin-bottom: 0;">
	      <?php if($isLogged): ?>
	            <nav class="navbar navbar-default" >
					  <div class="container-fluid">
					    <!-- Brand and toggle get grouped for better mobile display -->
					    <div class="navbar-header">
					      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					        <span class="sr-only"><?php echo lang('Toggle navigation'); ?></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					      </button>
					      <a class="navbar-brand" href="#"><?=$title?></a>
					    </div>

					    <!-- Collect the nav links, forms, and other content for toggling -->
					    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					      <ul class="nav navbar-nav">
					         <!-- login -->
					            <?php if ($_SERVER['REQUEST_URI'] != '/iframe/auth/login' || $_SERVER['REQUEST_URI'] != '/iframe_module/iframe_register/'){?>
						            <li><a style="color:white;" href="/iframe_module/iframe_playerSettings" class="_player_information"><?=lang('Profile')?></a>  </li>
						            <li><a style="color:white;" href="/iframe_module/iframe_makeDeposit" class="_player_information"><?=lang('header.deposit')?></a> </li>
						            <li><a style="color:white;" href="/iframe_module/iframe_viewWithdraw" class="_player_information"><?=lang('header.withdrawal')?></a> </li>
						            <li><a style="color:white;" href="/iframe_module/iframe_viewCashier" class="_player_information"><?=lang('Cashier')?></a> </li>
						            <li><a style="color:white;" href="/player_center2/report" class="_player_information"><?=lang('lang.search')?></a> </li>
						            <li><a style="color:white;" href="/player_center2/messages" class="_player_information"><?=lang('cs.messages')?></a></li>
					            <?php }?>
					        <!-- login -->
					      </ul>
					      <ul class="nav navbar-nav navbar-right">
					        <li>
					        		<?php if ($timeReminders) {?>
										<a href="javascript:void(0);" onClick=window.open("timeRemindersWindow?timeReminder=<?php echo $timeReminders ?>","Ratting","width=550,height=190,left=150,top=200,toolbar=0,status=0") class="btn btn-success btn-sm" ><?php echo lang("Start Time Reminder") ?></a>
									<?php }?>
					        </li>
					        <li style="padding-top:10px;">

<?php
	$currentLang = isset($currentLang) ? $currentLang : 2;
	$data = array('currentLang' => $currentLang);
	$this->load->view('iframe/partial/lang_select', $data);
?>
						</li>
						<li>
									<?php if ($_SERVER['REQUEST_URI'] != '/iframe_module/iframe_register/') {?>
										<a href="<?php echo $this->utils->playerLiveChatUrl(); ?>" target="_blank">
											<span class="glyphicon glyphicon-comment"></span> <?=lang('cashier.40')?>
										</a>
									<?php }?>
					        </li>

					      </ul>
					    </div><!-- /.navbar-collapse -->
					  </div><!-- /.container-fluid -->
					</nav>
			<?php endif; ?>

        <div class="panel-body"><?=$main_content?></div>
        <div class="panel-footer"><label class="pull-right small" style="margin-top: -6px;"><?php echo PRODUCTION_VERSION . '-' . $this->utils->getRuntimeEnv(); ?></label></div>
    </div>
	<?php }
?>

<?php if ($isLogged && !(isset($ignoreWebpush) && $ignoreWebpush)) {?>
	<script data-main="<?=site_url('/iframe_module/player_iframe');?>" src="<?=site_url('/resources/js/require.js');?>" async="true"></script>
<?php }
?>

<?php echo $this->utils->getAnalyticCode('player'); ?>
<?php echo '<!-- ' . PRODUCTION_VERSION . '-' . $this->utils->getRuntimeEnv() . ' -->'; ?>

<!-- customize player center css -->
<style type="text/css">
<?php echo isset($player_center_css) ? $player_center_css : '';?>
</style>

</body>
</html>
