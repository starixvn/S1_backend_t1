
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

<?php if(!empty($backoffice_info)){ ?>
	<?=lang('Loading')?>...
	<form action="<?=$backoffice_info['backoffice_url']?>" id="login_form" method='POST'>
		<input type="hidden" name="username" value="<?=$backoffice_info['backoffice_username']?>">
		<input type="hidden" name="password" value="<?=$backoffice_info['backoffice_password']?>">
	</form>
	<script type="text/javascript">
		// var backoffice_url="<?=$backoffice_info['backoffice_url']?>";
		//js submit
		//{username: "sdfds", password: "sdf"}
		$("#login_form").submit();
	</script>
<?php }else{ ?>

	<?=lang('Sorry, lottery backoffice is not available')?>

<?php }?>

	</div>

</div>
