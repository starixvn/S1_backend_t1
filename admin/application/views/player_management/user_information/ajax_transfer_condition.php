<div role="tabpanel" class="tab-pane active" id="transferCondition">
    <?php if($this->permissions->checkPermissions('cancel_member_transfer_condition')): ?>
        <button type="button" title="<?=lang('sys.ga.conf.cancel.selected')?>" id="cancel-tc-items" class="btn btn-danger btn-sm">
            <i class="glyphicon glyphicon-remove-circle" data-placement="bottom" ></i>
            <?=lang('lang.cancel');?>
        </button>
    <?php endif;?>
    <div class="panel-body transfer_condition_panel_body">
        <table class="table table-hover table-bordered table-condensed" id="transfer-condition-table">
            <thead>
                <tr>
                    <th><?=lang('sys.pay.action');?></th>
                    <th><?=lang('pay.promoName')?></th>
                    <th><?=lang('cms.requiredAmount')?></th>
                    <th><?=lang('pay.totalPlayerBet')?></th>
                    <th><?=lang('pay.startedAt')?></th>
                    <th><?=lang('pay.bt.updatedon')?></th>
                    <th><?=lang('pay.completedAt')?></th>
                    <th><?=lang('cms.disallow_wallet_transfer_in')?></th>
                    <th><?=lang('cms.disallow_wallet_transfer_out')?></th>
                    <th><?=lang('lang.status')?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        var title = "<?=lang('userinfo.tab07')?>";
        var cancel_tc_modal = $('#cancel_tc_modal');
        var wallet_info_modal = $('#wallet_info_modal');

        var TRANSFER_CONDITION = (function() {
            var transferCondTable = $('#transfer-condition-table').DataTable({
                dom:"<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
                searching: true,
                autoWidth: false,
                stateSave: false,
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ],
                        className: ['btn-linkwater']
                    }
                ],
                columnDefs: [
                    { sortable: false, targets: [0] },
                    <?php if (!$this->permissions->checkPermissions('cancel_member_transfer_condition')) {?>
                        { "targets": [ 0 ], className: "noVis hidden" },
                    <?php } ?>
                ],
                order: [[5, 'desc']]
            }).draw(false);

            var forCancelIds = Array();

            function getTransferCondition(){
                $.ajax({
                    url : '/player_management/getTransferCondition/' + playerId,
                    type : 'GET',
                    dataType : "json"
                }).done(function (obj) {
                    var arr = obj.transferCondition;
                    if(arr){
                        /*Clear the table rows first to prevent appending rows when refresh*/
                        transferCondTable.clear().draw();

                        for (var i = 0; i < arr.length; i++) {
                            var action,
                                promoName,
                                conditionAmount,
                                disallow_transfer_in_wallets_name,
                                disallow_transfer_out_wallets_name,
                                status,
                                currentBet,
                                obj = arr[i];

                            action = '<input type="checkbox" class="transfer-cancel-checkbox" title="<?=lang("sys.ga.conf.select.to.cancel")?>" value="'+obj.id+'" > &nbsp;&nbsp;<i class="fa fa-times-circle transfer-cancel-icon" style="color:#D32A0E;cursor:pointer;" title="<?=lang("sys.ga.conf.cancel.this")?>"  value="'+obj.id+'" data-placement="bottom" ></i>'
                            promoName = obj.promoName || "<?=lang('pay.noPromo')?>";
                            conditionAmount = 0;
                            conditionAmount = (obj.conditionAmount) ? numeral(obj.conditionAmount).format() : 0;
                            disallow_transfer_in_wallets_name = (obj.disallow_transfer_in_wallets_name) ? '<button type="button" class="btn btn-xs btn-primary check_disallow_transfer_in_wallet" data-wallet=\'' + obj.disallow_transfer_in_wallets_name + '\'><?=lang('lang.details')?></button>' : "<?=lang('lang.norecyet')?>";
                            disallow_transfer_out_wallets_name = (obj.disallow_transfer_out_wallets_name) ? '<button type="button" class="btn btn-xs btn-primary check_disallow_transfer_out_wallet" data-wallet=\'' + obj.disallow_transfer_out_wallets_name + '\'><?=lang('lang.details')?></button>' : "<?=lang('lang.norecyet')?>";
                            currentBet = (obj.currentBet) ? numeral(obj.currentBet).format() : 0;

                            if(obj.status == '<?=Transfer_condition::STATUS_ACTIVE?>'){
                               status = "<?=lang('player.ub13')?>";
                            }else if(obj.status == '<?=Transfer_condition::STATUS_CANCEL?>'){
                               status = "<?=lang('lang.cancel')?>";
                            }else if(obj.status == '<?=Transfer_condition::STATUS_COMPLETE?>'){
                               status = "<?=lang('player.ub14')?>";
                            }

                            var transfer_condition_row=[
                                action,
                                promoName,
                                conditionAmount,
                                currentBet,
                                obj.started_at,
                                obj.updated_at,
                                obj.completed_at,
                                disallow_transfer_in_wallets_name,
                                disallow_transfer_out_wallets_name,
                                status
                            ];
                            transferCondTable.row.add( transfer_condition_row ).draw( false );
                        }

                        attachEventsListener();
                    }else{
                        transferCondTable.clear().draw();
                    }
                }).fail(function (jqXHR, textStatus) {
                    if(jqXHR.status<300 || jqXHR.status>500){
                        alert(textStatus);
                    }
                });
            }

            function getTransferConditionWalletInfo(detail_wallets){
                var tbody = '';

                $.each(detail_wallets, function(k, v){
                    tbody += '<tr>';
                    tbody += '<td>' + (k+1) + '. ' + v + '</td>';
                    tbody += '</tr>';
                });

                $('.wallet_body').html(tbody);
            }

            function attachEventsListener(){
                $('.check_disallow_transfer_in_wallet , .check_disallow_transfer_out_wallet').click(function(){
                    getTransferConditionWalletInfo($(this).data('wallet'));
                    wallet_info_modal.modal('show');
                });

                $('.close_wallet_info').click(function(){
                    $('.wallet_body').html('');
                });

                $('.transfer-cancel-icon').click(function(){
                    cancel_tc_modal.modal('show');
                });

                //For paging use delegate
                $('#transfer-condition-table').delegate(".transfer-cancel-checkbox", "click", function(){
                    var id =$(this).val();
                    if($(this).prop('checked')){
                        forCancelIds.push(id);
                    }else{
                        var i =  forCancelIds.indexOf(id);
                        forCancelIds.splice(i, 1);
                    }
                });

                $('#transfer-condition-table').delegate(".transfer-cancel-icon", "click", function(){
                    var id =$(this).attr('value');
                    if(jQuery.inArray(id, forCancelIds) == -1){
                        forCancelIds.push(id);
                    }
                    $(this).prev('input:checkbox').prop('checked', true);
                    showConfirmation();
                });

                //tooltips
                $('#transfer-condition-table').delegate(".transfer-cancel-checkbox", "mouseover", function(){
                    $('.transfer-cancel-checkbox').tooltip({placement : "right"});
                    $('.transfer-cancel-icon').tooltip({placement : "right"});
                });

                $('#cancel-tc-items').tooltip({placement : "top"});
                $('#cancel-tc-items').click(function(){
                    if(!forCancelIds.length){
                        alert("<?=lang('cancel_transfer')?>");
                        return;
                    }
                    showConfirmation();
                });

                $('.tc-cancel-send').click(function(){
                   $('#tc_cancel_reason').next('span.help-block').html('');
                });
            }

            function showConfirmation(){
                cancel_tc_modal.modal('show');
                var items = (forCancelIds.length > 1) ? "<?=lang('sys.dasItems')?>" : "<?=lang('sys.dasItem')?>";
                $('.tc-conf-msg-ask').html("<?=lang('sys.ga.conf.cancel')?> "+forCancelIds.length+" "+items+" ?");
                return;
            }

            function cancelTransferCondition(){
                var cancelManualStatus = "<?=Transfer_condition::STATUS_CANCEL?>";
                var cancel_reason = $('#tc_cancel_reason');

                if(cancel_reason.val() != ''){
                    $('#transfer_condition-submit_btn', cancel_tc_modal).attr("disabled", true);

                    var data = {
                        forCancelIds  : forCancelIds,
                        reasonToCancel : cancel_reason.val(),
                        playerId :  playerId,
                        cancelManualStatus : cancelManualStatus
                    };
                    $.ajax({
                        url : '/player_management/cancelTransferCondition',
                        type : 'POST',
                        data : data,
                        dataType : "json"
                    }).done(function (data) {
                        if (data.status == "success") {
                            $('#transfer_condition-submit_btn', cancel_tc_modal).attr("disabled", false);
                            forCancelIds = Array();
                            cancel_reason.next('div.help-block').html('');
                            cancel_reason.val('');
                            cancel_tc_modal.modal('hide');
                            TRANSFER_CONDITION.refresh();
                            success_modal(title, "<?=lang('cms.deletePromoRuleSuccessMsg')?>")
                        } else if (data.status == "failed") {
                            error_modal(title, data.error_message);
                            cancel_tc_modal.modal('hide');
                        }
                    }).fail(function (jqXHR, textStatus) {
                        $(this).attr("disabled", false);
                        throw textStatus;
                    });
                }else{
                    cancel_reason.next('span.help-block').html('<?=lang("pay.reason")?><?=lang("is_required")?>');
                }
            }

            Number.prototype.format = function(n, x) {
                var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
                return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
            };

            return {
                refresh:function() {
                    getTransferCondition();
                },
                cancel:function(){
                    cancelTransferCondition();
                }
            }
        }());

        /*Load or initiate the existing data*/
        TRANSFER_CONDITION.refresh();

        $('#transfer_condition-submit_btn', cancel_tc_modal).click(function(){
            TRANSFER_CONDITION.cancel();
        });
    });
</script>