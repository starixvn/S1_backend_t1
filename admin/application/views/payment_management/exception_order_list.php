<style type="text/css">
</style>

<form action="<?php echo site_url('payment_management/exception_order_list'); ?>" id="search-form" method="GET">
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-exclamation-circle"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">
        <div class="row">
            <div class="col-md-12">
            <p>
                <?php echo lang('Only Support 3rd party: DaddyPay');?>
            </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-6">
                <label class="control-label"><?php echo lang('Date'); ?></label>
                <input id="search_cashback_date" class="form-control input-sm dateInput user-success" data-start="#by_date_from" data-end="#by_date_to" data-time="true" autocomplete="off">
                <input type="hidden" id="by_date_from" name="by_date_from" value="<?=$conditions['by_date_from'];?>">
                <input type="hidden" id="by_date_to" name="by_date_to" value="<?=$conditions['by_date_to'];?>">
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="control-label"><?php echo lang('Collection Bank Account Name'); ?></label>
                <input type="text" name="collection_bank_account_name" class="form-control input-sm"
                value="<?php echo $conditions['collection_bank_account_name']; ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <label class="control-label"><?php echo lang('Player Bank Account Name'); ?></label>
                <input type="text" name="player_bank_account_name" class="form-control input-sm"
                value="<?php echo $conditions['player_bank_account_name']; ?>"/>
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="control-label"><?php echo lang('Player Bank Account Number'); ?></label>
                <input type="text" name="player_bank_account_number" class="form-control input-sm"
                value="<?php echo $conditions['player_bank_account_number']; ?>"/>
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="control-label"><?php echo lang('Deposit Order'); ?></label>
                <input type="text" name="order_secure_id" class="form-control input-sm"
                value="<?php echo $conditions['order_secure_id']; ?>"/>
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="control-label"><?php echo lang('Withdrawal Order'); ?></label>
                <input type="text" name="withdrawal_order_id" class="form-control input-sm"
                value="<?php echo $conditions['withdrawal_order_id']; ?>"/>
            </div>
        </div>
        <div class="row">
            <div style="padding: 10px;">
                <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> pull-right" type="submit" value="<?php echo lang('Search'); ?>" />
            </div>
            <div class="col-md-6 col-lg-6" style="padding: 10px;">
            </div>
        </div>
	</div>

	</div>

</div>
</form>

<div class="panel panel-primary panel_list">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-exclamation-circle"></i> &nbsp;<?php echo $title; ?>
		</h4>
	</div>

	<div class="panel-collapse collapse in ">

	<div class="panel-body">
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed" id="report_table" style="width:100%;">
                <thead>
                    <tr>
                        <th><?php echo lang('Action'); ?></th>
                        <th><?php echo lang('Date'); ?></th>
                        <th><?php echo lang('Payment API'); ?></th>
                        <th><?php echo lang('Amount'); ?></th>
                        <th><?php echo lang('External Order'); ?></th>
                        <th><?php echo lang('Deposit Order'); ?></th>
                        <th><?php echo lang('Withdrawal Order'); ?></th>
                        <th><?php echo lang('Player Bank'); ?></th>
                        <th><?php echo lang('Player Bank Account Name'); ?></th>
                        <th><?php echo lang('Player Bank Account Number'); ?></th>
                        <th><?php echo lang('Player Bank Address'); ?></th>
                        <th><?php echo lang('Collection Bank'); ?></th>
                        <th><?php echo lang('Collection Bank Account Name'); ?></th>
                        <th><?php echo lang('Collection Bank Account Number'); ?></th>
                        <th><?php echo lang('Collection Bank Address'); ?></th>
                        <th><?php echo lang('Remarks'); ?></th>  
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
            </table>
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

	function showResponseContent(id){
		var content=$('#content_'+id).html();

       	BootstrapDialog.show({
       		title: '<?php echo lang("Response Content");?>',
            size: BootstrapDialog.SIZE_WIDE,
            message: content,
            buttons: [{
                label: '<?php echo lang("Close");?>',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });

	}

    $(document).ready(function(){

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#report_table').DataTable({
            autoWidth: false,
            searching: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
                }
                <?php if ($export_report_permission) {?>
                ,
                {

                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};


                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/exception_order_list/true'));
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();
                        <?php }else{?>

                        $.post(site_url('/export_data/exception_order_list/true'), d, function(data){
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
                <?php } ?>
            ],
            columnDefs: [
                // { sortable: false, targets: [  ] },
                { className: 'text-right', targets: [ 4 ] }
                // { visible: false, targets: [  ] }
            ],
            "order": [ 1, 'desc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/exception_order_list", data, function(data) {
                    callback(data);
                    if ( $('#report_table').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                        $('#report_table').DataTable().buttons().disable();
                    }
                    else {
                        $('#report_table').DataTable().buttons().enable();
                    }
                    // $('#total_amount').text(data.summary[0].total_amount);
                },'json');
            },
        });
    });

</script>