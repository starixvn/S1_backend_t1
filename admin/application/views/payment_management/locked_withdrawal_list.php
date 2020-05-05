<style> .iframe { border:0; width:0; height:0; } </style>

<form id="search-form">
    <input type="hidden" name="locked_user_id" value="1">
</form>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-lock"></i> <?=lang('Locked Withdrawal List')?>
        </h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive"  >
            <table class="table table-bordered table-hover" id="withdraw-table">
                <div class="btn-action" style="margin-top:14px;">
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-primary'?> btn-sm" id="unlockTransBtn">
                        <i class="fa fa-unlock" style="color:white;" title="Add"></i> <?= lang('Unlock Withdrawal'); ?>
                    </button>
                </div>
                <div class="clearfix"></div>
                <thead>
                <tr>
                    <th><?=lang('lang.action')?></th>
                    <th><?=lang('lang.status')?></th>
                    <th><?=lang("Withdraw Code")?></th>
                    <th><?=lang("Locked Status")?></th>
                    <th><?=lang("pay.username")?></th>
                    <th style="min-width:45px;"><?=lang("pay.reqtime")?></th>
                    <th style="min-width:45px;"><?=lang("pay.proctime")?></th>
                    <th style="min-width:45px;"><?=lang("pay.paidtime")?></th>
                    <th style="min-width:45px;"><?=lang("pay.spenttime")?></th>
                    <th><?=lang("pay.realname")?></th>
                    <th><?=lang('pay.playerlev')?></th>
                    <th><?=lang("Tag")?></th>
                    <th><?=lang('pay.withamt')?></th>
                    <th><?=lang('pay.bankname')?></th>
                    <th><?=lang('pay.acctname')?></th>
                    <th><?=lang('pay.acctnumber')?></th>
                    <th><?=lang('pay.payment_account_flag')?></th>
                    <th><?=lang('pay.acctbranch')?></th>
                    <th><?=lang('Withdrawal Declined Category')?></th>
                    <th><?=lang('Province')?></th>
                    <th><?=lang('City')?></th>
                    <th><?=lang('pay.withip')?></th>
                    <th><?=lang('pay.withlocation')?></th>
                    <th><?=lang('pay.procssby')?></th>
                    <th><?=lang('pay.updatedon')?></th>
                    <th><?=lang("pay.withdrawalId")?></th>
                    <th style="min-width:200px;"><?=lang('External Note');?></th>
                    <th style="min-width:200px;"><?=lang('Internal Note');?></th>
                    <th style="min-width:200px;"><?=lang('Action Log');?></th>
                    <th style="min-width:180px;"><?=lang('pay.timelog')?></th>
                    <th><?=lang('pay.curr')?></th>
                    <th><?=lang('sys.pay.systemcode')?></th>
                    <th><?=lang('lang.withdrawal_payment_api')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" style="margin-top:50px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= lang('Unlock Withdrawal') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <?= lang('Are you sure you want to unlock selected withdrawal transactions?'); ?>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn btn-default"><?= lang('lang.no'); ?></a>
                <a class="btn btn-primary" id="deleteBtn"><i class="fa"></i> <?= lang('lang.yes'); ?></a>
            </div>
        </div>
    </div>
</div>


<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script>
$(document).ready(function(){

    var base_url = '<?php echo base_url(); ?>';
    var checkedRows = [];

    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
    var $excelForm = $('#_export_excel_queue_form');

    $('#withdraw-table').DataTable({
        autoWidth: false,
        searching: false,
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        columnDefs: [
            { sortable: false, targets: [ 0 ] },
            { className: 'text-right', targets: [ 5 ] },
           // { "targets": [ 9, 18 ], "visible": false, "searchable": false }
        ],
        order: [[2, 'desc']],
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l>" +
             "<'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
            },
            <?php if( $this->permissions->checkPermissions('export_withdrawal_lists') ){ ?>
            {
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                action: function ( ) {
                    var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                    <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                        var form_params=$('#search-form').serializeArray();

                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};

                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/withdrawList/null/true'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                    <?php }else{?>

                    $.post(site_url('/export_data/withdrawList/null/true/true'), d, function(data){
                        if(data && data.success){
                            $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" class="frame"></iframe>');
                        }else{
                            alert('export failed');
                        }
                    });
                    <?php }?>
                }
            }
            <?php } ?>
        ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/lockedWithdrawList", data, function(data) {
                callback(data);
                if ( $('#withdraw-table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    $('#withdraw-table').DataTable().buttons().disable();
                }
                else {
                    $('#withdraw-table').DataTable().buttons().enable();
                }
            },'json');
        },
        drawCallback : function() {
            $("input[type='checkbox']").on('click', function(){
                var id = $(this).val();
                if( $(this).is(":checked") ) {
                    checkedRows.push(id);
                } else {
                    var index = checkedRows.indexOf(id);
                    if (index > -1) {
                        checkedRows.splice(index, 1);
                    }
                }
                console.log(checkedRows);
            });
        }
    });

    $('#unlockTransBtn').on('click', function() {
        if(checkedRows.length > 0) {
            $('#deleteModal').modal('show');
        } else {
            clearNotify();
            $.notify('<?= lang('No locked transaction selected!'); ?>',{type: 'danger'});
        }
    });

    $('#deleteBtn').on('click', function(){
        clearNotify();
        $(this).find('i').addClass('fa-refresh fa-spin');
        $.post(base_url + 'payment_management/batchUnlockWithdrawTransaction', {walletAccountId : checkedRows}, function(){
            successUnlocked();
        });
    });

    function clearNotify() {
        $.notifyClose('all');
    }

    function successUnlocked() {
        $('#deleteBtn').find('i').removeClass('fa-refresh fa-spin');
        $('#deleteModal').modal('hide');
        $.notify('<?= lang('Withdraw transaction successfully unlocked'); ?>',{type: 'success'});
        $('#withdraw-table').DataTable().ajax.reload(null,false);
        checkedRows = [];
    }
});
</script>