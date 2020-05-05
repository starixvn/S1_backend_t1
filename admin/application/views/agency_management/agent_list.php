<?php
    /**
     *   filename:   view_agent_list.php
     *   author:     William Zhang
     *   e-mail:     william.dev@tripleonetech.net
     *   date:       2016-05-02
     *   @brief:     view for agent list in agency sub-system
     */

    // set display according to configurations
    $panelOpenOrNot   = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
    $panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'collapse in';

    $parent_id      = (isset($_GET['parent_id'])) ? $_GET['parent_id'] : '';
    $search_on_date = (isset($_GET['search_on_date'])) ? $_GET['search_on_date'] : false;

    $activate_url = site_url('agency_management/activate_agent_array');
    $freeze_url   = site_url('agency_management/freeze_agent_array');
    $suspend_url  = site_url('agency_management/suspend_agent_array');
?>

<form class="form-horizontal" id="search-form">
    <input type="hidden" name="parent_id" value="<?=$parent_id?>"/>
    <div class="panel panel-primary hidden">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseAgentList"
                        class="btn btn-xs <?=$panelOpenOrNot?> <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>">
                    </a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>"
                    style="margin-right: 4px;">
                    <i class="fa fa-bookmark"></i> <?=lang('Add to bookmark'); ?>
                </a>
            </h4>
        </div>

        <div id="collapseAgentList" class="panel-collapse <?=$panelDisplayMode?>">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
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
                    <div class="col-md-3">
                        <label class="control-label"><?=lang('Agent Username');?></label>
                        <input type="text" name="agent_name" class="form-control input-sm" value="<?php echo $conditions['agent_name']; ?>"/>
                    </div>
                    <div class="col-md-3">
                        <?php include __DIR__.'/../includes/agency_form_currency.php'; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-md-offset-10 text-right">
                        <a href="/agency_management/agent_list" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('lang.reset');?></a>
                        <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" id="search-button"><?=lang('lang.search');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?=lang('Agent List');?>
            <a class="btn btn-xs pull-right btn-info" href="/agency_management/create_agent">
                <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?=lang('Add Agent');?>
            </a>
        </h4>
    </div>
    <div class="panel-body">
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed" id="agent_list_table" style="width:100%;">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-group' : ''?>">
                            <input type='button' id="btn-activate" value='<?php echo lang('Activate Selected'); ?>'
                                class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" onclick="activate_selected_agents('<?=$activate_url?>')">
                            <input type='button' id="btn-suspend" value='<?php echo lang('Suspend Selected'); ?>'
                                class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>" onclick="suspend_selected_agents('<?=$suspend_url?>')">
                            <input type='button' id="btn-freeze" value='<?php echo lang('Freeze Selected'); ?>'
                                class='btn btn-danger btn-sm' onclick="freeze_selected_agents('<?=$freeze_url?>')">
                        </span>
                    </div>
                </div>
                <br><br>
                <thead>
                    <tr>
                        <?php include __DIR__.'/../includes/agency_agent_list_table_header.php'; ?>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="modal fade in" id="add_players_modal" tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
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
        </div>
        <div class="modal fade" id="view_agent_keys_modal" tabindex="-1" role="dialog" aria-labelledby="keyModalsTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><?=lang('View Keys')?></h5>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col"><?=lang('Secure Key')?></th>
                                    <th scope="col"><?=lang('Sign Key')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><?=lang('Staging')?></th>
                                    <td id="staging_secure_key"></td>
                                    <td id="staging_sign_key"></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?=lang('Live')?></th>
                                    <td id="live_secure_key"></td>
                                    <td id="live_sign_key"></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if($this->config->item('enable_gamegateway_api')) : ?>
                            <div class="text-center">
                                <button class="btn btn-primary text-center" data-toggle="modal" data-target="#confirmRegenerateModal"><?=lang('Regenerate Keys')?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('Close')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<div id="confirmRegenerateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?=lang('Generate Keys Confirmation')?></h4>
            </div>
            <div class="modal-body">
                <p><?=lang('Are you sure you want to regenerate keys for this agent?')?> <b><span id="agent_name_note"></span></b></p>
                <p class="help-block"><i><?=lang('Note regenerating keys will affect setup games. Possible that all games in this agent won\'t work.')?></i></p>
            </div>
            <input type="hidden" id="agent_id">
            <input type="hidden" id="agent_name">
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnRegenerateKey"><?=lang('Generate')?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('No')?></button>
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
    var baseUrl = '<?php echo base_url(); ?>';
    $(document).ready(function(){
        <?php if (isset($_GET['parent_id'])) { ?>
            $('#date_from').val('');
            $('#date_to').val('');
        <?php } ?>

        $('#search-form').submit( function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });

        $('.bookmark-this').click(_pubutils.addBookmark);

        $('#search-form input[type="text"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#btnRegenerateKey').on('click', function(){
            $(this).prop("disabled", true).html('Generating...');
            setTimeout(function(){
                $.get(baseUrl + 'agency_management/regenerate_keys/' + $('#agent_id').val() + '/' + $('#agent_name').val(),function(data){
                    $('#staging_secure_key').html(data.staging_secure_key);
                    $('#staging_sign_key').html(data.staging_sign_key);
                    $('#live_secure_key').html(data.live_secure_key);
                    $('#live_sign_key').html(data.live_sign_key);

                    $.notify('Success' ,{type: 'success'});
                    $('#confirmRegenerateModal, #view_agent_keys_modal').modal('hide');
                    $('#btnRegenerateKey').prop("disabled", false).html('Generate');
                }, "json");
            }, 600);
        });

        var dataTable = $('#agent_list_table').DataTable({
            autoWidth: false,
            searching: false,
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            responsive: { details: { type: 'column'} },
            buttons: [
                { extend: 'colvis', postfixButtons: [ 'colvisRestore' ], className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'}
                <?php if( $this->permissions->checkPermissions('export_agent_list') ){ ?>
                    ,{
                        text: "<?php echo lang('CSV Export'); ?>",
                        className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                        action: function ( e, dt, node, config ) {
                            var d;
                            <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                var form_params = $('#search-form').serializeArray();
                                d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type, 'draw':1, 'length':-1, 'start':0};
                                $("#_export_excel_queue_form").attr('action', site_url('/export_data/agent_list'));
                                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                $("#_export_excel_queue_form").submit();
                            <?php } else {?>
                                d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                $.post(site_url('/export_data/agent_list'), d, function(data){
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
                { className: 'text-right', targets: [ 3,4 ] },
                { sortable: false, targets: [ 0 ] },
                { sortable: false, targets: [ 9 ] },
            ],
            order: [ 1, 'asc' ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/agent_list", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    } else {
                        dataTable.buttons().enable();
                    }
                },'json');
            }
        });
    });

    function credit_transactions(agent_username) {
        var url = '/agency_management/credit_transactions?agent_name=' + agent_username;
        var win = window.open(url, '_blank');
        win.focus();
    }
</script>
