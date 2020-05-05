<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary panel_player_info">
            <div class="panel-body">
                <table class="table-mc mc-table-credit-summary table">
                    <thead>
                    <tr>
                        <th class="title"><?=lang("xpj.cashback")?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="summary">

                            <div class="loading-box hide">
                                <img class="loading" src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/loading.gif">
                            </div>

                            <div id="platform-credit-summary" class="platform-credit-summary">

                                <div id="resultTable" class="main-summary">
                                    <?php if($can_user_cashback): ?>

                                        <?php if(!empty($disable_request)): ?>

                                            <div class="main-summary"><?=$disable_hint?></div>

                                        <?php elseif(empty($cashback_request)): ?>

                                            <div class="platform"><?=lang('xpj.cashback.request_datetime')?></div>
                                            <div class="credit"><?=date('Y-m-d H:i:s')?></div>
                                            <div class="platform"><?=lang('xpj.cashback.request_amount')?></div>
                                            <div class="credit"><?=$amount?></div>

                                        <?php else : ?>

                                            <div class="platform"><?=lang('xpj.cashback.request_datetime')?></div>
                                            <div class="credit"><?=$cashback_request->request_datetime?></div>
                                            <div class="platform"><?=lang('xpj.cashback.request_amount')?></div>
                                            <div class="credit"><?=$cashback_request->request_amount?></div>
                                            <div class="platform"><?=lang('xpj.cashback.status')?></div>
                                            <div class="credit"><?=lang('xpj.cashback.pending')?></div>

                                        <?php endif; ?>

                                    <?php else : ?>
                                        <div class="main-summary"><?=lang('xpj.cashback.can_not_cashback')?></div>
                                    <?php endif; ?>


                                </div>

                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td class="action right">


                            <?php if($can_user_cashback && empty($cashback_request) && empty($disable_request)): ?>

                            <div class="cashback-request">
                                <button class="mc-btn-refresh-credit-summary mc-btn mc-btn-default" data-toggle="refresh-credit-summary" title="<?=lang('xpj.cashback.cashback_immediately') . lang('xpj.cashback.cashback_settle')?>" onclick='requestCashback(); return false;'><?=lang('xpj.cashback.cashback_immediately') . lang('xpj.cashback.cashback_settle')?></button>
                            </div>

                            <?php endif; ?>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end of row-->

<div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true" id="summaryModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body summaryModal_body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><?php echo lang('Close'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">

    function requestCashback(){

        $('.cashback-request').hide();

        <?php if($disable_request):?>
        alert("<?php echo $disable_hint;?>");
        <?php else : ?>

        $('.loading-box').removeClass('hide');

        $.ajax({
            url: "<?=site_url('player_center/cashbackRequest/')?>",
            type: "POST",
            data: {},
            success: function(data) {
                $('#resultTable').html(data);
            }
        }).always(function(){
            $('.loading-box').addClass('hide');
        });

        <?php endif; ?>


    }

</script>