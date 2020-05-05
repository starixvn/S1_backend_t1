<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAgentPayment" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../../includes/report_tools.php" ?>
        </h4>
    </div>

    <div id="collapseAgentPayment" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form class="form-horizontal" action="<?php echo site_url('agency_management/agency_payment'); ?>" id="search-form" method="get" role="form" name="myForm">
                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label"><?=lang('Date');?> </label>
                        <input type="text" class="form-control input-sm dateInput" id ="filterDate" data-time="true" data-start="#start_date" data-end="#end_date"/>
                        <input type="hidden" name="start_date" id="start_date" value="<?=(isset($input['start_date']) ? $input['start_date'] : '')?>">
                        <input type="hidden" name="end_date" id="end_date" value="<?=(isset($input['end_date']) ? $input['end_date'] : '')?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="control-label"><?=lang('lang.status');?>: </label>
                        <?php echo form_dropdown('status', $status_list, $input['status'], 'class="form-control input-sm"'); ?>
                    </div>
                    <div class="col-md-2">
                        <label for="agent_name" class="control-label"><?=lang('aff.ap03');?>: </label>
                        <input type="text" name="agent_name" class="form-control input-sm"/>
                    </div>
                    <div class="col-md-2" style="padding-top:23px;">
                        <a href="/agency_management/agency_payment" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('lang.reset');?></a>
                        <input type="submit" value="<?=lang('lang.search');?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- display banner -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt"><i class="glyphicon glyphicon-credit-card"></i> <?=lang('Agency Payment');?> </h4>
    </div>

    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12 col-md-offset-0 table-responsive" id="paymentList">
                <table class="table table-bordered table-hover dataTable" id="paymentTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th><?=lang('lang.action');?></th>
                            <th><?=lang('Date');?></th>
                            <th><?=lang('Agent Username');?></th>
                            <th><?=lang('pay.acctname');?></th>
                            <th><?=lang('Amount');?></th>
                            <th><?=lang('Processed Date');?></th>
                            <th><?=lang('Processed By');?></th>
                            <th><?=lang('lang.status');?></th>
                            <th><?=lang('aff.apay11');?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $totalAmt = 0.00;
                        if (!empty($payments)) {
                            foreach ($payments as $row) {?>
                            <tr>
                                <td>
                                <?php if ($row['status'] == 1) {?>
                                    <a href="#" data-toggle="modal" data-target="#approve_dialog" class="btn btn-info btn-xs"
                                    data-historyid="<?php echo $row['agent_payment_history_id']; ?>" data-agentid="<?php echo $row['agent_id']; ?>"
                                    data-amount="<?php echo $row['amount']; ?>"
                                    data-agent_name="<?php echo $row['agent_name']; ?>">
                                        <?php echo lang('Approve'); ?>
                                    </a>

                                    <a href="#" data-toggle="modal" data-target="#decline_dialog" class="btn btn-danger btn-xs"
                                    data-historyid="<?php echo $row['agent_payment_history_id']; ?>" data-agentid="<?php echo $row['agent_id']; ?>"
                                    data-amount="<?php echo $row['amount']; ?>"
                                    data-agent_name="<?php echo $row['agent_name']; ?>">
                                        <?php echo lang('Decline'); ?>
                                    </a>
                                <?php }?>
                                </td>
                                <td><?php echo $row['created_on']; ?></td>
                                <td><a href="<?=site_url('agency_management/agent_information/' . $row['agent_id'])?>"><?=$row['agent_name']?></a></td>
                                <td><a href="<?=site_url('agency_management/agent_information/' . $row['agent_id'])?>"><?=lang($row['bank_name']) . ": " . $row['account_number']?></a></td>
                                <td><?=$row['amount']?></td>
                                <td><?=$row['processed_on']?></td>
                                <td><?=$row['adminuser']?></td>
                                <?php
                                if ($row['status'] == 1) {
                                    $status = lang('Request');
                                } else if ($row['status'] == 2) {
                                    $status = lang('Approved');
                                } else if ($row['status'] == 3) {
                                    $status = lang('Declined');
                                }?>
                                <td><?=$status?></td>
                                <td><?=($row['reason'] == null) ? '<i>n/a</i>' : $row['reason']?></td>
                            </tr>
                        <?php $totalAmt = (float)$totalAmt + (float)$row['amount'];} }?>
                    </tbody>
                    <?php if (!empty($payments)) { ?>
                        <tfoot>
                        <tr>
                            <th colspan="3"></th>
                            <th style="text-align: center"><?=lang('Total')?></th>
                            <th><span><?=number_format($totalAmt,2)?></span></th>
                            <th colspan="5"></th>
                        </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <!-- </form> -->
    <div class="panel-footer"></div>
</div>

<form action="<?=site_url('agency_management/approve_payment')?>" method="POST">
    <div class="modal fade" id="approve_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('aff.apay17');?>: <span class='payment_target'></span></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                    <label for="reason"><?=lang('Agent');?>:</label>
                    <span class="payment_target text-info"></span>
                    </div>
                    <div class="form-group">
                    <label for="reason"><?=lang('Amount');?>:</label>
                    <span class="payment_amount text-danger"></span>
                    </div>
                    <div class="form-group">
                    <label for="reason"><?=lang('Reason');?>:</label>
                    <input type="hidden" name="history_id">
                    <input type="hidden" name="agent_id">
                    <textarea name="reason" class="form-control" required="required" style="resize: none; height: 100px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <input type="submit" class="btn btn-primary" value="<?=lang('Save');?>">
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?=site_url('agency_management/decline_payment')?>" method="POST">
    <div class="modal fade" id="decline_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('aff.apay17');?>: <span class='payment_target'></span></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                    <label for="reason"><?=lang('Agent');?>:</label>
                    <span class="payment_target text-info"></span>
                    </div>
                    <div class="form-group">
                    <label for="reason"><?=lang('Amount');?>:</label>
                    <span class="payment_amount text-danger"></span>
                    </div>
                    <div class="form-group">
                    <label for="reason"><?=lang('Reason');?>:</label>
                    <input type="hidden" name="history_id">
                    <input type="hidden" name="agent_id">
                    <textarea name="reason" class="form-control" required="required" style="resize: none; height: 100px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <input type="submit" class="btn btn-primary" value="<?=lang('Save');?>">
                </div>
            </div>
        </div>
    </div>
</form>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<!-- end of display banner -->
<script type="text/javascript">

    function resetForm(){
        $('#search-form')[0].reset();
        $('#start_date').val("<?php echo $input['start_date']; ?>");
        $('#end_date').val("<?php echo $input['end_date']; ?>");
        $('#filterDate').val("<?php echo $input['start_date'].' to '.$input['end_date']; ?>");
    }

    $('#decline_dialog, #approve_dialog').on('show.bs.modal', function (e) {
        // console.log(e.relatedTarget);
        var historyid=$(e.relatedTarget).data('historyid');
        var agentid=$(e.relatedTarget).data('agentid');
        var agent_name=$(e.relatedTarget).data('agent_name');
        var amount=$(e.relatedTarget).data('amount');
        // console.log(this);
        $(this).find('.payment_target').html(agent_name);
        $(this).find('.payment_amount').html(amount);
        $(this).find('input[name=history_id]').val(historyid);
        $(this).find('input[name=agent_id]').val(agentid);
        $(this).find('textarea[name=reason]').val('');
    });


    $(document).ready(function(){
        $('#paymentTable').DataTable( {
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },

                <?php

                    if( $this->permissions->checkPermissions('export_agent_payment') ){

                ?>
                        {

                            text: "<?php echo lang('Excel Export'); ?>",
                            className:'btn btn-sm btn-primary',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/affiliatePayment'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/affiliatePayment'), d, function(data){
                                    // utils.safelog(data);

                                    //create iframe and set link
                                    if(data && data.success){
                                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                    }else{
                                        alert('export failed');
                                    }
                                });
                                <?php }?>
                            }
                        }
                <?php
                    }
                ?>


            ],
            "order": [ 1, 'desc' ]
        } );
    });
</script>
