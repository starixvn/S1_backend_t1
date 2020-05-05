<style type="text/css">
.panel-body .row{
	margin: 4px;
}
</style>

<form action="<?php echo site_url('system_management/preview_manage_currency'); ?>" method="POST">
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('Manage Currency')?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Player Username');?>
			</div>
			<div class="col-md-4">
			<input type="text" class="form-control input-sm" name="player_username" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Admin Username');?>
			</div>
			<div class="col-md-4">
			<input type="text" class="form-control input-sm" name="admin_username" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Affiliate Username');?>
			</div>
			<div class="col-md-4">
			<input type="text" class="form-control input-sm" name="affiliate_username" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Agency Username');?>
			</div>
			<div class="col-md-4">
			<input type="text" class="form-control input-sm" name="agency_username" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Enable/Disable Currency');?>
			</div>
			<div class="col-md-10">
			<?php if(!empty($available_currency_list)){?>
				<?php foreach ($available_currency_list as $currencyKey => $currencyInfo) { ?>
					<input type="checkbox" name="enable_currency[]" value="<?=$currencyKey?>" checked>
					<?=$currencyInfo['code']?> <?=lang($currencyInfo['name'])?> |
				<?php } ?>
			<?php }?>
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<input type="submit" class="btn btn-primary" value="<?php echo lang('Next'); ?>">
	</div>

	</div>

</div>
</form>

<script type="text/javascript">

	// resizeSidebar();
	$( document ).ready(function() {
	    $("#view_manage_currency").addClass('active');
	});

</script>
