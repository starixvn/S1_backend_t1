<style type="text/css">
	body {
		background-color:#FF0000;
	}
	.bonus{
		background-image:url('<?php echo $this->utils->imageUrl($this->utils->getConfig('random_bonus_result_img_url')); ?>');
	    background-repeat: no-repeat;
	    /*background-attachment: fixed;*/
	    background-position: center;
	    text-align: center;
	}
</style>
<div class="row bonus">
	<div class="col-md-12">
		<img src="<?php echo $this->utils->imageUrl($this->utils->getConfig('random_bonus_top_img_url')); ?>">
	</div>
	<div class="col-md-12">
		<?php if ($bonusChance) {?>
		<h3><?php echo lang('random.bonus.message1') . " " . $bonusChance . " " . lang('random.bonus.message4'); ?></h3>
		<a href='<?php echo site_url('iframe_module/pick_up_bonus/' . $random_bonus_mode . '/' . $promoCategoryId) ?>' class="btn btn-warning"><?php echo lang('random.bonus.clickHere'); ?></a>
		<?php } else {?>
		<h3><?php echo lang('random.bonus.message3'); ?></h3>
	<?php }
?>
	</div>
	<div class="col-md-12" style="margin-top: -20px;">
		<img src="<?php echo $this->utils->imageUrl($this->utils->getConfig('random_bonus_center_img_url')); ?>"></a>
	</div>
</div>
