<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title" >
            <i class="fa fa-bank"></i> <?=lang('pay.bt.paneltitle');?>  -  <span style ="font-size: small"><?=lang('pay.19');?></span>
            <div class="pull-right">
                <a href="<?=site_url('payment_management/newBankType')?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-default'?>">
                    <i class="fa fa-plus"></i> <span class="hidden-xs"><?=lang('pay.bt.add.banktype')?></span>
                </a>
            </div>
        </h3>
    </div>
    <div class="panel-body">
      <div class="table-responsive"  >
        <table class="table table-bordered table-hover" id="my_table" >
            <thead>
                <tr>
                    <th><?=lang('lang.action')?></th>
                    <th><?=lang('column.id')?></th>
                    <th><?=lang('pay.bt.bankname')?></th>
                    <th><?=lang('Bank Code')?></th>
                    <th><?=lang('pay.bt.payment_api_id')?></th>
                    <th><?=lang('Bank/3rd Payment Type')?></th>
                    <th><?=lang('report.p07')?></th>
                    <th><?=lang('report.p06')?></th>
                    <th><?=lang('pay.bt.createdon')?></th>
                    <th><?=lang('pay.bt.updatedon')?></th>
                    <th><?=lang('pay.bt.createdby')?></th>
                    <th><?=lang('pay.bt.updatedby')?></th>
                    <th><?=lang('pay.bt.status')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bankTypes as $row): ?>
                    <?php
                        if( $row['bank_code'] == 'other' || $row['status'] == Banktype::STATUS_DELETE) continue;
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo site_url('payment_management/editBankType/' . $row['bankTypeId']); ?>"
                                title="<?=lang('lang.edit');?>"
                                class="edit-row" id="edit_row-<?=$row['bankTypeId']?>"
                            >
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </td>
                        <td><?=$row['bankTypeId']?></td>
                        <td><?=$row['bankName']?></td>
                        <td><?=$row['bank_code']?></td>
                        <td><?=$row['external_system_id'] ? : '<i class="text-muted">' . lang('lang.norecyet') . '</i>'?></td>
                        <td><?=$payment_type_flags[$row['payment_type_flag']]?></td>

                        <?php if($this->utils->getConfig('use_new_sbe_color')){?>
                            <td><a href="/payment_management/toggleBankType/enabled_withdrawal/<?=$row['bankTypeId']?>/<?=$row['enabled_withdrawal'] ? 0 : 1?>" class="btn btn btn-<?=$row['enabled_withdrawal'] ? 'scooter' : 'chestnutrose'?> btn-xs"><?=$row['enabled_withdrawal'] ? lang('status.normal') : lang('status.disabled')?></a></td>
                            <td><a href="/payment_management/toggleBankType/enabled_deposit/<?=$row['bankTypeId']?>/<?=$row['enabled_deposit'] ? 0 : 1?>" class="btn btn btn-<?=$row['enabled_deposit'] ? 'scooter' : 'chestnutrose'?> btn-xs"><?=$row['enabled_deposit'] ? lang('status.normal') : lang('status.disabled')?></a></td>
                        <?php }else{?>
                            <td><a href="/payment_management/toggleBankType/enabled_withdrawal/<?=$row['bankTypeId']?>/<?=$row['enabled_withdrawal'] ? 0 : 1?>" class="btn btn btn-<?=$row['enabled_withdrawal'] ? 'success' : 'danger'?> btn-xs"><?=$row['enabled_withdrawal'] ? lang('status.normal') : lang('status.disabled')?></a></td>
                            <td><a href="/payment_management/toggleBankType/enabled_deposit/<?=$row['bankTypeId']?>/<?=$row['enabled_deposit'] ? 0 : 1?>" class="btn btn btn-<?=$row['enabled_deposit'] ? 'success' : 'danger'?> btn-xs"><?=$row['enabled_deposit'] ? lang('status.normal') : lang('status.disabled')?></a></td>
                        <?php }?>

                        <td><?=$row['createdOn']?></td>
                        <td><?=$row['updatedOn']?></td>
                        <td><?=$row['createdByUsername']?></td>
                        <td><?=$row['updatedByUsername']?></td>
                        <td>
                            <?php if ($row['status'] == Banktype::STATUS_ACTIVE): ?>
                                <span class="text-success"><?=lang('lang.active')?></span>
                                <a href="/payment_management/toggleBanktypeStatus/<?=$row['bankTypeId']?>/<?=Banktype::STATUS_INACTIVE?>" class="pull-right" onclick="return confirm('<?=lang('sys.ga.conf.disable.msg')?>')" title="<?=lang('tool.cms01')?>"><i class="fa fa-lock"></i></a>
                            <?php else: ?>
                                <span class="text-danger"><?=lang('Blocked')?></span>
                                <a href="/payment_management/toggleBanktypeStatus/<?=$row['bankTypeId']?>/<?=Banktype::STATUS_ACTIVE?>" class="pull-right" onclick="return confirm('<?=lang('sys.ga.conf.able.msg')?>?')" title="<?=lang('tool.cms02')?>"><i class="fa fa-unlock"></i></a>
                            <?php endif?>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
   </div>
</div>
    <div class="panel-footer"></div>

</div>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
<script type="text/javascript">

    $(document).ready(function () {
        //submenu
        $('#collapseSubmenu').addClass('in');
        $('#view_payment_settings').addClass('active');
        $('#bank3rdPaymentList').addClass('active');


        $('#my_table').DataTable({
            autoWidth: false,
            searching: true,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },
                <?php

                    if( $this->permissions->checkPermissions('export_bank_payment') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/bankPaymentList'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/bankPaymentList'), d, function(data){
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
            columnDefs: [
                { visible: false, targets: [ 5 ] },
            ],
            "order": [[ 1, "asc" ]],
            drawCallback: function () {
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                    dataTable.buttons().disable();
                }
                else {
                    dataTable.buttons().enable();
                }
            }
        });

    }); //end document ready.

</script>