<?php
/**
 *   filename:   agent_tier_comm_pattern.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-11
 *   @brief:     view for agent tier commission pattern list in agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'in';
?>
<!-- search form {{{1 -->
<form class="form-horizontal" id="search-form">
    <div class="panel panel-primary hidden">
        <!-- panel heading {{{2 -->
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i>
                <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseStructureList"
                        class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$panelOpenOrNot?>">
                    </a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>" style="margin-right: 4px;">
                    <i class="fa fa-bookmark"></i>
                    <?php echo lang('Add to bookmark'); ?>
                </a>
            </h4>
        </div>
        <!-- panel heading }}}2 -->


        <div id="collapseStructureList" class="panel-collapse collapse <?=$panelDisplayMode?>">
            <!-- panel body {{{2 -->
            <div class="panel-body">
                <!-- input row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label"><?=lang('Pattern Name');?></label>
                        <input type="text" name="pattern_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Tier Commission Pattern Name');?>'
                        value="<?php echo $conditions['pattern_name']; ?>"/>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label"><?=lang('Tier Count');?></label>
                        <input type="number" name="tier_count" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Tier Count');?>'
                        value="<?php echo $conditions['tier_count']; ?>"/>
                    </div>
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="button" value="<?=lang('lang.reset');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"
                        onclick="window.location.href='<?php echo site_url($controller_name. '/tier_comm_patterns'); ?>'">
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                    </div>
                    <?php if ($this->permissions->checkPermissions('edit_tier_comm_pattern')): ?>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="button" value="<?=lang('Add New Pattern');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-success'?>"
                        onclick="window.location.href='<?php echo site_url($controller_name. '/add_new_pattern'); ?>'" />
                    </div>
                    <?php endif ?>
                </div> <!-- button row }}}3 -->
            </div>
            <!-- panel body }}}2 -->
        </div>
    </div>
</form> <!-- end of search form }}}1 -->

<!-- panel for structure table {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?=lang('Agent Tier Commission Patterns');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- structure table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed"
                id="pattern_list_table" style="width:100%;">
                <thead>
                    <tr>
                        <th><?=lang('Pattern Name');?></th>
                        <th><?=lang('Tier Count');?></th>
                        <th><?=lang('Calculation Method');?></th>
                        <th><?=lang('Rev Share');?></th>
                        <th><?=lang('Rolling Comm Basis');?></th>
                        <th><?=lang('Rolling Comm');?></th>
                        <th><?=lang('Min Active Players');?></th>
                        <th><?=lang('Min Bets(Main Wallet)');?></th>
                        <th><?=lang('Min Deposit');?></th>
                        <th><?=lang('Actions');?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end of structure table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for structure table }}}1 -->
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
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

    // DataTable settings {{{2
    var dataTable = $('#pattern_list_table').DataTable({
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
                postfixButtons: [ 'colvisRestore' ],
                className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"

            },
            <?php

                    if( $this->permissions->checkPermissions('export_tier_comm_pattern_list') ){

                ?>
                        {

                            text: "<?php echo lang('Excel Export'); ?>",
                            className:'btn btn-sm btn-primary',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/structure_list'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/structure_list'), d, function(data){
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
                <?php
                    }
                ?>
        ],
        columnDefs: [
            //{ className: 'text-right', targets: [ 4 ] },
            { sortable: false, targets: [ 8 ] },
        ],
        "order": [ 0, 'asc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/agent_tier_comm_pattern_list", data, function(data) {
                callback(data);
            },'json');
        },
    }); // DataTable settings }}}2

});
var controller_name = "<?=$controller_name?>";
function edit_pattern(pattern_id) {
    //if (confirm('Are you sure you want to edit this pattern?')) {
        window.location = base_url + controller_name + "/edit_pattern/" + pattern_id;
    //}
}
function remove_pattern(pattern_id) {
    if (confirm('Are you sure you want to delete this pattern?')) {
        window.location = base_url + controller_name + "/remove_pattern/" + pattern_id;
    }
}
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_tier_comm_pattern.php
