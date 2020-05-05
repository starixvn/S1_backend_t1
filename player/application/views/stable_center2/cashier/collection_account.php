<?php
$is_qrcode=!(!empty($order_info->account_image_filepath) && !isset($qrcodeUrl) && !isset($qrcodeBase64Url) && !isset($qrcodeBase64) && !isset($qrcodeImage));
?>
<style type="text/css">
.order_id{
    color: #ee0000;
    font-size: 120%;
}
.alert{
    top: 5%;
}
.collection_account .page-header{
    margin: 0px;
}

.notes-red {
    color:  #ff0000;
    padding: 0 15px;
    font-size: 14px;
}
</style>
<div class="collection_account panel panel-default">
    <div class="panel-body">
        <div class="row hidden-print">
            <div class="col-xs-5">
            <?php
                $imageWidth = $this->config->item('account_image_width');
                $imageHeight = $this->config->item('account_image_height');
                if (!$is_qrcode) {
            ?>
                <p><?=lang('collection.scan_qrcode')?> <span class='order_id'><?php echo $order_info->secure_id; ?></span></p>
                <a class="fancybox" href="<?=site_url('resources/images/account/') . '/' . $order_info->account_image_filepath;?>"><img src='<?=site_url('resources/images/account/') . '/' . $order_info->account_image_filepath;?>' width='<?php echo $imageWidth; ?>' height='<?php echo $imageHeight; ?>'></a>
            <?php
            }
            ?>
            <?php if (!empty($qrcodeUrl) || !empty($qrcodeBase64Url) || !empty($qrcodeBase64) || !empty($qrcodeImage)) : ?>
                <h5 class="page-header" style="margin-top: 0"><?=lang('Please scan QRCode:')?></h5>
                <?php if(!empty($qrcode_upper_msg)) : ?>
                    <p><?=$qrcode_upper_msg?></p>
                <?php endif;?>

                <?php if(!empty($qrcodeUrl)) : ?>
                    <img src="<?php echo QRCODEPATH . urlencode($qrcodeUrl); ?>" width="200" />
                <?php elseif(!empty($qrcodeBase64Url)) : ?>
                    <img src="<?php echo QRCODE_BASE64PATH . base64_encode($qrcodeBase64Url); ?>" width="200" />
                <?php elseif (!empty($qrcodeBase64)) : ?>
                    <?php if(mb_substr($qrcodeBase64, 0, 11) == 'data:image/' ) { ?>
                        <img src="<?=$qrcodeBase64?>" width="200" />
                    <?php } else { ?>
                        <img src="data:image/gif;base64,<?=$qrcodeBase64?>" width="200" />
                    <?php } ?>
                <?php elseif(!empty($qrcodeImage)) : ?>
                    <img src="<?=$qrcodeImage; ?>" width="200" />
                <?php endif; ?>

                <?php if(!empty($qrcode_lower_msg)) : ?>
                    <p><?=$qrcode_lower_msg?></p>
                <?php endif;?>

            <?php elseif (!empty($staticData)) : ?>
            <div class="panel panel-default">
                <div class="panel-heading" style="margin-top: 0"><?=lang('Deposit Info')?></div>
                <table class="table table-bordered">
                    <tr></tr> <?php # empty line to workaround css first row no border ?>
                <?php foreach($staticData as $key => $value) : ?>
                    <?php if(!isset($key) || !isset($value)) continue;  ?>
                    <tr>
                        <th><?=lang($key)?></th>
                        <td><?=$value?></td>
                    </tr>
                <?php endforeach;?>
                </table>
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
            </div>
            <?php elseif (!empty($is_error)) : ?>
            <p><?=htmlspecialchars($message)?></p>
            <?php else : ?>
                <br><br>
                <h3 class="page-header" style="margin-top: 0"><?=lang('cms.notes')?></h3>
                <p><?=lang('collection.text.1')?></p>
                <br/>
                <div class="pull-right">
                    <button type="button" class="btn btn-default" onclick="window.print()"><?=lang('action.print_current_page')?></button>
                    <a href="<?=site_url('iframe_module/iframe_viewCashier')?>" class="btn btn-danger" onclick="return confirm('<?=lang('collection.cancel.confirm')?>')"><?=lang('collection.button.1');?></a>
                    <a href="<?=site_url('iframe_module/iframe_viewCashier')?>" class="btn btn-primary"><?=lang('lang.close');?></a>
                </div>
            <?php endif;?>
        </div>
        <?php if (empty($is_error)){ ?>
        <div class="col-xs-7">
            <div class="panel panel-default">
                <div class="panel-heading"><?=lang('collection.heading.1')?></div>
                <table class="table table-bordered">
                        <tr></tr>
                        <?php if ($order_info->secure_id): ?>
                        <tr>
                            <th><?=lang('collection.label.1')?></th>
                            <td class="order_id"><?=$order_info->secure_id?></td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->payment_account_name): ?>
                        <tr>
                            <th>
                                <span id="payment_account_name_lbl">
                                    <?=lang('collection.label.2')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_account_name">
                                    <?=$order_info->payment_account_name?>
                                </span>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->payment_account_number): ?>
                        <tr>
                            <th>
                                <span id="payment_account_number_lbl">
                                    <?=lang('collection.label.3')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_account_number">
                                    <?=$order_info->payment_account_number?>
                                </span>
                                <button class="ccb-btn btn btn-success pull-right"><?=lang('Copy')?></button>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->payment_branch_name): ?>
                        <tr>
                            <th>
                                <span id="payment_branch_name_lbl">
                                    <?=lang('collection.label.5')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_branch_name">
                                    <?=$order_info->payment_branch_name?>
                                </span>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->amount): ?>
                        <tr>
                            <th>
                                <span id="payment_amount_lbl">
                                    <?=lang('collection.label.4')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_amount">
                                    <?=$order_info->amount?>
                                </span>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->created_at): ?>
                        <tr>
                            <th>
                                <span id="payment_request_date_lbl">
                                    <?=lang('collection.label.6')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_request_date">
                                    <?=$order_info->created_at?>
                                </span>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ($order_info->timeout_at && !$hide_timeout): ?>
                        <tr>
                            <th>
                                <span id="payment_request_expired_date_lbl">
                                    <?=lang('collection.label.7')?>
                                </span>
                            </th>
                            <td>
                                <span id="payment_request_expired_date">
                                    <?=$order_info->timeout_at?>
                                </span>
                            </td>
                        </tr>
                        <?php endif;?>
                </table>
            </div>
        </div>
        <?php }?>
    </div>

    <?php if(isset($order_info)) : ?>
    <div class="visible-print-block">
        <?php if (!empty($order_info->account_image_filepath)) : ?>
        <a class="fancybox" href="<?php echo $order_info->account_image_filepath; ?>"><img src='<?php echo $order_info->account_image_filepath; ?>' width='<?php echo $imageWidth; ?>' height='<?php echo $imageHeight; ?>'></a>
        <?php endif; ?>
        <table class="table">
            <tr></tr>
            <tr>
                <th><?=lang('collection.label.1')?></th>
                <td><?=$order_info->secure_id?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.2')?></th>
                <td><?=$order_info->payment_account_name?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.3')?></th>
                <td><?=$order_info->payment_account_number?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.5')?></th>
                <td><?=$order_info->payment_branch_name?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.4')?></th>
                <td><?=$order_info->amount?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.6')?></th>
                <td><?=$order_info->created_at?></td>
            </tr>
            <tr>
                <th><?=lang('collection.label.7')?></th>
                <td><?=$order_info->timeout_at?></td>
            </tr>
        </table>
        <hr/>
        <p><?=lang('collection.text.1')?></p>
    </div>
    <?php endif; ?>
    </div>
