<div class="content-container">
    <div class="panel panel-primary hidden">

        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
                </span>
            </h4>
        </div>

        <div id="collapsePlayerReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
            <div class="panel-body">
                <form id="form-filter" class="form-horizontal" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from" value="<?=$conditions['date_from']?>"/>
                                <input type="hidden" id="date_to" name="date_to" value="<?=$conditions['date_to']?>"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_on_date" id="search_on_date" value="true"
                                    <?php if ( $conditions['search_on_date']) {echo 'checked';} ?>/>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="status"><?=lang('Status')?> </label>
                            <div class="input-group">
                                <select class="form-control" name="status" id="status">
                                    <option value="" ><?php echo lang('All');?></option>
                                    <option value="current" <?php echo $conditions['status']=='current' ? 'selected' : '' ;?>><?php echo lang('Current');?></option>
                                    <option value="settled" <?php echo $conditions['status']=='settled' ? 'selected' : '' ;?>><?php echo lang('Settled');?></option>
                                    <option value="pending" <?php echo $conditions['status']=='pending' ? 'selected' : '' ;?>><?php echo lang('Pending');?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group">
                            <label class="control-label" for="include_all_downlines"><?=lang('Include All Downline Agents')?></label>
                            <input type="checkbox" class="form-control"  name="include_all_downlines" value="true"
                                    <?php if ( $conditions['include_all_downlines']) {echo 'checked';} ?> >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="sub_agent_username"><?=lang('Agent Username')?> </label>
                            <div class="input-group">
                                <input type="text" name="sub_agent_username" id="sub_agent_username" class="form-control" value="<?=$conditions['sub_agent_username']?>"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="player_username"><?=lang('Player Username')?> </label>
                            <div class="input-group">
                                <input type="text" name="player_username" id="player_username" class="form-control" value="<?=$conditions['player_username']?>"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                        </div>

                    </div>
                    <div class="col-md-4 col-lg-4 pull-right" style="padding-top: 10px; padding-right:0px;">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary btn-sm pull-right">
                    </div>
                    <div class="col-md-4" style="padding-top: 10px; padding-left:0px;">
                        <input type="button" value="<?=lang('Export CSV')?>"
                        class="btn btn-success btn-sm agent-oper export_csv">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="icon-users"></i> <?=lang('Player Settlement')?> </h4>
        </div>
        <div class="panel-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th><?=lang('Player')?></th>
                            <th><?=lang('Status')?></th>
                            <th><?=lang('Date From')?></th>
                            <th><?=lang('Date To')?></th>
                            <th><?=lang('Real Bet')?></th>
                            <th><?=lang('Available Bet')?></th>
                            <th><?=lang('Rolling Rate')?></th>
                            <th><?=lang('Amount')?></th>
                            <th><?=lang('Rolling Comm Income')?></th>
                            <th><?=lang('Agent Earnings')?></th>
                            <th><?=lang('Remarks')?></th>
                            <th><?=lang('Action')?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?=lang('Sub Total')?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-right"><span id="sub_total_real_bet">0.0</span></th>
                            <th class="text-right"><span id="sub_total_avail_bet">0.0</span></th>
                            <th></th>
                            <th class="text-right"><span id="sub_total_amount">0.0</span></th>
                            <th class="text-right"><span id="sub_rolling_comm_income">0.0</span></th>
                            <th class="text-right"><span id="sub_agent_earning_amount">0.0</span></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th><?=lang('Total')?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-right"><span id="total_real_bet">0.0</span></th>
                            <th class="text-right"><span id="total_avail_bet">0.0</span></th>
                            <th></th>
                            <th class="text-right"><span id="total_amount">0.0</span></th>
                            <th class="text-right"><span id="rolling_comm_income">0.0</span></th>
                            <th class="text-right"><span id="agent_earning_amount">0.0</span></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        <div class="panel-footer"></div>
    </div>
</div>

<div class="modal fade in" id="change_rolling_modal"
    tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="label_change_rolling_modal"></h4>
            </div>
            <div class="modal-body">
                <div id="change_description"></div>
                <form class="form">
                <div class="form-group">
                <label class="control-label"><?php echo lang('Remarks');?></label>
                <textarea id="rolling_notes" class="form-control"></textarea>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="rolling_id">
                <input type="hidden" id="change_type">
                <input type="button" class="btn btn-danger btn-sm" data-dismiss="modal" value="<?php echo lang('Cancel');?>">
                <input type="button" class="btn btn-primary btn-sm" id="save_change_rolling" value="<?php echo lang('Save');?>"
                    onclick="change_rolling($('#rolling_id').val(), $('#change_type').val(), $('#rolling_notes').val())">
            </div>
        </div>
    </div>
