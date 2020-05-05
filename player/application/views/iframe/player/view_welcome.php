<div>
	<div style="float:left;">
		<?=$username;?>
		<button onclick="location.href='<?=site_url('iframe_module/iframe_logout');?>';"><?=lang('header.logout');?></button>
	</div>

	<div style="float: right">
		<button onclick="location.href='<?=site_url('iframe_module/iframe_viewCashier');?>';"><?=lang('header.memcenter');?></button>
		<button onclick="location.href='<?=site_url('iframe_module/iframe_makeDeposit')?>';"><?=lang('header.deposit');?></button>
		<button onclick="location.href='<?=site_url('iframe_module/iframe_viewWithdraw')?>';"><?=lang('header.withdrawal');?></button>
		<button onclick="location.href='<?=site_url('iframe_module/iframe_playerSettings')?>';"><?=lang('header.information');?></button>
	</div>
</div>
