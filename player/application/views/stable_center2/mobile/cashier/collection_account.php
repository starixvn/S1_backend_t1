<style type="text/css">
    .ttl-header {
        background: rgba(0,0,0,0.05);
        height: auto;
        line-height: 34px;
        border-bottom: 1px solid #ddd;
        border-top: 1px solid #ddd;
        text-align: left;
        text-indent: 15px;
        font-size: 18px !important;
        font-weight: bold;
    }
    .ttl-dep li{
        line-height: 34px;
        border-bottom: 1px solid #ddd;
        border-top: 0px solid #ddd;
        text-align: left;
        text-indent: 15px;
        font-size: 14px;
    }
.ttl-dep li span.w30{
    box-shadow: 1px 0 0px #ccc;
}

.ttl-dep li:first-child{
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #ddd;
}

.notes-red {
    color:  #ff0000;
    padding: 0 15px;
    font-size: 14px;
}

.decur {
    position: absolute;
    width: 100%;
    z-index: 99;
    height: 60px;
    line-height: 60px;
    color: #fff;
    font-size: 17px;
}
</style>
<div class="qbmain" id="qbmain_zz">
        <div class="deposit_content" id="deposit_content" style="position: relative;text-align: center;">

                <?php if (empty($is_error)){ ?>

                <?php if (empty($qrcodeUrl) && empty($qrcodeBase64Url) && empty($qrcodeBase64) && (empty($staticData)) && empty($qrcodeImage)) { ?>

                    <div  style="margin-top: 55px; text-align: left;margin-bottom: 15px;">
                        <div style="margin-top: 60px; text-align: left;">
                          <div class="notes-red">
                            <strong><?=lang('cms.notes')?></strong>:
                            <?=lang('collection.text.1')?>
                          </div>
                        </div>
                    <div>
                <?php } ?>

                        <ul class="ttl-dep">
                <?php if (!empty($qrcodeUrl) || !empty($qrcodeBase64Url) || !empty($qrcodeBase64) || !empty($qrcodeImage)) { ?>
                        <li class="clearfix">
                            <h3 class="page-header" style="margin-top: 0"><?=lang('Please scan QRCode:')?></h3>
                            <?php if(!empty($qrcode_upper_msg)) : ?>
                                <p><?=$qrcode_upper_msg?></p>
                            <?php endif;?>

                            <?php if(!empty($qrcodeUrl)) { ?>
                                <img src="<?php echo QRCODEPATH . urlencode($qrcodeUrl); ?>" width="175" />
                            <?php }elseif(!empty($qrcodeBase64Url)) { ?>
                                <img src="<?php echo QRCODE_BASE64PATH . base64_encode($qrcodeBase64Url); ?>" width="175" />
                            <?php } elseif (!empty($qrcodeBase64)) { ?>
                                <?php if(mb_substr($qrcodeBase64, 0, 11) == 'data:image/' ) { ?>
                                    <img src="<?=$qrcodeBase64?>" width="175" />
                                <?php } else { ?>
                                    <img src="data:image/gif;base64,<?=$qrcodeBase64?>" width="175" />
                                <?php } ?>
                             <?php } elseif (!empty($qrcodeImage)) { ?>
                                <img src="<?=$qrcodeImage;?>" width="175" />
                            <?php } ?>

                            <?php if(!empty($qrcode_lower_msg)) : ?>
                                <p><?=$qrcode_lower_msg?></p>
                            <?php endif;?>
                        </li>

                <?php } ?>

                <?php if (!empty($staticData)) : ?>
                <?php if(!$is_not_display_recharge_instructions) : ?>
                <div style="margin-top: 55px; text-align: left;margin-bottom: 15px;">
                    <div style="margin-top: 60px; text-align: left;">
                        <div class="notes-red">
                            <strong><?= isset($collection_text_transfer['title'])? $collection_text_transfer['title'] :lang('cms.notes.1').':';?></strong><br>
                            <?php
                                if(!empty($collection_text_transfer) && isset($collection_text_transfer)) {
                                    foreach ($collection_text_transfer as $key => $val){
                                        if(preg_match("/hide/i", $key) || preg_match("/title/i", $key)){
                                            if (!preg_match("/title/i", $key)){
                                                echo $val."<br>";
                                                continue;
                                            }
                                        }else{
                                            echo $key.$val."<br>";
                                        }
                                    }
                                } else {
                                    echo lang('collection.text.transfer.1')."<br>";
                                    echo lang('collection.text.transfer.2')."<br>";
                                    echo lang('collection.text.transfer.3');
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <li class="ttl-header"><?=lang('Deposit Info')?></li>
                    <?php foreach($staticData as $key => $value) : ?>
                        <?php if(!isset($key) || !isset($value)) continue;  ?>
                        <li class="clearfix">
                            <span class="w30 fl"><?=lang($key)?></span>
                            <span class="w70 fr"><?=$value?></span>
                        </li>
                    <?php endforeach;?>
                <?php endif;?>

                            <li class="ttl-header"><?=lang('collection.heading.1')?></li>
                            <?php if ($order_info->secure_id): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.1')?></span>
                                    <span class="w70 fr"><?=$order_info->secure_id?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->payment_account_name): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.2')?></span>
                                    <span class="w70 fr"><?=$order_info->payment_account_name?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->payment_account_number): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.3')?></span>
                                    <span class="w70 fr"><?=$order_info->payment_account_number?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->payment_branch_name): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.5')?></span>
                                    <span class="w70 fr"><?=$order_info->payment_branch_name?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->amount): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.4')?></span>
                                    <span class="w70 fr"><?=$order_info->amount?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->created_at): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.6')?></span>
                                    <span class="w70 fr"><?=$order_info->created_at?></span>
                                </li>
                            <?php endif;?>
                            <?php if ($order_info->timeout_at): ?>
                                <li class="clearfix">
                                    <span class="w30 fl"><?=lang('collection.label.7')?></span>
                                    <span class="w70 fr"><?=$order_info->timeout_at?></span>
                                </li>
                            <?php endif;?>
                        </ul>

                    </div>
                </div>

                <br>
            <?php }else{
                echo htmlspecialchars($message);
            } ?>

        </div>
</div>