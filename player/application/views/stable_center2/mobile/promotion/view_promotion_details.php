<div style="padding: 20px 60px 40px; margin-top:20px;" >
	<!-- <img class="img-responsive" src="<?=IMAGEPATH . 'promo/banner-2.png'?>" style="width: 100%;"/> -->
	<!-- <hr/> -->
	<h3><strong><?=$promocms[0]['promoName']?></strong></h3>
	<!-- <img class="img-responsive" src="<?=IMAGEPATH . 'promo/banner-3.jpg'?>" style="width: 100%; margin-bottom: 20px;"/> -->
	<p>
		<?php //var_dump($promocms);
echo $promocms[0]['promoDetails']?>

	</p>
	<div class="row" style="margin-top: 40px;">
		<hr/>
		<div class="col-sm-offset-3 col-sm-3">
			<?php if ($this->authentication->isLoggedIn()) {
	if ($promocms[0]['promoId'] != 0) {
		?>
						<?php if ($promocms[0]['promoType'] == 0) {?>
							<a href="<?=BASEURL . 'iframe_module/applyDepositPromo/' . $promocms[0]['promoId'] . '/' . $promocms[0]['promoCmsSettingId']?>"  class="promo_menu_sec_item_link btn btn-block btn-sm btn-hotel" style="font-weight: bold; text-transform: uppercase;"><?=lang('promo.applyNow')?></a>
						<?php } else {?>
							<a href="<?=BASEURL . 'iframe_module/applyNonDepositPromo/' . $promocms[0]['promoId'] . '/' . $promocms[0]['promoCmsSettingId']?>"  class="promo_menu_sec_item_link btn btn-block btn-sm btn-hotel" style="font-weight: bold; text-transform: uppercase;"><?=lang('promo.applyNow')?></a>
						<?php }
		?>
			<?php } else {?>
					<a href="" class="promo_menu_sec_item_link"><span class='btn btn-block btn-sm btn-danger' style="font-weight: bold; text-transform: uppercase;"><?=lang('promo.contactCS')?></span></a>
			<?php }
} else {?>
					<a href="" class="promo_menu_sec_item_link"><span class='btn btn-block btn-sm btn-danger' style="font-weight: bold; text-transform: uppercase;"><?=lang('promo.loginToJoin')?></span></a>
			<?php }
?>
		</div>
		<div class="col-sm-3">
			<button onclick="goBack()" class="btn btn-sm btn-block btn-hotel" style="font-weight: bold; text-transform: uppercase;"><?=lang('promo.back')?></button>
		</div>
	</div>
</div>