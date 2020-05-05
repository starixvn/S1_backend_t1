<style media="screen" type="text/css">
.full-width {
    display:block;
}
</style>

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseManageCashbackRequest" class="btn btn-info btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseManageCashbackRequest" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
        <form id="search-form" class="form-horizontal" method="get">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label"><?=lang('Date')?></label>
                        <input type="text" class="form-control dateInput" data-start="#date_from" value="<?= $conditions['date_from'] ?>" />
                        <input type="hidden" name="date_from" id="date_from" value="<?= $conditions['date_from'] ?>" />
                        <!-- <input type="hidden" name="date_to" id="date_to"/> -->
                    </div>
                    <div class="col-md-3" style="margin-top: 29px;">
                         <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
                    </div>
                    <?php /*
                    <div class="col-md-2">
                        <label class="control-label" for="username"><?=lang('pay.username')?></label>
                        <input type="text" name="username" id="username" value="<?=($conditions['username']) ? $conditions['username']:''?>" class="form-control"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="status"><?=lang('xpj.cashback.status');?></label>
                        <select class="form-control input-sm" name="status" id="status">
                            <option value=""><?=lang('lang.selectall');?></option>
                            <option value="1">APPROVED</option>
                            <option value="2">DECLINED</option>
                            <option value="3">PENDING</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="request_amount_from"><?=lang('xpj.cashback.request_amount_from')?></label>
                        <input type="number" name="request_amount_from" id="request_amount_from" value="<?=($conditions['request_amount_from']) ? $conditions['request_amount_from']:''?>" class="form-control number_only"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="request_amount_to"><?=lang('xpj.cashback.request_amount_to')?></label>
                        <input type="number" name="request_amount_to" id="request_amount_to" value="<?=($conditions['request_amount_to']) ? $conditions['request_amount_to']:''?>" class="form-control number_only"/>
                    </div>
                    */ ?>
                </div>
            </div>
            <!-- <div class="panel-footer text-right">
                <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
            </div> -->
        </form>

    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<?=lang('OLE777 Wager Sync')?>
		</h4>
	</div>
	<div class="panel-body">



		<table class="table table-condensed table-bordered table-hover" id="daily_wager">
			<thead>
				<tr>
					<!-- <th><?=lang('#')?></th> -->
                    <th><?=lang('Date')?></th>
					<th><?=lang('Product')?></th>
                    <th><?=lang('Game Type')?></th>
					<th><?=lang('Wager Count')?></th>
					<th><?=lang('Bet Amount')?></th>
                    <th><?=lang('Effective Amount')?></th>
                    <th><?=lang('Win/Loss')?></th>
                    <th><?=lang('Action')?></th>
                    <th><?=lang('Status')?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<!--
<div class="modal fade bs-example-modal-md" id="cashbackRequestCancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?=lang('xpj.cashback.decline_msg');?>: </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="<?=site_url('marketing_management/declineCashbackRequest')?>">
							<?=lang('xpj.cashback.decline_reason');?>
                            <input type="hidden" name="declineCashbackRequestId" id="decline_cashback_request_id" class="form-control">
                            <textarea name="reasonToCancel" class="form-control reason_text" rows="7" required></textarea>
                            <br/>
                            <center>
                                <button class="btn btn-primary" style="width:30%"><?=lang('lang.submit');?></button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
-->