</div>
<br/>
<script type="text/javascript">
    $(document).ready(function() {
        $(".fancybox").fancybox();

        _player_center_utils.initRefreshSaleOrder($, <?php echo isset($order_info->id) ? $order_info->id : 'null';?>);

    });
</script>

<?php if(isset($order_info)) : ?>
<script>
    var clipboard = new Clipboard('.ccb-btn', {
        text: function() {
            var payment_account_number = "<?=$order_info->payment_account_number?>";//$("#payment_account_number").text();
            return payment_account_number;
        }
    });

    clipboard.on('success', function(e) {
    });

    clipboard.on('error', function(e) {
    });

    <?php if(isset($statusUrl) && isset($statusSuccessKey)) :
    # If there is a status URL and success key, add javascript to poll this URL until success key is seen, then redirect to the status URL.
    ?>
    setInterval(function(){
        $.ajax("<?=$statusUrl?>", {
                success: function(responseText) {
                    if(responseText.includes("<?=$statusSuccessKey?>")) {
                        window.location.replace("<?=$statusUrl?>");
                    }
                }
            }
        )
    }, 5000); // poll this page every 5 seconds
    <?php endif; ?>
</script>
<?php endif; ?>

<?php if(isset($getExternalApi_btn)) : ?>
<script type="text/javascript">
    $('#getExternalApi_btn').on('click', function(){
        var orderid = "<?=$order_info->id?>";
        $.ajax({
            'url' : base_url +'api/getExternalApiResponseByOrderId/'+ orderid,
            'type' : 'GET',
            'success' : function (data) {
                if (data.success) {
                    $('#getExternalApi_btn').parent().html(data.message);
                    $('#getExternalApi_btn').html('done');
                }
                else{
                    alert(data.message);
                }
            },
        });
    });
</script>
<?php endif; ?>

<?php if(isset($setExternalApi_btn)) : ?>
<script type="text/javascript">
    $('#setExternalApi_btn').on('click', function(){
        var orderid = "<?=$order_info->id?>";
        var external_data = $('#external_data').val();
        $.ajax({
            'url' : base_url +'api/setExternalApiValueByOrderId/'+ orderid,
            'type' : 'POST',
            'data' : {'external_data': external_data},
            'dataType' : "json",
            'success' : function (data) {
                if (data.success) {
                    $('#setExternalApi_btn').parent().html(external_data);
                    $('#setExternalApi_btn').html('done');
                    alert(data.message);
                }
                else{
                    alert(data.message);
                }
            },
        });
    });
</script>
<?php endif; ?>