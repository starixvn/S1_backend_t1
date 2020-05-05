<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-search"></i> <?=lang("lang.search")?> </h4>
    </div>
    <div class="panel-body">
        <form class="col-md-12" id="search-form">

            <div class="form-group col-md-3">
                <label class="control-label"><?=lang('player.ufr02')?></label>
                <div class="input-group">
                    <input id="search_date" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                    <span class="input-group-addon input-sm">
                        <input type="checkbox" name="enabled_date" <?=isset($player['username']) ? '' : 'checked="checked"'; ?>>
                    </span>
                </div>
                <input type="hidden" name="date_from" id="date_from"/>
                <input type="hidden" name="date_to" id="date_to"/>
            </div>

            <div class="form-group col-md-2">
                <label class="control-label"><?=lang('player.fr03');?></label>
                <input type="text" name="invited" class="form-control input-sm"/>
            </div>

            <div class="form-group col-md-2">
                <label class="control-label"><?=lang('player.fr04');?></label>
                <input type="text" name="inviter" value="<?=isset($player['username']) ? $player['username'] : ''; ?>" class="form-control input-sm"/>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="form-group col-md-2 pull-right" style="padding-top: 22px; margin-right: -15px;">
                    <button type="button" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit"><i class="fa fa-search"></i> <?=lang('lang.search');?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-primary" >
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-profile"></i><?=lang('pay.friendref');?> </h4>
    </div>
    <div class="panel-body" >
        <div class="table-responsive">
            <table class="table table-hover table-condensed table-bordered" id="myTable" style="width: 100%;">
                <thead>
                    <tr>
    					<th><?=lang('player.fr03');?></th>
    					<th><?=lang('player.fr05');?></th>
    					<th><?=lang('player.fr04');?></th>
                        <th><?=lang('player.fr02');?></th>
    					<th><?=lang('pay.transact');?></th>
                        <th><?=lang('player.ut09');?></th>
                        <th><?=lang('lang.action');?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
     $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({
            order: [[3, 'desc']],
            searching: true,

            stateSave: true,
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
					className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : '' ?>',
                    postfixButtons: [ 'colvisRestore' ]
                },
                <?php if( $this->permissions->checkPermissions('export_friend_referral') ){ ?>
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>',
                    action: function ( e, dt, node, config ) {

                        var form_params = $('#search-form').serializeArray();

                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};

                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/friendReferral'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                    }
                }
                <?php } ?>
            ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/friend_referral", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                        $('[data-toggle=confirmation]').confirmation({
                            rootSelector: '[data-toggle=confirmation]',
                          // other options
                        });
                    }
                }, 'json');
            }
        });

        $('#btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        $(document).on("click",".decline_referral",function(){
            var id = $(this).data("id");
            var invited_player = $(this).data("invited");
            var inviter = $(this).data("inviter");
            $.post(base_url + "api/declinePlayerFriendReferral", { id: id , invited_player: invited_player , inviter: inviter}, function(data) {
                dataTable.ajax.reload();
            });
        });
    });
</script>