</div> <!--  modal for level name setting }}}2 -->

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">

function change_rolling(id, type, notes){
    if(type=='settle'){
        $('#save_change_rolling').prop('disabled', true);
        //call settle
        $.post('<?php echo site_url('/agency/settle_rolling'); ?>', {"rolling_id": id, 'notes': notes}, function(data){
            alert(data['message']);
            //refresh
            window.location.reload();
            $('#save_change_rolling').prop('disabled', true);
        }).fail(function() {
            alert( "<?php echo lang('Submit failed');?>" );
            $('#save_change_rolling').prop('disabled', false);
        });

    }else if(type=='pending'){
        $('#save_change_rolling').prop('disabled', true);
        $.post('<?php echo site_url('/agency/pending_rolling'); ?>', {"rolling_id": id, 'notes': notes}, function(data){
            alert(data['message']);
            //refresh
            window.location.reload();
        }).fail(function() {
            alert( "<?php echo lang('Submit failed');?>" );
            $('#save_change_rolling').prop('disabled', false);
        });
    }
}

function settle_rolling(id, amount, username){
    $('#rolling_id').val(id);
    $('#change_type').val('settle');
    $('#change_description').html('<?php echo lang('Settle');?> <?php echo lang('Amount');?>:'
        +amount+', <?php echo lang('Player');?>: '+username);
    $('#label_change_rolling_modal').html('<?php echo lang('Settle');?>');
    $('#change_rolling_modal').modal('show');
}

function pending_rolling(id, amount, username){
    $('#rolling_id').val(id);
    $('#change_type').val('pending');
    $('#change_description').html('<?php echo lang('Pending');?> <?php echo lang('Amount');?>: '
        +amount+', <?php echo lang('Player');?>: '+username);
    $('#label_change_rolling_modal').html('<?php echo lang('Pending');?>');
    $('#change_rolling_modal').modal('show');
}
$(document).ready(function(){
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

    var dataTable = $('#myTable').DataTable({
            lengthMenu: [50, 100, 250, 500, 1000],

        autoWidth: false,
            searching: false,
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                        //{ className: 'text-right', targets: [ 11,12,13,14,15,16,17,18,19, ] },
                        //{ visible: false, targets: [ 1,3,4,6,7,8,9,10,11, ] },
            ],
            buttons: [
                {
                    extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                }
        ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/player_rolling_comm", data, function(data) {

                    if (data.sub_summary && data.sub_summary.length > 0) {
                        $('#sub_total_real_bet').text(data.sub_summary[0].sub_total_real_bet);
                        $('#sub_total_avail_bet').text(data.sub_summary[0].sub_total_avail_bet);
                        $('#sub_total_amount').text(data.sub_summary[0].sub_total_amount);
                    }

                    if (data.summary && data.summary.length > 0) {
                        $('#total_real_bet').text(data.summary[0].total_real_bet);
                        $('#total_avail_bet').text(data.summary[0].total_avail_bet);
                        $('#total_amount').text(data.summary[0].total_amount);
                    }

                    callback(data);
                }, 'json');
            },
    });

    $('#form-filter').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

    $('#group_by').change(function() {
        var value = $(this).val();
        if (value == 'player.playerId') {
            $('#username').val('').prop('disabled', false);
        } else {
            $('#username').val('').prop('disabled', true);
        }
    });

    $('.export_csv').click(function(){

        if (agent_suspended) {
            return false;
        }
        // utils.safelog(dataTable.columns());

        var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0,
            'export_format': 'csv', 'export_type': export_type};
        var export_url = "<?php echo site_url('export_data/player_rolling_comm') ?>";

        $("#_export_excel_queue_form").attr('action', export_url);
        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
        $("#_export_excel_queue_form").submit();

        // utils.safelog(d);
        //$.post(site_url('/export_data/player_reports'), d, function(data){
        // $.post(export_url, d, function(data){
        //    // utils.safelog(data);

            //create iframe and set link
        //     if(data && data.success){
        //         $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
        //     }else{
        //         alert('export failed');
        //     }
        // });
    });


});
</script>
