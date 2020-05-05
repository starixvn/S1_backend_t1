<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAffiliatePayment" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../../includes/report_tools.php" ?>
        </h4>
    </div>

    <div id="collapseAffiliatePayment" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form class="form-horizontal" action="<?=site_url('affiliate_management/paymentSearchPage'); ?>" id="search-form" method="get" role="form" name="myForm">
                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label"><?=lang('Date');?> </label>
                        <input type="text" class="form-control input-sm dateInput" id ="filterDate" data-start="#start_date" data-end="#end_date"/>
                        <input type="hidden" name="start_date" id="start_date" value="<?=(isset($input['start_date']) ? $input['start_date'] : '')?>">
                        <input type="hidden" name="end_date" id="end_date" value="<?=(isset($input['end_date']) ? $input['end_date'] : '')?>">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="control-label"><?=lang('lang.status');?>: </label>
                        <?=form_dropdown('status', $status_list, $input['status'], 'class="form-control input-sm"'); ?>
                    </div>
                    <div class="col-md-2">
                        <label for="username" class="control-label"><?=lang('aff.ap03');?>: </label>
                        <input type="text" name="username" class="form-control input-sm" value="<?=(isset($input['username']) ? $input['username'] : '')?>"/>
                    </div>
                    <div class="col-md-2" style="padding-top:23px;">
                        <input type="button" value="<?=lang('lang.reset');?>" onclick="resetForm()" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>">
                        <input type="submit" value="<?=lang('lang.search');?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary'?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- display banner -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt"><i class="glyphicon glyphicon-credit-card"></i> <?=lang('aff.apay09');?> </h4>
    </div>

    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12 col-md-offset-0 table-responsive" id="paymentList">
                <table class="table table-bordered table-hover dataTable" id="paymentTable">
                    <thead>
                        <tr>
                            <th class= "col-span-left"><?=lang('lang.action');?></th>
                            <th class= "col-span-left"><?=lang('Date');?></th>
                            <th class= "col-span-left"><?=lang('Affiliate Username');?></th>
                            <th class= "col-span-left"><?=lang('pay.acctname');?></th>
                            <th><?=lang('Amount');?></th>
                            <th class= "col-span-right"><?=lang('Processed Date');?></th>
                            <th class= "col-span-right"><?=lang('Processed By');?></th>
                            <th class= "col-span-right"><?=lang('lang.status');?></th>
                            <th class= "col-span-right"><?=lang('aff.apay11');?></th>
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
                                        data-historyid="<?= $row['affiliatePaymentHistoryId']; ?>" data-affid="<?= $row['affiliateId']; ?>"
                                        data-amount="<?= $row['amount']; ?>"
                                        data-username="<?= $row['username']; ?>">
                                            <?= lang('Approve'); ?>
                                        </a>

                                        <a href="#" data-toggle="modal" data-target="#decline_dialog" class="btn btn-danger btn-xs"
                                        data-historyid="<?= $row['affiliatePaymentHistoryId']; ?>" data-affid="<?= $row['affiliateId']; ?>"
                                        data-amount="<?= $row['amount']; ?>"
                                        data-username="<?= $row['username']; ?>">
                                            <?= lang('Decline'); ?>
                                        </a>
                                    <?php }?>
                                </td>
                                <td>
                                    <?= $row['createdOn']; ?>
                                </td>
                                <td>
                                    <a href="<?=site_url('affiliate_management/userInformation/' . $row['affiliateId'])?>"><?=$row['username']?></a>
                                </td>
                                <td>
                                    <a href="<?=site_url('affiliate_management/userInformation/' . $row['affiliateId'])?>"><?=lang($row['bankName']) . ": " . $row['accountNumber']?></a>
                                </td>
                                <td style="text-align: right" >
                                    <?=$row['amount']?>
                                </td>
                                <td>
                                    <?=$row['processedOn']?>
                                </td>
                                <td>
                                    <?=$row['adminuser']?>
                                </td>

                                <?php if ($row['status'] == 1) {
                                    $status = lang('Request');
                                } else if ($row['status'] == 2) {
                                    $status = lang('Approved');
                                } else if ($row['status'] == 3) {
                                    $status = lang('Declined');
                                } ?>
                                <td><?=$status?></td>
                                <td><?=($row['reason'] == null) ? '<i>n/a</i>' : $row['reason']?></td>
                            </tr>
                        <?php
                            $totalAmt = (float)$totalAmt + (float)$row['amount'];
                            }
                        }
                        ?>
                    </tbody>
                    <?php if (!empty($payments)) { ?>
                        <tfoot>
                            <tr>
                                <th colspan="4"></th>
                                <th>
                                    <span class="pull-left">
                                        <?=lang('Total')?>:</span> <span class="pull-right"><?=number_format($totalAmt,2)?>
                                    </span>
                                </th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<form action="<?=site_url('affiliate_management/approve_payment')?>" method="POST">
    <div class="modal fade" id="approve_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('aff.apay17');?>: <span class='payment_target'></span></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason"><?=lang('Affiliate');?>:</label>
                        <span class="payment_target text-info"></span>
                    </div>
                    <div class="form-group">
                        <label for="reason"><?=lang('Amount');?>:</label>
                        <span class="payment_amount text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="reason"><?=lang('Reason');?>:</label>
                        <input type="hidden" name="history_id">
                        <input type="hidden" name="affId">
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

<form action="<?=site_url('affiliate_management/decline_payment')?>" method="POST">
    <div class="modal fade" id="decline_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('aff.apay17');?>: <span class='payment_target'></span></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason"><?=lang('Affiliate');?>:</label>
                        <span class="payment_target text-info"></span>
                    </div>
                    <div class="form-group">
                        <label for="reason"><?=lang('Amount');?>:</label>
                        <span class="payment_amount text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="reason"><?=lang('Reason');?>:</label>
                        <input type="hidden" name="history_id">
                        <input type="hidden" name="affId">
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
        $('#start_date').val("<?=$input['start_date']; ?>");
        $('#end_date').val("<?=$input['end_date']; ?>");
        $('#filterDate').val("<?=$input['start_date'].' to '.$input['end_date']; ?>");
    }

    $('#decline_dialog, #approve_dialog').on('show.bs.modal', function (e) {
        var historyid=$(e.relatedTarget).data('historyid');
        var affid=$(e.relatedTarget).data('affid');
        var username=$(e.relatedTarget).data('username');
        var amount=$(e.relatedTarget).data('amount');
        $(this).find('.payment_target').html(username);
        $(this).find('.payment_amount').html(amount);
        $(this).find('input[name=history_id]').val(historyid);
        $(this).find('input[name=affId]').val(affid);
        $(this).find('textarea[name=reason]').val('');
    });


    $(document).ready(function(){
        $('#paymentTable').DataTable( {
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
                },
                <?php if( $this->permissions->checkPermissions('export_affiliate_payment') ){ ?>
                    {
                        text: "<?=lang('CSV Export'); ?>",
                        className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                        action: function ( e, dt, node, config ) {
                            var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                            $.post(site_url('/export_data/affiliatePayment'), d, function(data){
                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            });

                        }
                    }
                <?php } ?>
            ],
            columnDefs: [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            order: [ 2, 'desc' ],
            drawCallback: function () {
                var paymentTBL = $('#paymentTable');
                if ( paymentTBL.DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    paymentTBL.DataTable().buttons().disable();
                } else {
                    paymentTBL.DataTable().buttons().enable();
                }

                var count_colspan_left = $("#paymentTable thead tr .col-span-left").length;
                var count_colspan_right = $("#paymentTable thead tr .col-span-right").length;
                $("#paymentTable tfoot tr th").first().attr('colspan',count_colspan_left);
                $("#paymentTable tfoot tr th").last().attr('colspan',count_colspan_right);
            }
        });
    });
</script>