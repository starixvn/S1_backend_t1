<?php
$shoppingList = $this->utils->getAvailableShoppingList();
?>

<div id="shop" class="tab-pane main-content">
    <h1><?php echo lang("Shop"); ?></h1>
    <p><?php echo lang("Shop now using your points."); ?></p>
    <div class="row">
      <?php if (!empty($shoppingList)) {
	foreach ($shoppingList as $key) {
		?>
      <div class="col-sm-6">
        <div class="shop-content">
          <div class="shop-header">
            <div class="amount"><span><i class="fa fa-cubes" aria-hidden="true"></i><?php echo json_decode($key['requirements'], true)['required_points'] . ' ' . lang("Points"); ?></span></div>
            <h1 class="title-name"><?php echo ucwords($key['title']) ?>
                <?php if ($key['tag_as_new']) {?>
                    <span class="badge-new"><?php echo lang("New"); ?></span>
                <?php }?>
            </h1>
            <?php
              if ($key['banner_url']) {
                if ($key['is_default_banner_flag']) {
                  $shopBannerUrl = $this->utils->imageUrl('shopping_banner/' . $key['banner_url']);
                } else {
                  if (file_exists($this->utils->getShopThumbnailsPath().$key['banner_url'])) {
                    $shopBannerUrl = base_url().'upload/shopthumbnails/'.$key['banner_url'];
                  } else {
                    $shopBannerUrl = $this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg');
                  }
                }
              } else {
                $shopBannerUrl = $this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg');
              }
            ?>
            <img src="<?php echo $shopBannerUrl ?>" width="460" height="199"/>
          </div>
          <div class="shop-body clearfix">
            <div class="col-xs-8"><?php echo $key['short_description']; ?></div>
            <div class="col-xs-4 text-right">
              <a href="#" class="btn viewShopItemDetails" onclick="Shop.displayShoppingDetails(this, <?= $this->authentication->getPlayerId() ?>);" id="<?php echo $key['id'] ?>" data-toggle="modal" data-target="#shop-modal"><?php echo lang("Avail"); ?></a>
            </div>
          </div>
        </div>
      </div>
  <?php }
}?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade shop-modal" id="shop-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <img id="shopItemPreviewImg" src="<?= $this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg') ?>" alt="Shop">
        <h4 class="modal-title" id="myModalLabel"><span id="shopItemTitle"></span></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-7">
            <h4><?php echo lang("Details"); ?>:</h4>
            <p id="shopItemDesc">
            </p>
          </div>
          <div class="col-xs-5 amount">
            <h4><?php echo lang("Required Points"); ?>:</h4>
            <span id="shopItemRequirePoints"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="row" id="shopMsgSec">
          <center>
          <p id="shopMsg"></p>
          </center>
        </div>
        <div id="claimBtnSec">

        </div>
        <!-- <button type="button" id="claimShoppingItemBtn" onclick="claimShoppingItem(this)" class="btn btn-default submit-btn claimShoppingItemBtn"><span id="claimNowBtnTxt"><?php echo lang("Claim Now"); ?></span></button> -->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-shop.js') ?>"></script>
<script type="text/javascript">
$(function(){
    /** initialize default */
    Shop.default_banner_img_src = "<?= $this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg') ?>";
    Shop.claim_now_msg = "<?= lang('Claim Now'); ?>";
    Shop.pending_msg = "<?php echo lang('Pending'); ?>";
});
</script>