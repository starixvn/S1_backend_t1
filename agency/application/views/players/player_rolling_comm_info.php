<?php
/**
 *   filename:   list_players.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-08
 *   @brief:     view for players list in agency sub-system
 */

?>
<div class="container">
    <form class="form-horizontal" id="search-form">
        <input type="hidden" name="settlement_id" value="<?php echo $settlement_id?>"/>
    </form>
    <!-- panel for list {{{1 -->
    <div class="panel panel-primary">
        <!-- thead {{{2 -->
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <?=lang('Player Rolling Comm Info');?>
            </h4>
            <div class="clearfix"></div>
        </div>
        <!-- thead }}}2 -->

        <div class="panel panel-body table-responsive" id="agency_panel_body">
            <div class="col-md-12" id="view_payments" style="margin: 30px 0 0 0;">
                <!-- table {{{2 -->
                <table class="table table-striped table-hover" id="players_list_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="input-sm"><?=lang('Player Username');?></th>
                            <th class="input-sm"><?=lang('Settlement Period');?></th>
                            <th class="input-sm"><?=lang('Date Range');?></th>
                            <th class="input-sm"><?=lang('Total Bet');?></th>
                            <th class="input-sm"><?=lang('Rolling Comm');?></th>
                            <th class="input-sm"><?=lang('Rolling Comm Amt');?></th>
                            <th class="input-sm"><?=lang('Rolling Comm Payment Status');?></th>
                        </tr>
                    </thead>
                </table>
                <!-- table }}}2 -->
            </div>
        </div>
    </div>
    <!-- panel for list }}}1 -->
</div>

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){

    // DataTable settings {{{2
    var dataTable = $('#players_list_table').DataTable({
        autoWidth: false,
            searching: false,
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
                postfixButtons: [ 'colvisRestore' ]
        }
    ],
        columnDefs: [
        { className: 'text-left', targets: [ 4 ] },
        //{ sortable: false, targets: [ 0 ] },
        //{ visible: false, targets: [ 2 ] },
    ],
    "order": [ 0, 'asc' ],
    processing: true,
    serverSide: true,
    ajax: function (data, callback, settings) {
        data.extra_search = $('#search-form').serializeArray();
        $.post(base_url + "api/player_rolling_comm_info", data, function(data) {
            callback(data);
        },'json');
    },
    }); // DataTable settings }}}2

    dataTable.ajax.reload();
});
</script>
<!-- JS code }}}1 -->
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of list_players.php
