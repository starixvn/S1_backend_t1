<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang('lang.search')?>
        </h4>
    </div>
    <div class="panel-collapse">
        <div class="panel-body">

        	<form id="form-filter" method="GET" class="form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_registration_date" class="control-label"><?=lang('player.38')?></label>
                            <div class="input-group">
                                <input id="search_registration_date" class="form-control input-sm dateInput" data-start="#registration_date_from" data-end="#registration_date_to" data-time="true"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_reg_date" id="search_reg_date" <?php echo $conditions['search_reg_date']  == 'on' ? 'checked="checked"' : '' ?> />
                                </span>
                            </div>
                            <input type="hidden" name="registration_date_from" id="registration_date_from" value="<?=$conditions['registration_date_from'];?>" />
                            <input type="hidden" name="registration_date_to" id="registration_date_to" value="<?=$conditions['registration_date_to'];?>" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><?=lang('Player Username')?></label>
                            <input type="text" name="username" class="form-control" value="<?=$conditions['username']?>" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('exclude_player')?></label>
                            <select name="tag_list[]" id="tag_list" multiple="multiple" class="form-control">
                                <option value="notag" id="notag" <?=is_array($selected_tags) && in_array('notag', $selected_tags) ? "selected" : "" ?>><?=lang('player.tp12')?></option>
                                <?php foreach ($tags as $key) {?>
                                    <option value="<?=$key['tagId']?>" <?=is_array($selected_tags) && in_array($key['tagId'], $selected_tags) ? "selected" : "" ?>><?=$key['tagName']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('report.balance.player.balance')?> <=</label>
                            <input type="text" name="total_balance" class="form-control" value="<?=$conditions['total_balance']?>" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3 pull-right text-right">
                        <div class="form-group">
                            <button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-sm btn-portage' : 'btn-primary'?>"><?=lang('lang.search')?></button>
                        </div>
                    </div>
                </div>
        	</form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<?=lang('Player Balance Report')?>
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="playerRealtimeBalance" >
            <thead>
                <tr>
                    <th><?=lang('Player Username')?></th>
                    <th><?=lang('player.38')?></th>
                    <th><?=lang('Player Tag')?></th>
                    <th><?=lang('report.balance.player.balance')?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th><?=lang('report.balance.sum')?></th>
                    <th><span class="sum-balance">0.00</span></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>



<?php if($this->utils->isEnabledFeature('export_excel_on_queue')) : ?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php else :?>
    <form id="_export_csv_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' id="json_csv_search" type="hidden">
    </form>
<?php endif;?>
<script type="text/javascript">
    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

    $("#search_reg_date").change(function() {
        if(this.checked) {
            $('#search_registration_date').prop('disabled',false);
            $('#registration_date_from').prop('disabled',false);
            $('#registration_date_to').prop('disabled',false);
        }else{
            $('#search_registration_date').prop('disabled',true);
            $('#registration_date_from').prop('disabled',true);
            $('#registration_date_to').prop('disabled',true);
        }
    }).trigger('change');

    $(function(){
        let dataTable = $('#playerRealtimeBalance').DataTable({
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            ordering: true,
            iDisplayLength: 25,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search': $('#form-filter').serializeArray(), 'export_format': 'csv', 'export_type': export_type, 'draw':1, 'length':-1, 'start':0};
                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')): ?>
                                $("#_export_excel_queue_form").attr('action', site_url('/export_data/player_realtime_balance'));
                                $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                $("#_export_excel_queue_form").submit();
                        <?php else: ?>
                            $.post(site_url('/export_data/player_realtime_balance'), d, function(data){
                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            }).fail(function(){
                                alert('export failed');
                            });
                        <?php endif; ?>
                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                { className: 'text-center', targets: [0,1,2] },
                { className: 'text-right', targets: 3 },
            ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                var formData = $('#form-filter').serializeArray();
                data.extra_search = formData;
                $.post(base_url + "api/player_realtime_balance", data, function(data) {
                    $('.sum-balance').text(data.sum_balance);

                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
            }
        });
    });

    $(document).ready(function() {
        $('#tag_list').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            buttonWidth: '100%',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return '<?php echo lang('Select Tag'); ?>';
                }
                else {
                    var labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });
    });
</script>

