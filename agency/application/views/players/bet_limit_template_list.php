<?php
/**
 *   filename:   list_players.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-08
 *   @brief:     view for players list in agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'collapse in';

// $this->utils->debug_log('panelDisplayMode', $panelDisplayMode);

if (isset($_GET['username'])) {
	$player_username = $_GET['username'];
} else {
	$player_username = '';
}
if (isset($_GET['search_on_date'])) {
	$search_on_date = $_GET['search_on_date'];
} else {
	$search_on_date = true;
}
?>
<div class="content-container">
    <!-- search form {{{1 -->
    <form class="form-horizontal" id="search-form">
        <input type="hidden" name="agent_id" value="<?php echo $agent_id?>"/>
        <div class="panel panel-primary">
            <!-- panel heading {{{2 -->
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i>
                    <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseAgentList"
                            class="btn btn-info btn-xs <?=$panelOpenOrNot?>">
                        </a>
                    </span>
                </h4>
            </div>
            <!-- panel heading }}}2 -->

            <div id="collapseAgentList" class="panel-collapse <?=$panelDisplayMode?>">
                <!-- panel body {{{2 -->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label"><?=lang('system.word33')?></label>
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
                        <div class="col-md-4 col-lg-4">
                            <label class="control-label"><?=lang('Template');?></label>
                            <input type="text" name="template_name" class="form-control"
                            placeholder=' <?=lang('Enter Template Name');?>' value="" />
                        </div>
                    </div>
                    <!-- button row {{{3 -->
                    <div class="row">
                        <div class="col-md-12 col-lg-12" style="padding: 10px;">
                            <input type="button" value="<?=lang('lang.reset');?>"
                            class="btn btn-default btn-sm" onclick="window.location.reload()">
                            <input class="btn btn-sm btn-primary" type="submit" value="<?=lang('lang.search');?>" />
                            <input id="add_template" type="button" value="<?=lang('Add Template');?>"
                            class="btn btn-info btn-sm" onclick="window.location.href='<?php echo site_url("/agency/add_bet_limit_template");?>'">
                        </div>
                    </div> <!-- button row }}}3 -->
                </div>
                <!-- panel body }}}2 -->
            </div>
        </div>
    </form> <!-- end of search form }}}1 -->

    <!-- panel for list {{{1 -->
    <div class="panel panel-primary">
        <!-- thead {{{2 -->
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <?=lang('Bet Limit Template List');?>
            </h4>
            <div class="clearfix"></div>
        </div>
        <!-- thead }}}2 -->

        <div class="panel panel-body table-responsive" id="agency_panel_body">
            <div class="col-md-12" id="view_payments" style="margin: 30px 0 0 0;">
                <!-- table {{{2 -->
                <table class="table table-striped table-hover" id="bet_limit_template_list_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="input-sm"><?=lang('Action');?></th>
                            <th class="input-sm"><?=lang('Updated At');?></th>
                            <th class="input-sm"><?=lang('Template');?></th>
                            <th class="input-sm"><?=lang('Note');?></th>
                            <th class="input-sm"><?=lang('Status');?></th>
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
$(function(){

    <?php if (isset($_GET['username'])) { ?>
        $('#date_from').val('');
        $('#date_to').val('');
    <?php } ?>
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

    $('#search-form input[type="text"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    // DataTable settings {{{2
    var dataTable = $('#bet_limit_template_list_table').DataTable({
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
        { sortable: false, targets: [ 0 ] },
        //{ visible: false, targets: [ 2 ] },
    ],
    "order": [ 1, 'asc' ],
    processing: true,
    serverSide: true,
    ajax: function (data, callback, settings) {
        data.extra_search = $('#search-form').serializeArray();
        $.post('<?php echo site_url("/api/bet_limit_template_list/".$agent_id); ?>', data, function(data) {
            callback(data);
            set_agent_operations();
        },'json')
        .fail( function (jqxhr, status_text)  {
            if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                if (confirm('<?= lang('session.timeout') ?>')) {
                    window.location.href = '/';
                }
            }
            else {
                alert(status_text);
            }
        });
    },
    }); // DataTable settings }}}2

    $('#search-form').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

});
</script>
<!-- JS code }}}1 -->
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of list_players.php