<script type="text/javascript">



    $(document).ready(function(){
        // Datatable initialization
        var dataTable = $('#daily_wager').DataTable( {

            columnDefs: [
                { targets: [ 2 ], className: 'text-left' } ,
                { targets: [ 3, 4, 5, 6 ], className: 'text-right' } ,
                { targets: [ 7, 8 ], className: 'text-center' }
            ],
            buttons : [
                {
                    text: "<?= lang("ole777_wager.check_all") ?>" ,
                    className:'btn btn-sm btn-success',
                    action: function (e, dt, node, config) {
                        console.log('Check_all');
                        conf_check_all();
                    }
                } ,
                {
                    text: "<?= lang("ole777_wager.rebuild") ?>" ,
                    className:'btn btn-sm btn-primary',
                    action: function (e, dt, node, config) {
                        console.log('Rebuild hit');
                        send_rebuild_request();
                    }
                }
            ] ,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            processing: true,
            serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();
				$.post(base_url + "api/ole777_wager_sync_list", data, function(data) {
					callback(data);
				},'json');
			},
        } );

        // Search button click handler
        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });

    /**
     * conf_queue: any clicked confirmation checkbox is first stored in the queue
     * then sent to backend every 500ms
     */
    var conf_queue = [];

    /**
     * Enqueue a single checkbox
     */
    function conf_enqueue(cbox) {
        conf_queue.push(cbox);
    }

    /**
     * 'Check all' button click handler
     * Checks all unchecked and unsynced checkboxes
     */
    function conf_check_all() {
        $('.sync_conf').not(':disabled').not(':checked').each(function() {
            console.log($(this).data('rowid'), $(this).data('dateprod'), $(this).data('syncdt'));
            $(this).click();
        });
    }

    /**
     * Sends confirm/clear (according checkbox check status) by ajax
     */
    function submit_sync_confirm(cbox) {
        var rowid = $(cbox).data('rowid');
        var dateprod = $(cbox).data('dateprod');
        var syncdt = $(cbox).data('syncdt');
        var checked = $(cbox).attr('checked');

        if (syncdt != '') {
            console.log('Row ' + dateprod + ' was synced, cannot withdraw its sync confirmation.');
        }

        $(cbox).prop('disabled', 1);
        $.post(
            '/marketing_management/ole777_wager_sync_confirm' ,
            { id: rowid }
        )
        .success(function(resp) {
            if (resp.success) {
                $(cbox).attr('checked', resp.result);
            }
            else {
                alert(resp.mesg);
                $(cbox).prop('checked', !checked);
                $(cbox).prop('disabled', 0);
            }
        })
        .error(function (resp) {
            alert('<?= lang("text.error") ?>');
            $(cbox).prop('checked', !checked);
            $(cbox).prop('disabled', 0);
        });

    }

    /**
     * conf_queue watcher, Running on 500ms interval
     */
    function conf_queue_watcher() {
        if (conf_queue.length > 0) {
            var sc_queue = conf_queue.slice();
            conf_queue = [];

            for (var i = 0; i < sc_queue.length; ++i) {
                submit_sync_confirm(sc_queue[i]);
            }
        }
    }

    setInterval(function() { conf_queue_watcher(); }, 500);

    /**
     * Rebuild button click event handler
     */
    function send_rebuild_request() {
        var args = { date: $('#date_from').val() }

        $('#modal_spinner').modal( { keyboard: false, backdrop: 'static' } );

        var xhr = $.get(base_url + "marketing_management/ole777_wager_rebuild", args)
        .done(function (resp) {
            $('#modal_spinner').modal( 'hide' );
            $('#modal_complete').modal( );
            if (resp.success == true) {
                $('#btn-submit').click();
            }
            else {
                alert(resp.mesg);
            }
        })
        .fail(function (xhr, status, errors) {
            $('#modal_spinner').modal( 'hide' );
            setTimeout(function() {
                alert("<?= lang('error.default.message') ?>");
            }, 300);
            console.log(status, xhr.status, JSON.stringify(errors));
        });
    }

</script>

<!-- Spinner model, shows up only when rebuilding wagers -->
<div class="modal fade" id="modal_spinner" tabindex="-1" role="dialog" aria-labelledby="modal_spinner_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p><?= lang('ole777_wager.please_wait') ?></p>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>

<!-- Complete model, shows up after rebuilding complete -->
<div class="modal fade" id="modal_complete" tabindex="-1" role="dialog" aria-labelledby="modal_complete_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
      <div class="modal-body">
        <p class="mesg"><?= lang('ole777_wager.rebuild_complete') ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('Close') ?></button>
      </div>
    </div>
  </div>
</div>