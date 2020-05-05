<?php
$promotion_rules=$this->utils->getConfig('promotion_rules');
$enabled_request_without_check=$promotion_rules['enabled_request_without_check'];
?>
<div class="col-md-12">
	<h4><?php echo lang('Promotion'); ?>: <?php echo @$promorulesCmsDetails[0]['promoName'] ?></h4>
	<h5><?php echo lang('Promotion Details')?>:</h5>
	<div class="col-md-12">
		<?php echo @$promorulesCmsDetails[0]['promoName'] ?>
	</div>
	<hr/><br/>
	<a href="<?=site_url("iframe_module/request_promo/" . @$promorulesCmsDetails[0]['promoCmsSettingId']);?>" class="btn btn-sm btn-info"><?=lang('promo.applyNow');?></a>
</div>