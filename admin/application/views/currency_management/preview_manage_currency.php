<style type="text/css">
.panel-body .row{
	margin: 4px;
}
</style>

<form action="<?php echo site_url('system_management/post_manage_currency'); ?>" method="POST">
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('Manage Currency')?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">
		<?php if(!empty($error_message)){?>
		<h3 class="text-danger">
			<?php foreach($error_message as $err){ ?>
				<div><?=$err?></div>
			<?php }?>
		</h3>
		<?php }?>

		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Player Username');?>
			</div>
			<div class="col-md-4">
			<?=$conditions['player_username']?>
			<?php if(!empty($conditions['player_username']) && empty($player_id)){?>
				<span class="text-danger"><?=lang('Not found').' '.$conditions['player_username']?></span>
			<?php }?>
			<input type="hidden" name="player_id" value="<?=$player_id?>">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Admin Username');?>
			</div>
			<div class="col-md-4">
			<?=$conditions['admin_username']?>
			<?php if(!empty($conditions['admin_username']) && empty($admin_id)){?>
				<span class="text-danger"><?=lang('Not found').' '.$conditions['admin_username']?></span>
			<?php }?>
			<input type="hidden" name="admin_id" value="<?=$admin_id?>">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Affiliate Username');?>
			</div>
			<div class="col-md-4">
			<?=$conditions['affiliate_username']?>
			<?php if(!empty($conditions['affiliate_username']) && empty($affiliate_id)){?>
				<span class="text-danger"><?=lang('Not found').' '.$conditions['affiliate_username']?></span>
			<?php }?>
			<input type="hidden" name="affiliate_id" value="<?=$affiliate_id?>">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Agency Username');?>
			</div>
			<div class="col-md-4">
			<?=$conditions['agency_username']?>
			<?php if(!empty($conditions['agency_username']) && empty($agency_id)){?>
				<span class="text-danger"><?=lang('Not found').' '.$conditions['agency_username']?></span>
			<?php }?>
			<input type="hidden" name="agency_id" value="<?=$agency_id?>">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
			<?php echo lang('Enable Currency');?>
			</div>
			<div class="col-md-10">
			<?php
				if(!empty($available_currency_list)){
					foreach ($available_currency_list as $currencyKey => $currencyInfo) {
						if(in_array($currencyKey, $enable_currency_arr)){
			?>

					<input type="hidden" name="enable_currency[]" value="<?=$currencyKey?>">
					<?=$currencyInfo['code']?> <?=lang($currencyInfo['name'])?> |
			<?php
						}
			 		}
				}
			?>
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<input type="button" class="btn btn-danger" value="<?php echo lang('Back'); ?>" onclick="window.history.back()">
		<input type="submit" class="btn btn-primary" value="<?php echo lang('Save'); ?>">
	</div>

	</div>

</div>
</form>

<script type="text/javascript">

	// resizeSidebar();
	$( document ).ready(function() {
	    $("#view_manage_currency").addClass('active');

	    // hljs.initHighlightingOnLoad();
	   //  $('pre code').each(function(i, block) {
    // 		hljs.highlightBlock(block);
  		// });
	});

</script>
