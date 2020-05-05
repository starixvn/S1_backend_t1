<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseUserLogsReport" class="btn btn-info btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseUserLogsReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="search-form" class="form-horizontal">
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label"><?=lang("lang.date")?></label>
                        <input type="text" class="form-control input-sm dateInput" data-start="#log_date_from" data-end="#log_date_to" data-time="true"/>
                        <input type="hidden" name="log_date_from" id="log_date_from"/>
                        <input type="hidden" name="log_date_to" id="log_date_to"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('report.log03');?></label>
                        <select name="management" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <?php foreach ($managements as $management): ?>
                                <option value="<?=$management?>"><?=$management?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('report.log04');?></label>
                        <select name="userRole" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?=$role['roleName']?>"><?=$role['roleName']?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('report.log02');?></label>
                        <input type="text" name="username" class="form-control input-sm" value="<?= ($this->input->get('username') ? : '')?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('Data');?></label>
                        <select name="extra" class="form-control input-sm">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <option value="1"><?=lang('Filter Empty Data')?></option>
                            <option value="2"><?=lang('Only Empty Data')?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label"><?=lang('lang.action');?></label>
                        <input type="text" name="action" class="form-control input-sm"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1 col-md-offset-11" style="padding-top: 20px;">
                        <button type="button" class="btn btn-primary pull-right" id="btn-submit"><i class="fa fa-search"></i> <?=lang("lang.search")?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-profile"></i><?=lang('user.logs');?> </h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-hover table-condensed table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th><?=lang('report.log05');?></th>
                        <th><?=lang('report.log02');?></th>
                        <th><?=lang('report.log04');?></th>
                        <th><?=lang('report.log03');?></th>
                        <th><?=lang('lang.action');?></th>
                        <th><?php echo lang('Referrer'); ?></th>
                        <th><?=lang('link');?></th>
                        <th><?=lang('lang.details');?></th>
                        <th><?=lang('sys.ip08');?></th>
                        <th><?=lang('lang.status');?></th>
                        <th><?=lang('Data');?></th>
                        <th><?=lang('File');?></th>
                        <th><?=lang('player.tm04');?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<form id="_export_csv_form" class="hidden" method="POST" target="_blank">
<input name='json_search' id = "json_csv_search" type="hidden">
</form>
<?php }?>
<script type="text/javascript">
    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
     $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            autoWidth: false,
            searching: false,
            responsive: false,
           // dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },
                <?php

                    if( $this->permissions->checkPermissions('export_user_logs') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                // var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // // utils.safelog(d);

                                // // remove queue ( function not exist )

                                // $.post(site_url('/export_data/userLogs'), d, function(data){
                                //     // utils.safelog(data);

                                //     //create iframe and set link
                                //     if(data && data.success){
                                //         $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                //     }else{
                                //         alert('export failed');
                                //     }
                                // });

                                 var d = {'extra_search': $('#search-form').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/userLogs'));
                                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                            $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/userLogs'), d, function(data){
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

                                <?php }?>

                            }
                        }
                <?php
                    }
                ?>
            ],
            columnDefs: [
                { visible: false, targets: [ 5,7,12 ] }
            ],
            order: [[0, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/userLogs", data, function(data) {
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

        $('#btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
            if (e.which == 13) {
                dataTable.ajax.reload();
            }
        });

         $('[data-toggle="tooltip"]').tooltip();
     });
</script>