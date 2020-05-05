<form class="form-horizontal" id="search-form" method="get" role="form">

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?php echo lang("Duplicate Account Report"); ?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseDuplicateAccountReport" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <a href="javascript:void(0);" class="bookmark-this btn btn-info btn-xs pull-right" style="margin-right: 4px;"><i class="fa fa-bookmark"></i> <?php echo lang('Add to bookmark'); ?></a>
        </h4>
    </div>


    <div id="collapseDuplicateAccountReport" class="panel-collapse collapse <?=$this->config->item('default_open_search_panel') ? '' : 'in'?>">
        <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label"><?php echo lang('Username'); ?></label>
                        <input type="text" name="by_username" class="form-control input-sm" placeholder='<?php echo lang('Enter Username'); ?>'
                        value="<?php echo $conditions['by_username']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-lg-3" style="padding: 10px;">
                        <input type="button" value="<?php echo lang('Reset'); ?>" class="btn btn-danger btn-sm" onclick="window.location.href='<?php echo site_url('report_management/duplicate_account_report'); ?>'">
                        <input class="btn btn-sm btn-primary pull-right" type="submit" value="<?php echo lang('Search'); ?>" />
                    </div>
                </div>
        </div>
    </div>

</div>
</form>
        <!--end of Sort Information-->


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
                                <th><?=lang('player.01');?></th>
                                <th><?=lang('pay.realname');?></th>
                                <th><?=lang('pay.email');?></th>
                                <th><?=lang('pay.mobile');?></th>
                                <th><?=lang('pay.city');?></th>
                                <th><?=lang('player.10');?></th>
                                <th><?=lang('sys.totalRate');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!--end of result table -->
            <div class="panel-footer"></div>
        </div>

<script type="text/javascript">
    $(document).ready(function(){
        // $('#search-form').submit( function(e) {
        //     e.preventDefault();
        //     dataTable.ajax.reload();
        // });

        $('.bookmark-this').click(_pubutils.addBookmark);

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').trigger('submit');
            }
        });

        $('#report_table').DataTable({
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
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);
                        $.post(site_url('/export_data/duplicate_account_report'), d, function(data){
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
            "order": [ 6, 'desc' ],
            //processing: true,
            //serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/duplicate_account_report", data, function(data) {
                    callback(data);
                },'json');
            },
        });
    });
</script>
