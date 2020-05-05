<form id="search-form">
    <input type="hidden" name="locked_user_id" value="1">
    <input type="hidden" name="dwStatus" value="requestAll">
</form>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-lock"></i> <?=lang('Locked Deposit List')?>
        </h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive"  >
            <table class="table table-bordered table-hover" id="deposit-table">
                <div class="btn-action" style="margin-top:14px;">
                    <button type="button" class="btn btn-emerald btn-sm" id="unlockTransBtn">
                        <i class="fa fa-unlock" style="color:white;" title="Add"></i> <?= lang('Unlock Deposit'); ?>
                    </button>
                </div>
                <div class="clearfix"></div>
                <thead>
                    <tr>
                        <th></th>
                        <th><?=lang('lang.status');?></th>
                        <th><?=lang('deposit_list.order_id');?></th>
                        <th><?=lang('system.word38');?></th>
                        <th><?=lang('pay.payment_account_flag');?></th>
                        <th><?=lang('pay.reqtime');?></th>
                        <th><?=lang('Deposit Datetime');?></th>
                        <th><?=lang('system.word39');?></th>
                        <th><?=lang("Affiliate")?></th>
                        <th><?=lang('pay.playerlev');?></th>
                        <th><?=lang('Deposit Amount');?></th>
                        <th><?=lang('pay.collection_name');?></th>
                        <th><?=lang('pay.ip') . ' ' . lang('pay.locatn');?></th>
                        <th><?=lang('pay.updatedon');?></th>
                        <th><?=lang('cms.timeoutAt');?></th>
                        <th><?=lang('pay.procsson');?></th>
                        <th><?=lang('pay.collection_account_name');?></th>
                        <th><?=lang('con.bnk20');?></th>
                        <th><?=lang('pay.deposit_payment_name');?></th>
                        <th><?=lang('pay.deposit_payment_account_name');?></th>
                        <th><?=lang('pay.deposit_payment_account_number');?></th>
                        <th><?=lang('pay.deposit_transaction_code');?></th>
                        <th><?=lang('pay.promoname');?></th>
                        <th><?=lang('pay.promobonus');?></th>
                        <th><?=lang('External ID'); ?></th>
                        <th><?=lang('Bank Order ID'); ?></th>
                        <th><?=lang('Deposit Datetime From Player'); ?></th>
                        <th><?=lang('Mode of Deposit'); ?></th>
                        <th style="min-width:200px;"><?=lang('Player Deposit Note');?></th>
                        <th style="min-width:400px;"><?=lang('pay.procssby');?></th>
                        <th style="min-width:400px;"><?=lang('External Note');?></th>
                        <th style="min-width:400px;"><?=lang('Internal Note');?></th>
                        <th style="min-width:600px;"><?=lang('Action Log');?></th>
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
                <h4 class="modal-title"><?= lang('Unlock Deposit') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <?= lang('Are you sure you want to unlock selected deposit transactions?'); ?>
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

<script type="text/javascript">
    <?php if(!empty($this->utils->getConfig('deposit_list_columnDefs'))) : ?>
		<?php if(!empty($this->utils->getConfig('deposit_list_columnDefs')['not_visible_payment_management'])) : ?>
			var visible_target = JSON.parse("<?=json_encode($this->utils->getConfig('deposit_list_columnDefs')['not_visible_payment_management']) ?>" ) ;
		<?php else: ?>
			var visible_target = [ 6,7,10, 12, 13, 14, 16, 17, 19, 20, 21, 22, 23 ];
		<?php endif; ?>
		<?php if(!empty($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_payment_management'])) : ?>
			var className_text = JSON.parse("<?= json_encode($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_payment_management']) ?>" ) ;
		<?php else: ?>
			var className_text = [ 5 ];
		<?php endif; ?>
	<?php else: ?>
		var visible_target = [ 6, 7, 10, 12, 13, 14, 16, 17, 19, 20, 21, 22, 23 ];
		var className_text = [ 5 ];
	<?php endif; ?>

    var checkedRows = [];
    var export_type = "<?=$this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
    var $excelForm  = $('#_export_excel_queue_form');

    $(document).ready( function() {
        var params = {
            'extra_search'  : $('#search-form').serializeArray(),
            'draw'          : 1,
            'length'        : -1,
            'start'         : 0
        };

        $('#deposit-table').DataTable({
            autoWidth: false,
            searching: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l>" +
                 "<'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p>" +
                 "<'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: ['colvisRestore'],
                    className: 'btn-linkwater'
                },
                <?php if($this->permissions->checkPermissions('export_deposit_lists')): ?>
                    {
                        text: "<?=lang('CSV Export'); ?>",
                        className: 'btn btn-sm btn-portage',
                        action: function (e, dt, node, config) {
                            <?php if($this->utils->isEnabledFeature('export_excel_on_queue')): ?>
                                var form_params=$('#search-form').serializeArray();
                                var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                                    'draw':1, 'length':-1, 'start':0};

                                $("#_export_excel_queue_form").attr('action', site_url('/export_data/depositList/null/true'));
                                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                $("#_export_excel_queue_form").submit();
                            <?php else: ?>
                                $.post(site_url('/export_data/depositList/null/true'), params, function (data) {
                                    if (data && data.success) {
                                        $('body').append('<iframe src="' + data.link + '" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                    } else {
                                        alert('export failed');
                                    }
                                });
                            <?php endif; ?>
                        }
                    }
                <?php endif; ?>
            ],
            columnDefs: [
				{ sortable: false, targets: [ 0 ] },
				{ visible: false, targets: visible_target },
				{ className: 'text-right', targets: className_text },
				<?php if (!$this->utils->isEnabledFeature('enable_deposit_datetime')) { ?>
                    { targets: [ 26 ], className: "noVis hidden" },
                    { targets: [ 27 ], className: "noVis hidden" },
                <?php } ?>
				<?php if($this->utils->isEnabledFeature('close_aff_and_agent')): ?>
					{ targets: [ 8 ], className: "noVis hidden" },
				<?php endif?>
			],
			order: [[ <?=$defaultSortColumn;?>, 'desc']],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post("/api/lockedDepositList/", data, function (data) {
                    callback(data);
                    if( $('#deposit-table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#deposit-table').DataTable().buttons().disable();
                    }
                    else {
                        $('#deposit-table').DataTable().buttons().enable();
                    }
                }, 'json');
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
        $.post('/payment_management/batchUnlockTransaction', {saleOrdersId : checkedRows}, function(){
            successUnlocked();
        });
    });

    function clearNotify() {
        $.notifyClose('all');
    }

    function successUnlocked() {
        $('#deleteBtn').find('i').removeClass('fa-refresh fa-spin');
        $('#deleteModal').modal('hide');
        $.notify('<?= lang('Deposit transaction successfully unlocked'); ?>',{type: 'success'});
        $('#deposit-table').DataTable().ajax.reload(null,false);
        checkedRows = [];
    }
</script>

