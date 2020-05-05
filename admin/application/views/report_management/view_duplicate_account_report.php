<!-- search form {{{1 -->
<form class="form-horizontal" id="search-form" method="get" role="form">
    <div class="panel panel-primary hidden">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?php echo lang("Duplicate Account Report"); ?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapseDuplicateAccountReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
                </span>
            </h4>
        </div>

        <div id="collapseDuplicateAccountReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Player Username'); ?></label>
                        <input type="text" id="by_username" name="by_username"
                        class="form-control input-sm" placeholder='<?php echo lang('Enter Username'); ?>'
                        value="<?php echo $conditions['by_username']; ?>"/>
                    </div>
                    <div class="col-md-2 col-lg-2" style="padding: 20px;padding-top: 25;">
                        <input type="reset" value="<?php echo lang('Reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-danger' : 'btn-default'?>">
                        <input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" name="submit" value="<?php echo lang('Search'); ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- search form }}}1 -->

<!-- table info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt"><i class="icon-bullhorn"></i> <?php echo lang('Report Result'); ?></h4>
    </div>
    <div class="panel-body">
        <!-- result table -->
        <div id="logList" >
            <table class="table table-striped table-hover table-condensed" id="report_table" style="width:100%;">
                <thead>
                    <tr>
                        <!--
                        <th><?=lang('player.01');?></th>
                        <th><?=lang('pay.realname');?></th>
                        <th><?=lang('pay.email');?></th>
                        <th><?=lang('pay.mobile');?></th>
                        <th><?=lang('pay.city');?></th>
                        <th><?=lang('player.10');?></th>
                        <th><?=lang('sys.totalRate');?></th>
                        -->
                        <th><?= lang('player.01')     ?></th>
                        <th><?= lang('sys.totalRate') ?></th>
                        <th><?= lang('Last Updated On') ?></th>
                        <!-- <th><?= lang('Player Id') ?></th> -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--end of result table -->
    <div class="panel-footer"></div>
</div>
<!-- table info }}}1 -->

<script type="text/javascript">
$(document).ready(function(){

    $('.bookmark-this').click(_pubutils.addBookmark);

    $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
        if (e.which == 13) {
            $('#search-form').trigger('submit');
        }
    });

    var dataTable = $('#report_table').DataTable({
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        autoWidth: false,
        searching: true,
        processing: true,
        aserverSide: true,
        pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
        order: [ 1, 'desc' ],
        responsive: {
            details: {
                type: 'column'
            }
        },
        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();

            $.post(base_url + "api/duplicate_account_total", data, function(data) {
                callback(data);
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                $('#search-form').find(':submit').prop('disabled', false);
            },'json');
        },
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
            }
            /*<?php if ($export_report_permission) {?>
            ,{
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                action: function ( e, dt, node, config ) {
                    var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                    // utils.safelog(d);
                    $.post(site_url('/export_data/duplicate_account_report'), d, function(data){
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
            <?php }?>*/
        ],
    });

    $('#search-form').submit( function(e) {
        $(this).find(':submit').prop('disabled', true);
        e.preventDefault();
        dataTable.ajax.reload();
    });
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_duplicate_account_report.php
