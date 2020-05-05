<div id="viprewards" class="tab-pane vip-rewards-container main-content">

  <h1><span id="vipGroupPageVipGroupNameTxt"><?=lang('VIP GROUP')?></span></h1>
  <h4 class="vip-group-name"><span id="vipGroupPageVipGroupLvlNameTxt">VIP GROUP NAME</span></h4>
  <div class="row">
    <div class="col-xs-12 col-sm-6">
      <div class="inner-content">
        <p><?php echo lang("Available Cashback") ?></p>
        <h2 class="available-rebate"><?php echo $currency['symbol'] ?><span id="vipGroupPageAvailableCashbackAmtTxt"></span></h2>
        <!-- <a class="btn d-btn member-center-deposit-btn" href="#fundManagement"><?php echo lang("Apply") ?></a> -->
      </div>
    </div>
    <div class="col-xs-12 col-sm-6">
      <div class="inner-content">
        <p><?php echo lang("Total Cashback") ?></p>
        <h2><?php echo $currency['symbol'] ?><span id="vipGroupPagePlayerTotalCashbackAmtTxt" style="color:#000"></span></h2>
      </div>
    </div>
  </div>
  <p class="vip-rewards-panel" id="vipBirthdayBonusSec">
    <img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/icons/c05.png" alt="<?php echo lang("Birthday Bonus") ?>">
    <span><?php echo lang("Birthday Bonus") ?>:</span>
    <?php echo $currency['symbol'] ?><span id="vipGroupPageBirthdayBonusAmtTxt" style="color:#000"></span>(<b id="daysLeftTxt" style="color:#000;margin-left: 0px;padding-left: 0px;"></b>) <?php echo lang("more days until you can claim") ?>
  </p>
</div>
