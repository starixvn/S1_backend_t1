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
                        <div class="col-md-offset-2 col-md-8">
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

                        <div class="col-md-offset-2 col-md-4">
                            <label class="control-label"><?=lang('Player Username');?></label>
                            <input type="text" name="player_username" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Player Username');?>' value="<?=$player_username?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="control-label"><?=lang('Parent Agent Username');?></label>
                            <input type="text" id="agent_name" name="agent_name" class="form-control input-sm"
                            placeholder=' <?=lang('Enter Agent Username');?>' />
                        </div>

                        <div class="col-md-offset-2 col-md-4" style="margin-top: 10px;">
                            <input type="button" value="<?=lang('lang.reset');?>"
                            class="btn btn-default btn-sm"
                            onclick="window.location.href='<?php echo site_url('agency/players_list'); ?>'">
                            <input class="btn btn-sm btn-primary" type="submit" value="<?=lang('lang.search');?>" />
                        </div>

                        <?php if ($this->utils->isEnabledFeature('enable_create_player_in_agency') && $this->agency_model->get_agent_by_id($this->session->userdata('agent_id'))['can_have_players']): ?>
                            <div class="col-md-4" style="margin-top: 10px;">
                                <input type="button" value="<?=lang('Add Players');?>"
                                class="btn btn-success btn-sm agent-oper" onclick="agent_add_players('<?=$agent_id?>')" />
                            </div>
                        <?php endif ?>

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
                <?=lang('traffic.playerlist');?>
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
                            <th class="input-sm"><?=lang('Action');?></th>
                            <th class="input-sm"><?=lang('Username');?></th>
                            <th class="input-sm"><?=lang('Parent Agent Username');?></th>
                            <!-- <th class="input-sm"><?=lang('Rolling Comm');?></th> -->
                            <th class="input-sm"><?=lang('player.ui21');?></th>
                            <th class="input-sm"><?=lang('Created On');?></th>
                            <th class="input-sm"><?=lang('player.04');?></th>
                            <th class="input-sm"><?=lang('player.05');?></th>
                            <th class="input-sm"><?=lang('player.10');?></th>
                        </tr>
                    </thead>
                </table>
                <!-- table }}}2 -->
            </div>
            <!--  modal for adding players for the agent {{{2 -->
            <div class="modal fade in" id="add_players_modal"
                tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="label_add_players_modal"></h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div> <!--  modal for level name setting }}}2 -->
        </div>
    </div>
    <!-- panel for list }}}1 -->
</div>

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
        <?php if (isSet($_GET['username'])) { ?>
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
    var dataTable = $('#players_list_table').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        "responsive": {
            details: {
                type: 'column'
            }
        },
        buttons: [
        {
            text: "<?=lang('Column visibility')?>",
            extend: 'colvis',
            postfixButtons: [ 'colvisRestore' ]
        }
        ],
        columnDefs: [
        { className: 'text-left', targets: [ 4 ] },
        { sortable: false, targets: [ 0 ] },
        { visible: false, targets: [ 5,6,7 ] }
    ],
    "order": [ 1, 'asc' ],
    processing: true,
    serverSide: true,
    ajax: function (data, callback, settings) {
        data.extra_search = $('#search-form').serializeArray();
        $.post(base_url + "api/players_list_under_agent", data, function(data) {
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
        var master_agent = '<?= $this->session->userdata("agent_name") ?>';
        var target_agent = $('#agent_name').val();

        // console.log('master', master_agent, 'target', target_agent);

        if (target_agent == '' || target_agent == master_agent) {
           dataTable.ajax.reload();
           return;
        }

        $.post(
            '/agency/agency_check_ancestry',
            { target_agent: target_agent }
        )
        .success(function (res) {
            if (res.success == false) {
                alert("<?= lang('error.default.message') ?>");
                return;
            }

            if (res.result == false) {
                alert(("<?= lang('agency.no_permission_for_agent') ?>").replace('%s', target_agent));
                return;
            }

            dataTable.ajax.reload();
        });
    });

});


function game_history(player_username) {
    var date_from = $('#date_from:enabled').val() || '';
    var date_to = $('#date_to:enabled').val() || '';
    var url = '/agency/game_history?player_username=' + player_username;
    var win = window.open(url, '_blank');
    win.focus();
}

function credit_transaction(player_username) {
    var date_from = $('#date_from:enabled').val() || '';
    var date_to = $('#date_to:enabled').val() || '';
    var url = '/agency/credit_transactions?player_username=' + player_username;
    var win = window.open(url, '_blank');
    win.focus();
}
</script>
<!-- JS code }}}1 -->
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of list_players.php
