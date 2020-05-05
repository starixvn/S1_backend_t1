<?php
/**
 *   filename:   view_games_report.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view game reports in agency sub-system
 */

if (isset($_GET['search_on_date'])) {
    $search_on_date = $_GET['search_on_date'];
} else {
    $search_on_date = false;
}
?>
<style type="text/css">
    input, select{
        font-weight: bold;
    }
</style>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGamesReport" class="btn btn-default btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseGamesReport" class="panel-collapse collapse <?=$this->config->item('default_open_search_panel') ? '' : 'in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="GET">
                <input type="hidden" id="only_under_agency" name="only_under_agency" value="yes" />
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label"><?=lang('report.sum02')?></label>
                        <div class="input-group">
                            <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                            <input type="hidden" id="date_from" name="date_from"/>
                            <input type="hidden" id="date_to" name="date_to"/>
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_on_date" id="search_on_date" value="1"
                                <?php if ($search_on_date) {echo 'checked';} ?>/>
                            </span>
                        </div>
                    </div>
                    <!-- date selection {{{2
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('Date From');?></label>
                        <input type="text" name="date_from" class="form-control input-sm dateInput" value="<?=$conditions['date_from'];?>" />
                    </div>
                    <div class="col-md-1 col-lg-1">
                        <label class="control-label"><?=lang('Hour From');?></label>
                        <?php echo form_dropdown('hour_from', $this->utils->getHoursForSelect(), $conditions['hour_from'], 'class="form-control input-sm"'); ?>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label"><?=lang('Date To');?></label>
                        <input type="text" name="date_to" class="form-control input-sm dateInput" value="<?=$conditions['date_to'];?>" />
                    </div>
                    <div class="col-md-1 col-lg-1">
                        <label class="control-label"><?=lang('Hour To');?></label>
                        <?php echo form_dropdown('hour_to', $this->utils->getHoursForSelect(), $conditions['hour_to'], 'class="form-control input-sm"'); ?>
                    </div>
                        date selection }}}2  -->
                    <div class="col-md-3">
                        <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                        <select name="group_by" id="group_by" class="form-control input-sm">
                            <option value="game_platform_id" <?php echo $conditions["group_by"] == 'game_platform_id' ? "selected=selected" : ''; ?> ><?php echo lang('Game Platform'); ?></option>
                            <option value="game_type_id" <?php echo $conditions["group_by"] == 'game_type_id' ? "selected=selected" : ''; ?>><?php echo lang('Game Type'); ?></option>
                            <option value="game_description_id" <?php echo $conditions["group_by"] == 'game_description_id' ? "selected=selected" : ''; ?>><?php echo lang('Game'); ?></option>
                            <option value="player_id" <?php echo $conditions["group_by"] == 'player_id' ? "selected=selected" : ''; ?> ><?php echo lang('Player'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="total_bet_from"><?=lang('Username')?> </label>
                        <input type="text" name="username" id="username" class="form-control input-sm"
                        value='<?php echo $conditions["username"]; ?>'/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="total_bet_from"><?=lang('report.g09') . " <= "?> </label>
                        <input type="text" name="total_bet_from" id="total_bet_from" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_bet_from"]; ?>'/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="total_bet_to"><?=lang('report.g09') . " >= "?> </label>
                        <input type="text" name="total_bet_to" id="total_bet_to" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_bet_to"]; ?>'/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="total_loss_from"><?=lang('report.g11') . " <= "?> </label>
                        <input type="text" name="total_loss_from" id="total_loss_from" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_loss_from"]; ?>'/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="total_loss_to"><?=lang('report.g11') . " >= "?> </label>
                        <input type="text" name="total_loss_to" id="total_loss_to" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_loss_to"]; ?>'/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="total_gain_from"><?=lang('report.g10') . " <= "?> </label>
                        <input type="text" name="total_gain_from" id="total_gain_from" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_gain_from"]; ?>'/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="total_gain_to"><?=lang('report.g10') . " >= "?> </label>
                        <input type="text" name="total_gain_to" id="total_gain_to" class="form-control input-sm number_only"
                        value='<?php echo $conditions["total_gain_to"]; ?>'/>
                    </div>
                    <!--
                    <div class="col-md-3">
                        <label class="control-label" for="affiliate_username"><?=lang('Affiliate Username')?> </label>
                        <input type="text" name="affiliate_username" id="affiliate_username" class="form-control input-sm"
                        value='<?php echo $conditions["affiliate_username"]; ?>'/>
                    </div> -->
                    <div class="col-md-6">
                        <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                        <div class="input-group">
                            <input type="text" name="agent_name" id="agent_name" class="form-control input-sm"
                            value='<?php echo $conditions["agent_name"]; ?>'/>
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="include_all_downlines" value="true"/>
                                <?=lang('Include All Downline Agents')?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1" style="text-align:center;padding-top:24px;">
                    <input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn col-md-12 btn-info btn-sm">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?=lang('report.s07')?> </h4>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <?php include __DIR__.'/../includes/agency_games_report_table_header.php'; ?>
                </tr>
            </thead>
        </table>
    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var dataTable = $('#myTable').DataTable({
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            //dom: "<'panel-body'l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            buttons: [
{
    extend: 'colvis',
        postfixButtons: [ 'colvisRestore' ]
}
<?php if ($export_report_permission) {?>
,{
    text: "<?php echo lang('CSV Export'); ?>",
        className:'btn btn-sm btn-primary',
        action: function ( e, dt, node, config ) {
            var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
            // utils.safelog(d);
            $.post(site_url('/export_data/agency_game_reports'), d, function(data){
                // utils.safelog(data);

                //create iframe and set link
                if(data && data.success){
                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                }else{
                    alert('export failed');
                }
            }).fail(function(){
                alert('export failed');
            });
        }
}
<?php }
?>
],
columnDefs: [
                { className: 'text-right', targets: [ 4,5,6,7,8 ] }
                // { sortable: false, targets: [ 1 ] },
                //            {
                // targets: 3,
                // render: function ( data, type, full, meta ) {
                // if ($('#group_by').val() == 'total_player_game_day.game_description_id') {
                //            		dataTable.column( 1 ).visible( true );
                //            		dataTable.column( 1 ).order('asc');
                //            	} else {
                //            		dataTable.column( 1 ).visible( false );
                //            		dataTable.column( 2 ).order('asc');
                //            	}
                // return data;
                // return '<a href="/marketing_management/viewGameLogs?game_date_from='+$('#date_from').val()+'&game_date_to='+$('#date_to').val()+data;
                // }
                // }
            ],
            "order": [ 0, 'asc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/agency_game_reports", data, function(data) {
                    // if ($('#group_by').val() == 'total_player_game_day.game_description_id') {
                    // 	dataTable.column( 1 ).visible( true );
                    // 	dataTable.column( 1 ).order('asc');
                    // } else {
                    // 	dataTable.column( 1 ).visible( false );
                    // 	dataTable.column( 2 ).order('asc');
                    // }
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
            },
    });

    //  $('#form-filter').submit( function(e) {
    //     e.preventDefault();
    //     dataTable.ajax.reload();
    // });
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_games_report.php
