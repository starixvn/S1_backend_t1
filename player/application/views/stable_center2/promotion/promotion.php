<div id="promotions" class="tab-pane main-content">
    <h1><?php echo lang("Promotions") ?></h1>

    <div class="tab-content" style="border:none">
        <!-- ============= MY PROMO CONTENT ============= -->
        <div role="tabpanel" class="tab-pane  promotions-section" id="mypromo">
            <div class="row">
                <?php
                $cnt = 0;
                if (!empty($mypromo)) {
                    foreach ($mypromo as $key) {
                        if ($key['promoCmsTitle'] != "_SYSTEM_MANUAL") {
                            $cnt++;
                            ?>
                            <div class="col-sm-6">
                                <div class="promotion-content">
                                    <div class="promotion-header">
                                        <h1 class="title-name"><?php echo ucwords($key['promoCmsTitle']) ?>
                                            <?php if ($key['tag_as_new_flag']) : ?>
                                            <span class="badge-new"><?php echo lang("lang.new") ?></span>
                                            <?php endif ?>
                                        </h1>
                                        <img width="377" height="199" src="<?= $this->utils->getPromoThumbnailsUrl($key['promoThumbnail'], false) ?>"/>
                                    </div>
                                    <div class="promotion-body clearfix">
                                        <div class="col-xs-8">
                                            <p>
                                                <span><?php echo lang("Current Betting Amount") ?>:</span> <?= $currency['symbol'] ?> <?php echo $key['currentBet'] ?> <br />
                                                <span><?php echo lang("Required Betting Amount") ?>:</span> <?= $currency['symbol'] ?> <?php echo $key['withdrawConditionAmount'] ?> <br />
                                                <span><?php echo lang("Remaining Betting Amount") ?>:</span> <?= $currency['symbol'] ?> <?php echo $key['withdrawConditionAmount'] - $key['currentBet'] ?>
                                            </p>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <a href="#" class="btn viewPromoDetailsMyPromoItem" onclick="Promotions.viewMyPromoDetails(this, <?=$playerId?>);" id="<?php echo $key['promoCmsSettingId'] ?>"  data-toggle="modal" data-target="#promodetails_modal"><?php echo lang("View Details") ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }
                        }
                    }
                    if (!$cnt) {
                        echo "<div style='margin:20px'>" . lang("notify.103") . "</div>";
                    }

                    ?>
                </div>
            </div>

        <!-- ============= ALL PROMO CONTENT ============= -->
        <div role="tabpanel" class="tab-pane active promotions-section" id="allpromo">
            <!-- Nav tabs -->
            <ul class="nav nav-pills" role="tablist">
                <?php $ctr=0; ?>
                <?php foreach ($promoCategoryList as $row) : ?>
                    <?php
                    $class = ($default_show_category_id === $row['id']) ? 'active' : '';
                    ?>
                   <li role="presentation" class="<?=$class?>">
                        <a href="#tab<?=$row['id']?>" aria-controls="home" role="tab" data-toggle="tab" class="hi-icon-effect-5" aria-expanded="true">
                            <?php if (!empty($row['icon'])) : ?>
                                <img src="<?= $this->utils->getPromoCategoryIcon($row['icon']) ?>">
                            <?php endif; ?>
                            <span><?=lang($row['name'])?></span>
                        </a>
                    </li>
                    <?php $ctr++; ?>
                <?php endforeach; ?>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <?php $ctr=0; ?>
                <?php foreach ($promoCategoryList as $row) : ?>
                    <?php
                    $class = ($default_show_category_id === $row['id']) ? 'active in' : '';
                    ?>
                    <?php /*
                    <div role="tabpanel" class="tab-pane <?= empty($ctr) ? 'active in' : '' ?>" id="tab<?=$row['id']?>">
                    */?>
                    <div role="tabpanel" class="tab-pane <?=$class?>" id="tab<?=$row['id']?>">
                        <div class="row">
                            <?php
                            if (!empty($promo_list)) {
                                $isCategoryHasPromo = false;
                                foreach ($promo_list as $key) {
                                    if (empty($row['id']) || $key['promorule']['promoCategory'] == $row['id']) {
                                        $isCategoryHasPromo = true;
                                        ?>
                                        <div class="col-sm-6">
                                            <div class="promotion-content">
                                            <?php if(!$this->utils->isEnabledFeature('hidden_player_center_promotion_page_title_and_img')) :  ?>
                                                <div class="promotion-header">
                                                    <h1 class="title-name">
                                                        <?php echo ucwords($key['promoName']) ?>
                                                        <?php if ($key['tag_as_new_flag']) {?>
                                                        <span class="badge-new"><?php echo lang("lang.new") ?></span>
                                                        <?php }?>
                                                    </h1>
                                                    <img width="377" height="199" src="<?= $this->utils->getPromoThumbnailsUrl($key['promoThumbnail'], false) ?>"/>
                                                </div>
                                                <?php endif; ?>
                                                <div class="promotion-body clearfix">
                                                    <div class="col-xs-8">
                                                        <p>
                                                            <?php echo $key['promoDescription']; ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4 text-right">
                                                        <?php if(!$this->utils->isEnabledFeature('disabled_show_promo_detail_on_list')) :  ?>
                                                        <a href="javascript: void(0);" class="btn viewPromoDetailsAllPromoItem" onclick="Promotions.viewPromoDetailsAll(this, <?=$playerId?>);" id="<?php echo $key['promoCmsSettingId'] ?>" data-toggle="modal" data-target="#promodetails_modal"><?php echo lang("View Details") ?></a>
                                                        <?php endif; ?>
                                                        <?php if($this->utils->isEnabledFeature('enabled_request_promo_now_on_list')) :  ?>
                                                        <a href="javascript: void(0);" class="btn requestPromoNowItem" onclick="requestPromoNow('<?=$key['promoCmsSettingId']?>');"><?=lang('Claim Now');?></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }

                                if (!$isCategoryHasPromo) {
                                    echo '<br/><br/><br/><center><p>' . lang('cat.no.promo') . '</p></center>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php $ctr++; ?>
                <?php endforeach; ?>

            </div>
        </div>

    </div>
</div>

    <!-- Modal -->
    <div class="modal fade promo-modal" id="promodetails_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <img id="promoItemPreviewImg" src="<?=$this->utils->imageUrl($this->utils->getConfig('default_promo_cms_banner_url'))?>"/>
                    <h4 class="modal-title" id="myModalLabel"><span id="promoCmsTitleModal" style="text-transform:uppercase"></span> <span class="badge-new" id="badgeNew"><?php echo lang("lang.new") ?></span></h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-xs-12">
                            <div id="dateApplied">
                                <p><span><?php echo lang("Date Applied") ?>:</span> <p id="dateAppliedTxt"></p></p>
                            </div>
                            <p><span><?php echo lang("Promo Type") ?>:</span> <p id="promoCmsPromoTypeModal"></p></p>
                            <h4><?php echo lang("sys.description") ?>:</h4>
                            <p id="promoCmsPromoDetailsModal"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" id="promoMsgSec">
                        <center>
                            <p id="promoMsg" style="color: #222;"></p>
                        </center>
                    </div>
                    <?php

                    ?>
                    <div class="applyBtn">
                        <input type="hidden" id="itemDetailsId">
                        <a href="javascript:void(0)" onclick="Promotions.requestPromoNow();" class="requestPromoBtn btn btn-default submit-btn"><?=lang('Claim Now');?></a>
                    </div>
                    <div class="claimLinkBtn">
                        <a href="javascript:void(0)" class="btn btn-default claim-link-btn">&nbsp;</a>
                    </div>
                    <div class="apply-mesg" style="text-align: center; font-weight: bold; color: #222; display: none;">
                    </div>
                    <div class="reject-mesg" style="text-align: center; font-weight: bold; color: #222; display: none;">
                    </div>
                    <button type="button" class="btn btn-default submit-btn" id="closeModal" data-dismiss="modal"><?=lang('Close');?></button>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
function requestPromoNow(promoCmsSettingId){
    Promotions.requestPromoNow(promoCmsSettingId, function(data){
        if(data.status === 'success'){
            MessageBox.success(data.msg, null, function(){
                show_loading();
                window.location.reload(true);
            });
        } else {
            MessageBox.danger(data.msg);
        }
    });
}
$(function(){
    /** initialize default */
    Promotions.currency_symbol = "<?= $this->utils->getCurrentCurrency()['symbol'] ?>";
});
</script>