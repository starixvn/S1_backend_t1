<?php if(isset($error)): ?>
    <div class="panel panel-danger panel_main panel_error">
        <div class="panel-body">
            <?=$error?>
        </div>
    </div> <!-- EOF .panel_error -->
<?php else: ?>
    <!-- Before Handling Callback Error Msg  -->
    <?php if(!empty($fail_msg)): ?>
        <div class="panel panel-warning panel_fail_msg">
            <div class="panel-heading">
                <?=lang('Error Message Before Handling Callback')?>
            </div>
            <div class="panel-body">
                <?=$fail_msg?>
            </div>
        </div> <!-- EOF .panel_fail_msg -->
    <?php endif; ?>
    <!-- /Before Handling Callback Error Msg  -->

    <?php foreach($content as $key => $value):?>
        <?php if($key == 'url'): ?>
            <h4><b><?=lang('Sent to')?>: <?=$value?></b></h4>
        <?php elseif($key == 'params'): ?>
            <h4><b><?=lang('Sent Params')?> (<?=$key?>)</b></h4>
            <textarea cols='150' rows='15'><?=$value?></textarea>
        <?php elseif($key == 'content'): ?>
            <h4><b><?=lang('Received Params')?> (<?=$key?>)</b></h4>
            <?php if(strpos($value,'content.redirected')===0):?>
                <?=lang($value)?>
            <?php elseif(strpos($value,'content.redirected.player_center.api')===0):?>
                <?=lang($value)?>
            <?php else:?>
                <textarea cols='150' rows='15'><?=$value?></textarea>
            <?php endif; ?>
        <?php elseif($key == 'callbackExtraInfo' || $key == '_REQUEST' || $key == '_RAW_POST' || $key == '_RAW_POST_XML_JSON'): ?>
            <h4><b><?=lang('Callback Content')?> (<?=$key?>)</b></h4>
            <textarea cols='150' rows='15'><?=$value?></textarea>
        <?php endif; ?>
    <?php endforeach;?>



    <?php
    // Patch for OGP-12954 在 payment api callback request 內加上可重複發送的機制
    if($request_api == 'deposit'):
    ?>
    <div class="panel panel-primary panel_resend" >
        <div class="panel-heading">
            <h4 class="panel-title">ReSend
                <a href="#resend_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
            </h4>
        </div>

        <div id="resend_panel" class="panel-collapse collapse in ">
            <form class="resend-form" >
                <input name="error_msg" type="hidden" value="<?php echo $error_msg; ?>">
                <input name="respIdStr" type="hidden" value="<?php echo $respIdStr; ?>">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-10 Description">
                            Resend the request for deposit.
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 respText hidden">
                            <div class="statusEq1 hidden">
                                Link to View response results by Order ID, <a href="#" class="view_response_results_link" target="_blank"><span class="secure_id"></span></a>
                            </div>
                            <div class="statusEq0 hidden">
                            </div>
                        </div>
                    </div>
                </div><!-- EOF .panel-body -->
                <div class="panel-footer">
                    <button type="button" class="btn btn-warning action-resend" >Resend</button>
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom hidden loadding-resend"></i>
                </div>
            </form> <!-- EOF .resend-form -->
        </div> <!-- EOF #resend_panel -->
    </div> <!-- EOF .panel.panel_resend -->
    <?php
    endif; // EOF // EOF if($request_api == 'deposit')
    ?>

    <!-- Callback Error Msg  -->
    <?php if(!empty($error_msg)): ?>
        <h4><b><?=lang('Callback Error')?></b> -<?=lang('for internal use only')?></h4>
        <?=$error_msg?>
    <?php endif; ?>
    <!-- /Callback Error Msg  -->

    <!-- _SERVER Vars -->
    <?php if(isset($server)):?>
        <fieldset style="padding-bottom: 8px">
            <legend>
                <label class="control-label"><?=lang('HTTP SERVER')?></label>
                <a id="server_vars_toggle_btn" style="text-decoration:none; border-radius:2px;" class="btn btn-primary btn-xs">
                    <span class="fa fa-plus-circle"><?=lang("Expand All")?></span>
                </a>
            </legend>
            <div id="server_vars">
                <table>
                <?php foreach($server as $key => $value):?>
                    <tr>
                        <td><?=$key?></td>
                        <td><?=is_array($value) ? json_encode($value): $value?></td>
                    </tr>
                <?php endforeach;?>
                </table>
            </div>
        </fieldset>
    <?php endif; ?>
    <!-- /_SERVER Vars -->
<?php endif; ?>


<script type="text/javascript">
    $(document).ready(function(){
        $('#view_resp_result').addClass('active');

        $('#server_vars').hide();
        $('#server_vars_toggle_btn').click(function(){
            $('#server_vars').toggle();
            if($('#server_vars_toggle_btn span').attr('class') == 'fa fa-plus-circle'){
                $('#server_vars_toggle_btn span').attr('class', 'fa fa-minus-circle');
                $('#server_vars_toggle_btn span').html(' <?=lang("Collapse All")?>');
            }
            else{
                $('#server_vars_toggle_btn span').attr('class', 'fa fa-plus-circle');
                $('#server_vars_toggle_btn span').html(' <?=lang("Expand All")?>');
            }
        });

        $('body').on('click','.action-resend:not(.disabled)', function(e){
            var theTarget$El = $('.action-resend');
            if(e){
                theTarget$El = $(e.target);
            }

            if(confirm('Are u sure?')){
                theForm$El = theTarget$El.closest('form');
                var respIdStr = theForm$El.find('input[name="respIdStr"]').val();
                doResend(respIdStr);
            }else{

            }
        });

        function doResend(respIdStr){

            var ajaxUri = '<?php echo site_url('/system_management/resend_response_content');?>';
            var data = {};
            data.respId = respIdStr;
            $.ajax({
                url : ajaxUri,
                type : 'POST',
                data : data,
                dataType : "json",
                cache : false,
                beforeSend:function(){
                    // for show loadding-icon
                    $('.loadding-resend').removeClass('hidden');
                    $('.action-resend').addClass('disabled');

                    $('.respText').addClass('hidden');
                    $('.statusEq0').addClass('hidden');
                    $('.statusEq1').addClass('hidden');
                },
            }).done(function (data) {
                if(data.status == '1'){

                    BootstrapDialog.show({
                        title: 'Resent Response',
                        message: data.response
                    });


                    if(data.sourceSaleOrder.secure_id){
                        // link for Review  Response Result by the order
                        var link='/system_management/view_resp_result?order_id='+ data.sourceSaleOrder.secure_id;
                        $('.view_response_results_link').prop('href', link);
                        $('.secure_id').text(data.sourceSaleOrder.secure_id);
                        $('.respText').removeClass('hidden');
                        $('.statusEq1').removeClass('hidden');
                    }
                    // http://admin.og.local/system_management/view_resp_result?order_id=D146418550384
                }else{
                    if(data.msg){
                        $('.statusEq0').html(data.msg);
                        $('.respText').removeClass('hidden');
                        $('.statusEq0').removeClass('hidden');
                    }
                }
            }).fail(function (jqXHR, textStatus) {
                /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                if(jqXHR.status>=300 && jqXHR.status<500){
                    location.reload();
                }else{
                    $('.statusEq0').html(textStatus);
                    $('.respText').removeClass('hidden');
                    $('.statusEq0').removeClass('hidden');
                }
            }).always(function(){
                // for hidden loadding-icon
                $('.loadding-resend').addClass('hidden');
                $('.action-resend').removeClass('disabled');
            });
            // 回應 重送主鍵 或 檢視列表 或 連結。


        }
    });
</script>