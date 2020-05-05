<?php
/**
 *   filename:   view_structure_list.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for structure list in agency sub-system
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
                    <a data-toggle="collapse" id="collapse-search-form" href="#collapseStructureList"
                        class="btn btn-xs <?=$panelOpenOrNot?> <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>">
                    </a>
                </span>
                <a href="javascript:void(0);" class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>"
                    style="margin-right: 4px;">
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
                    <div class="col-md-4 col-lg-4">
                        <label class="control-label"><?=lang('Agent Template Name');?></label>
                        <input type="text" name="structure_name" class="form-control input-sm"
                        placeholder=' <?=lang('Enter Agent Template Name');?>'
                        value="<?php echo $conditions['structure_name']; ?>"/>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <?php include __DIR__.'/../includes/agency_form_currency.php'; ?>
                    </div>
                </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="button" value="<?=lang('lang.reset');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"
                        onclick="window.location.href='<?php echo site_url('agency_management/structure_list'); ?>'">
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" value="<?=lang('lang.search');?>" />
                    </div>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="button" id="add_new_template" value="<?=lang('Add New Agent Template');?>"
                        class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>"
                        onclick="window.location.href='<?php echo site_url('agency_management/create_structure'); ?>'" />
                    </div>
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
            <?=lang('Agent Template List');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- structure table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed"
                id="structure_list_table" style="width:100%;">
                <thead>
                    <tr>
                        <th><?=lang('Agent Template Name');?></th>
                        <th><?=lang('Currency');?></th>
                        <th><?=lang('Credit Limit');?></th>
                        <th><?=lang('Status');?></th>
                        <th><?=lang('Allowed Level');?></th>
                        <th><?=lang('Agent Level Names');?></th>
                        <th><?=lang('VIP Groups');?></th>
                        <th><?=lang('Settlement Period');?></th>
                        <th><?=lang('Action');?></th>
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
    var dataTable = $('#structure_list_table').DataTable({
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
                className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
            },
            <?php

                    if( $this->permissions->checkPermissions('export_agent_template_list') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>

                                var form_params=$('#search-form').serializeArray();

                                var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                                    'draw':1, 'length':-1, 'start':0};

                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/structure_list'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();

                                <?php }else{?>

                                $.post(site_url('/export_data/structure_list'), d, function(data){
                                    var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

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
            { className: 'text-right', targets: [ 4 ] },
            { sortable: false, targets: [ 8 ] },
        ],
        "order": [ 0, 'asc' ],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/structure_list", data, function(data) {
                callback(data);
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                    dataTable.buttons().disable();
                } else {
                    dataTable.buttons().enable();
                }
            },'json');
        },
    }); // DataTable settings }}}2

});
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_structure_list.php
