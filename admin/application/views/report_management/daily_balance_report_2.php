<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang('lang.search')?>
        </h4>
    </div>
    <div class="panel-collapse">
        <div class="panel-body">
        	<form  id="form-filter" method="GET" class="form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('Date')?> : </label>
                            <input class="form-control" id="date_filter" name="date_filter" value="<?= $conditions['date_filter']?>" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('Player Username')?> : </label>
                            <input type="text" name="username" id="username" class="form-control" value="<?=$conditions['username']?>" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('pay.totalbal')?> >= : </label>
                            <input type="text" name="total_balance" id="total_balance" class="form-control" value="<?=$conditions['total_balance']?>" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('exclude_player')?> : </label>
                            <select name="tag_list[]" id="tag_list" multiple="multiple" class="form-control">
                                <?php foreach ($tags as $key) {?>
                                    <option value="<?=$key['tagId']?>" <?=is_array($selected_tags) && in_array($key['tagId'], $selected_tags) ? "selected" : "" ?>><?=$key['tagName']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-sm btn-portage' : 'btn-primary'?>" id="button_submit"><?= lang('Search') ?></button>
                            <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-sm btn-linkwater' : 'btn-default'?>" id="button_reset"><?= lang('Reset') ?></button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-sm btn-burntsienna' : 'btn-warning'?>" id="button_rebuild" title="<?= lang('report.daily_bal.rebuild.title') ?>"><?= lang('report.daily_bal.rebuild') ?></button>
                        </div>
                    </div>
                </div>

        	</form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<?=lang('Daily Player Balance Report')?>
		</h4>
	</div>
	<div class="panel-body" style="overflow-x: scroll;">
        <div class="table-responsive">
            <table id="tblPlayerDailyBalance" class="table table-condensed table-bordered table-hover">
                <thead>
                    <tr>
                        <th  class="non-sortable-col" <?= !empty($game_platforms) ? "rowspan='2'" : "" ?>><?=lang('Date')?></th>
                        <th <?= !empty($game_platforms) ? "rowspan='2'" : "" ?>><?=lang('Player Username')?></th>
                        <th class="non-sortable-col balance-col" <?= !empty($game_platforms) ? "rowspan='2'" : "" ?>><?=lang('Total Balance')?></th>
                        <th class="non-sortable-col balance-col" <?= !empty($game_platforms) ? "rowspan='2'" : "" ?>><?=lang('Main Wallet Balance')?></th>
                        <?php if (!empty($game_platforms)): ?>
                            <th class="non-sortable-col balance-col text-center" colspan="<?=count($game_platforms)?>"><?=lang('pay.subwalltbal')?></th>
                        <?php endif; ?>
                    </tr>
                    <?php if (!empty($game_platforms)): ?>
                        <tr>
                            <?php foreach ($game_platforms as $game_platform): ?>
                                <th class="non-sortable-col balance-col text-center"><?=$game_platform['system_code']?></th>
                            <?php endforeach ?>
                        </tr>
                    <?php endif; ?>
                </thead>
            </table>
        </div>
	</div>
	<div class="panel-footer"></div>
</div>

<!-- Spinner modal -->
<div class="modal fade" id="modal_spinner" tabindex="-1" role="dialog" aria-labelledby="modal_spinner_label" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p><?= lang('report.daily_bal.please_wait') ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Complete modal -->
<div class="modal fade" id="modal_complete" tabindex="-1" role="dialog" aria-labelledby="modal_complete_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p class="mesg"><?= lang('report.daily_bal.rebuild_complete') ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('Close') ?></button>
      </div>
    </div>
  </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
    <form id="_export_csv_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' id = "json_csv_search" type="hidden">
</form>
<?php }?>

<script type="text/javascript">
    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

	$(function(){
        let notSortableCols = [];
        let balanceCols = [];
        let flagColIndex = $('#tblPlayerDailyBalance thead tr th').filter(function(index){
            if ($(this).hasClass('non-sortable-col')) {
                notSortableCols.push(index);
            }
            if ($(this).hasClass('balance-col')) {
                balanceCols.push(index);
            }
        }).index();

        notSortableCols.pop();
        balanceCols.pop();

        $('#date_filter').daterangepicker({
            singleDatePicker: true,
            startDate: "<?= $conditions['date_filter']?>",
            endDate: "<?= $conditions['date_filter']?>",
            maxDate: "<?=$this->utils->getYesterdayForMysql()?>",
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        let dataTable = $('#tblPlayerDailyBalance').DataTable({
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            ordering: true,
            iDisplayLength: 25,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                         var d = {'extra_search': $('#form-filter').serializeArray(), 'export_format': 'csv', 'export_type': export_type, 'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')): ?>
                                $("#_export_excel_queue_form").attr('action', site_url('/export_data/player_daily_balance'));
                                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                $("#_export_excel_queue_form").submit();
                        <?php else: ?>
                            $.post(site_url('/export_data/player_daily_balance'), d, function(data){
                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            }).fail(function(){
                                alert('export failed');
                            });
                        <?php endif; ?>
                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                { sortable: false, targets: notSortableCols },
                { className: 'text-right', targets: balanceCols },
            ],
            "order": [ 1, 'asc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                var formData = $('#form-filter').serializeArray();
                data.extra_search = formData;
                $.post(base_url + "api/dailyBalance", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
           }
        });
    });

    $(document).ready(function() {
        $("#daily_player_balance_report_2").addClass("active");

        $('#tag_list').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            buttonWidth: '90%',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Select Tags';
                }
                else {
                    var labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });

        /**
         * Reset button handler
         */
        $('#button_reset').click(function () {
            $('#date_filter').val(moment().subtract(1, 'days').format('YYYY-MM-DD'));
            $('input#username').val('');
            $('input#total_balance').val('10');
            $('#tag_list').multiselect('deselectAll');
            $('#tag_list').multiselect('refresh');
        });

        /**
         * Rebuild button handler
         * @see     function send_rebuild_request()
         */
        $('#button_rebuild').click(function () {
            var conf_mesg_tmpl = "<?= lang('report.daily_bal.rebuild.confirm') ?>";
            var rebuild_date = $('#date_filter').val();
            var conf_mesg = conf_mesg_tmpl.replace('{date}', rebuild_date);
            if (confirm(conf_mesg)) {
                send_rebuild_request(rebuild_date);
            }
        })
    });

    /**
     * Ajax method for rebuilding report for given date
     * @param   datestring  rebuild_date    The date for which to rebuild
     * @return  none
     */
    function send_rebuild_request(rebuild_date) {
        var args = { date: rebuild_date };

        $('#modal_spinner').modal( { keyboard: false, backdrop: 'static' } );

        var xhr = $.get(base_url + 'report_management/daily_player_balance_report_rebuild', args)
        .done (function (resp) {
            $('#modal_spinner').modal( 'hide' );
            $('#modal_complete').modal( );
            if (resp.success == true) {
                $('#button_submit').click();
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

