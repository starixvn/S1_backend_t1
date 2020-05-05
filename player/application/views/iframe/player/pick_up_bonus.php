<style type="text/css">
	body {
		background-color:#FF0000;
	}
	.bonus{
		background-image:url('<?php echo $this->utils->imageUrl($this->utils->getConfig('random_bonus_result_img_url')); ?>');
	    background-repeat: no-repeat;
	    /*background-attachment: fixed;*/
	    background-position: center;
	    min-height:800px;
	    /*margin-top:40px;*/
	    padding-top:240px;
	}
</style>
<div class="row bonus" style="text-align: center;">
	<div class="col-md-12">
		<h2><?php echo $username . " " . lang('random.bonus.message2') . " " . $this->utils->displayCurrency($bonus_amount); ?></h2>
		<br/><br/>
		<a href='<?php echo site_url('iframe_module/pick_up_bonus_intro/' . $random_bonus_mode . '/' . $promoCategoryId) ?>' class='btn btn-lg btn-warning'><?php echo lang('random.bonus.goBack'); ?></a>
	</div>
</div>