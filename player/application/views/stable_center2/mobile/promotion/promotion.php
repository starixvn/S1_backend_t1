<div class="dropdown select-promo-category-dropdown">
    <label for="select_promo_category_toggle"><?=lang('cms.selectPromoCat')?>: </label>
    <button class="btn btn-primary dropdown-toggle" type="button" id="select_promo_category_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span><?=lang('cms.selectPromoCat')?></span>
        <span class="caret"></span>
    </button>

    <ul class="dropdown-menu" aria-labelledby="select_promo_category_toggle">
        <li role="presentation" value="" disabled="disabled"><a href="javascript: void(0);" role="menuitem"><?=lang('cms.selectPromoCat')?></a></li>
        <?php foreach($promoCategoryList as $promo_data){ ?>
        <li role="presentation" value="<?=$promo_data['id']?>"><a href="javascript: void(0);" role="menuitem"><?=lang($promo_data['name'])?></a></li>
        <?php } ?>
    </ul>
</div>
<div class="clearfix"></div>
<div class="promotions">
    <!-- Main Content -->
    <?php foreach ($promoCategoryList as $row) : ?>
    <div id="promo_category_<?=$row['id']?>" class="promotions-category-list hide" data-promo_category_id="<?=$row['id']?>">
        <?php
        $promo_item_count = 0;
        foreach($promo_list as $promo_item){
            if($row['id'] !== 0 && $row['id'] != $promo_item['promorule']['promoCategory']){
                continue;
            }
            $promo_item_count++;
        ?>
        <div class="pr_show threepage cpt5" data-promo_item_anchor="promo_item_<?=$promo_item["promoCmsSettingId"]?>">
            <?php if(!$this->utils->isEnabledFeature('hidden_player_center_promotion_page_title_and_img')) :  ?>
            <div class="primage">
                <?php
                if(file_exists($this->utils->getPromoThumbnails() . $promo_item['promoThumbnail']) && !empty($promo_item['promoThumbnail'])){
                    $promoThumbnail = $this->utils->getPromoThumbnailRelativePath(FALSE) . $promo_item['promoThumbnail'];
                }else{
                    if(!empty($promo_item['promoThumbnail'])){
                        $promoThumbnail = $this->utils->imageUrl('promothumbnails/' . $promo_item['promoThumbnail']);
                    }else{
                        $promoThumbnail = $this->utils->imageUrl('promothumbnails/default_promo_cms_1.jpg');
                    }
                }
                ?>
                <img src="<?=$promoThumbnail;?>?v=<?=PRODUCTION_VERSION?>">
            </div>
            <div class="title">
                【<?php echo $promo_item['promoName'] ?>】
            </div>
            <?php endif; ?>
            <div class="description"><?php echo $promo_item['promoDescription'] ?></div>
            <div class="actions">
                <?php if(!$this->utils->isEnabledFeature('disabled_show_promo_detail_on_list')) :  ?>
                    <a href="javascript: void(0);" class="btn btn-sm btn-info" data-toggle="collapse" data-target="#promo_<?=$row['id']?>_item_<?=$promo_item["promoCmsSettingId"]?>_detail"><?=lang('lang.details')?></a>
                <?php endif; ?>
                <?php if($this->utils->isEnabledFeature('enabled_request_promo_now_on_list')) :  ?>
                    <?php if($promo_item['allow_claim_promo_in_promo_page']): ?>
                        <a href="javascript: void(0);" class="btn btn-sm btn-info" onclick="requestPromoNow('<?=$promo_item['promoCmsSettingId']?>');"><?=lang('Claim Now');?></a>
                    <?php else: ?>
                        <a href="<?=$promo_item['claim_button_url']?>" class="btn btn-sm btn-info"><?=$promo_item['claim_button_name']?></a>
                    <?php endif ?>
                <?php endif; ?>
            </div>
        </div>
        <div id="promo_<?=$row['id']?>_item_<?=$promo_item["promoCmsSettingId"]?>_detail" class="collapse">
            <div class="panel-body">
                <div class="lestCo">
                    <?=html_entity_decode($promo_item['promoDetails'], ENT_QUOTES, 'UTF-8')?>
                </div>
                <div class="action">
                    <?php if($promo_item['allow_claim_promo_in_promo_page']): ?>
                        <a href="<?=site_url("iframe_module/request_promo/" . @$promo_item['promoCmsSettingId']);?>" class="btn btn-sm btn-info"><?=lang('Claim Now');?></a>
                    <?php else: ?>
                        <a href="<?=$promo_item['claim_button_url']?>" class="btn btn-sm btn-info"><?=$promo_item['claim_button_name']?></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($promo_item_count === 0): ?>
        <div class="no_data">
            <center><?=lang('lang.norec')?></center>
        </div>
        <?php endif ?>
    </div>
    <?php endforeach; ?>

    <?php if($this->utils->isEnabledFeature('enable_mobile_copyright_footer')): ?>
        <?=$this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/mobile/includes/template_footer');?>
    <?php endif; ?>
</div>
<script>
var promotion_category_id = <?=$default_show_category_id?>;

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

$(document).ready(function(){
    $(".select-promo-category-dropdown .dropdown-menu li").on("click", function(){
        if($(this).attr('disabled')){
            return false;
        }

        var str = $(this).text();
        var promotion_category_id = $(this).attr('value');

        $(this).parent().parent().parent().find(".dropdown-toggle span:first").html(str);
        $('#jltext').html(str);
        // $('.header_title_text').html(str);

        $('.promotions-category-list').addClass('hide');
        $('#promo_category_' + promotion_category_id).removeClass('hide');
    });

    // auto redirect to anchor
    var promoAnchorPrefix = window.location.hash.substr(1);
    var anchor_promo = (promoAnchorPrefix.indexOf("promo_item_") !== -1) ? $(".promotions div[data-promo_item_anchor=" + promoAnchorPrefix + "]") : false;
    if(anchor_promo != false && anchor_promo.length > 0){
        promotion_category_id = anchor_promo.parent().data('promo_category_id');
        $(".select-promo-category-dropdown .dropdown-menu li[value=" + promotion_category_id + "]").trigger('click');

        var top = anchor_promo.offset().top - $('.header').height();
        if(top > 0){
            $('html,body').animate({scrollTop: top}, 1000);
        }
        anchor_promo.click();
    }else{
        $(".select-promo-category-dropdown .dropdown-menu li[value=" + promotion_category_id + "]").trigger('click');
    }
});
</script>