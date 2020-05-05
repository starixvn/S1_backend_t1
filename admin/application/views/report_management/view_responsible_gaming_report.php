<style>
    .custimized-margin-left > span{
        margin-left: 3%;
    }
    .input-group[class*="col-"] {
        float: left;
    }
</style>
<?php
$conditions['search_by']="";
$conditions['username']="";
$conditions['search_reg_date']="";
$conditions['registration_date_from']="";
$conditions['registration_date_to']="";

?>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePlayerReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body" style="margin-right: 1em;">
            <form id="form-filter" class="form-horizontal" method="post">
                <div class="row">
                    <div class="col-md-3">
                        <div style="margin-left: 15px;">
                            <div class="form-group">
                                <label for="search_start_time" class="control-label"><?=lang('Start Time')?></label>
                                <div class="input-group">
                                    <input id="search_start_time" class="form-control input-sm dateInput" data-start="#start_at_from" data-end="#start_at_to" data-time="true"/>
                                    <span class="input-group-addon input-sm">
                                        <input type="checkbox" name="search_start_time_date" id="search_start_time_date"    />
                                    </span>
                                </div>
                                <input type="hidden" name="start_at_from" id="start_at_from" value="" />
                                <input type="hidden" name="start_at_to" id="start_at_to" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="margin-left: 25px;">
                            <div class="form-group">
                                <label for="search_update_date" class="control-label"><?=lang('Updated At')?></label>
                                <div class="input-group">
                                    <input id="search_update_date" class="form-control input-sm dateInput" data-start="#update_at_from" data-end="#update_at_to" data-time="true"/>
                                    <span class="input-group-addon input-sm">
                                        <input type="checkbox" name="search_reg_date" id="search_reg_date"    />
                                    </span>
                                </div>
                                <input type="hidden" name="update_at_from" id="update_at_from" value="" />
                                <input type="hidden" name="update_at_to" id="update_at_to" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div style="margin-left: 25px;">
                            <div class="form-group">
                                <label  class="control-label">
                                    <span style="margin-right:5px;"><?php echo lang('Username'); ?>:</span>
                                    <input type="radio" name="search_by" value="1"  checked="checked"  />
                                    <span style="margin-right:5px;"><?php echo lang('Similar');?></span>
                                </label>
                                <label  class="control-label">
                                    <input type="radio" name="search_by" value="2" />
                                    <?php echo lang('Exact'); ?>
                                </label>
                                <input type="text" name="username" id="username" value="<?php echo $conditions['username']; ?>" class="form-control input-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="margin-left: 25px;">
                            <div class="form-group">
                                <label for="label_Tag" class="control-label"><?=lang('Tag')?></label>
                                <select  name="tag_id" class="form-control">
                                    <option value="">&mdash; <?= lang('None') ?> &mdash;</option>
                                    <?php if (!empty($tags)): ?>
                                        <?php foreach ($tags as $tag): ?>
                                            <option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                    <!--<div class="col-md-4">
                        <div style="margin-left: 15px;">
                            <div class="form-group">
                                <label for="risk_score" class="control-label"><?=lang('risk score')?></label>
                                <select  name = "risk_score" class="form-control">
                                    <option select="" value="-1">selected</option>
                                    <option  value="All">All</option>
                                    <option value="7">Low(0~7)</option>
                                    <option value="14">Medium(8~14)</option>
                                    <option value="20">High(15~20)</option>
                                    <option value="21">Very High( &gt;21)</option>
                                </select>
                            </div>

                        </div>

                    </div>
                    <div class="col-md-3">
                        <div style="margin-left: 15px;">
                            <div class="form-group">
                                <label for="kyc_level" class="control-label"><?=lang('KYC Level')?></label>
                                <select name="kyc_lvl" class="form-control">
                                    <option value="">selected</option>
                                    <?php if (!empty($kyc)): ?>
                                    <?php foreach ($kyc as $kycAry): ?>
                                    <option value="<?=$kycAry['id']?>"><?=$kycAry['kyc_lvl']?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>

                        </div>

                    </div>-->

                <div class="row">
                    <div class="col-md-3 input-group">
                        <div style="margin-left: 15px;">
                            <label for="Player_Level" class="control-label"><?=lang('Player Level')?></label>
                            <select  name="player_level" class="form-control">
                                <option value="">&mdash; <?= lang('None') ?> &mdash;</option>
                                <?php if (is_array($playerLevel)):?>
                                    <?php foreach($playerLevel as $playAry){?>
                                        <option value="<?=$playAry['vipsettingcashbackruleId']?>"><?=$playAry['groupLevelName']?></option>
                                    <?php }?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9" style="margin-left: -5px; margin-top:9px;">
                        <div class="col-md-2" style="width: 17.2%;">
                            <label style="margin-bottom: 0;"><?=lang('Days Left')?> >=</label>
                            <input type="number" class="form-control" name="days_left_min" id="days_left_min" value="">
                        </div>
                        <div class="col-md-2" style="width: 17.2%;">
                            <label style="margin-bottom: 0;"><?=lang('Days Left')?> <=</label>
                            <input type="number" class="form-control" name="days_left_max" id="days_left_max" >
                        </div>
                    </div>
                </div>

                <fieldset style="margin-top:30px;">
                    <legend>
                        <label class="cb-group-title"><?= lang('Type of Exclusion') ?></label>
                    </legend>
                    <div class="custimized-margin-left">
                        <span style="margin-left:5px;">
                            <input type="checkbox" id="rg_type_selectall" checked="1" />
                            <label for="rg_type_selectall"><?= lang('Select/Unselect All') ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_type" id="rg_type_3" value="<?= Responsible_gaming::COOLING_OFF ?>" checked="1" />
                            <label for="rg_type_3"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::COOLING_OFF) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_type" id="rg_type_6" value="<?= Responsible_gaming::DEPOSIT_LIMITS ?>" checked="1" />
                            <label for="rg_type_6"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::DEPOSIT_LIMITS) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_type" id="rg_type_8" value="<?= Responsible_gaming::WAGERING_LIMITS ?>" checked="1" />
                            <label for="rg_type_8"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::WAGERING_LIMITS) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_type" id="rg_type_1" value="<?= Responsible_gaming::SELF_EXCLUSION_TEMPORARY ?>" checked="1" />
                            <label for="rg_type_1"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::SELF_EXCLUSION_TEMPORARY) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_type" id="rg_type_2" value="<?= Responsible_gaming::SELF_EXCLUSION_PERMANENT ?>" checked="1" />
                            <label for="rg_type_2"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::SELF_EXCLUSION_PERMANENT) ?></label>
                        </span>
                        <!-- div class="col-md-2">
                            <input type="checkbox" name="rg_type" id="rg_type_4" value="<?= Responsible_gaming::TIMER_REMINDERS ?>"" checked="1" />
                            <label for="rg_type_4"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::TIMER_REMINDERS) ?></label>
                        </div-->
                    </div>
                    <!-- <div class="row" style="margin-bottom: 15px"> -->
                        <!-- div class="col-md-2">
                            <input type="checkbox" name="rg_type" id="rg_type_5" value="<?= Responsible_gaming::SESSION_LIMITS ?>" checked="1" />
                            <label for="rg_type_5"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::SESSION_LIMITS) ?></label>
                        </div -->
                        <!-- div class="col-md-2">
                            <input type="checkbox" name="rg_type" id="rg_type_7" value="<?= Responsible_gaming::LOSS_LIMITS ?>" checked="1" />
                            <label for="rg_type_7"><?= $this->responsible_gaming->type_to_string(Responsible_gaming::LOSS_LIMITS) ?></label>
                        </div -->
                    <!-- </div> -->
                </fieldset>
                <fieldset style="margin-top:30px;">
                    <legend>
                        <label><?= lang('Status') ?></label>
                    </legend>
                     <div class="custimized-margin-left">
                        <span style="margin-left:5px;">
                            <input type="checkbox" id="rg_status_selectall" />
                            <label for="rg_status_selectall"><?= lang('Select/Unselect All') ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_1" value="<?= Responsible_gaming::STATUS_REQUEST ?>" checked="1" />
                            <label for="rg_stat_1"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_REQUEST) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_2" value="<?= Responsible_gaming::STATUS_APPROVED ?>" checked="1" />
                            <label for="rg_stat_2"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_APPROVED) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_5" value="<?= Responsible_gaming::STATUS_EXPIRED ?>" />
                            <label for="rg_stat_5"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_EXPIRED) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_3" value="<?= Responsible_gaming::STATUS_DECLINED ?>" />
                            <label for="rg_stat_3"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_DECLINED) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_4" value="<?= Responsible_gaming::STATUS_CANCELLED ?>" />
                            <label for="rg_stat_4"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_CANCELLED) ?></label>
                        </span>
                        <span>
                            <input type="checkbox" name="rg_status" id="rg_stat_6" value="<?= Responsible_gaming::STATUS_PLAYER_DEACTIVATED ?>" />
                            <label for="rg_stat_6"><?= $this->responsible_gaming->status_to_string(Responsible_gaming::STATUS_PLAYER_DEACTIVATED) ?></label>
                        </span>
                     </div>
                </fieldset>
                <div class="row" style="margin-top: 30px">
                    <div class="col-md-4"></div>
                    <div class="col-md-12 text-right">
                        <input type="button" value="<?php echo lang('Reset');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ' btn-default'?>" onclick="javascript: rg_form_reset();">
                        <input type="submit" id="searchby" name="submit" value="<?php echo lang('lang.searchby');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>">
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-users"></i> <?=lang('Responsible Gaming Report')?> </h4>
    </div>
    <div class="panel-body">
        <div class="panel-body"><button id="btn_add_tag" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> "><?=lang('aff.t08');?></button> </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="myTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?=lang('ID')?></th>
                    <th><?=lang('Username')?></th>
                    <th><?=lang('Player Level')?></th>
                    <th><?=lang('Type of Exclusion')?></th>
                    <th><?=lang('Status')?></th>
                    <th><?=lang('Date of Request')?></th>
                    <th><?=lang('Requested Amount')?></th>
                    <th><?=lang('Start Time')?></th>
                    <th><?=lang('End Time')?></th>
                    <th><?=lang('Total Days')?></th>
                    <th><?=lang('Days Left')?></th>
                    <th><?=lang('Tag')?></th>
                    <th><?=lang('risk score')?></th>
                    <th><?=lang('KYC Level')?></th>
                    <th><?=lang('Updated At')?></th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td></td>
                    <td><strong><?=lang('Total')?></strong></td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                    <td>&mdash;</td>
                </tr>
                </tfoot>
            </table>
        </div>

    </div>
    <div class="panel-footer"></div>
</div>
<!--BROADCAST MESSAGE BOX START-->
<div id="add_tag_box"  class="modal fade bs-example-modal-md"  data-backdrop="static"
     data-keyboard="false"   role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">


            <div class="modal-header panel-heading">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                <h3 id="myModalLabel"><?=lang('aff.t08')?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label for="subject"><?=lang("aff.aa08")?> </label>
                        <select id="tags-list"  name="tags-list" class="form-control input-sm"></select>
                        <span class="help-block" style="color:#F04124"></span>
                    </div>

                    <div class="alert alert-success" id="msgsuccess">
                        <strong><?=lang('Save settings successfully')?>!</strong>
                    </div>
                    <div class="alert alert-danger" id="msgerror">
                        <strong><?=lang('Save settings failed')?>!</strong>
                    </div>
                </div>

            </div>
            <div class="modal-footer" >
                <div style="height:70px;position:relative;">

                    <button type="button" class="btn btn-default"  id="cancel_broadcast"  ><?=lang('lang.close');?></button>
                    <button type="button" id="win_add_tag" class="btn btn-primary">  <?=lang('aff.t08')?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
<?php }?>

<!--BROADCAST MESSAGE BOX END-->
<script type="text/javascript">
    var dt_page = 0;
    var dt_start = 0;
    var dt_length = 0;

    function chkTag(){

        var checkedCount=0;
        $(".clickTag:checked").each(function(){
            checkedCount++;
        });
        if(checkedCount>0){
            $('#btn_add_tag').prop('disabled', false);
        }else{
            $('#btn_add_tag').prop('disabled', true);
        }
    }

    $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            autoWidth: false,
            searching: false,
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                { className: 'text-right', targets: [ 9, 10, 12] },
                {   targets: [ 8, 9, 10, 11, 12 ],
                    render: function(data, type, row, meta ){
                        //console.log(row);
                        if(type==='display'){
                            if ( data === '0.00' || data == '&mdash;'){
                                return  '<span class="text-muted">' + data + '</span>' ;
                            }  else{
                                return  '' + data + '' ;
                            }
                        }
                        return data;
                    }
                }
            ],
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if( $this->permissions->checkPermissions('export_responsible_gaming_report') ){ ?>
                ,{
                    text: "<?=lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export_csv_btn',
                    action: function ( e, dt, node, config ) {

                        var form_params=$('#form-filter').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};

                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/exportResponsibleGamingReport'));
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();
                        <?php }?>
                    }
                }
                <?php } ?>
            ],
            order: [ 5, 'desc' ] ,

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                if (dt_start != 0) {
                    data['start'] = dt_start;
                }
                if (dt_length != 0) {
                    data['length'] = dt_length;
                }

                dataTable.page(dt_page);
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/responsibleGamingReport", data, function(data) {
                    if(data.data.length == 0){
                        $('.export_csv_btn').addClass('disabled');
                    }else{
                        $('.export_csv_btn').removeClass("disabled");
                    }

                    callback(data);

                }, 'json');
            }
        });
        dataTable.on( 'page.dt', function () {
            var info = dataTable.page.info();
            dt_page = info.page;
            dt_start = info.start;
            dt_length = info.length;
        } );


        $('#form-filter').submit( function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });

        $('#group_by').change(function() {
            var value = $(this).val();
            if (value == 'player.playerId') {
                $('#username').val('').prop('disabled', false);
            } else {
                $('#username').val('').prop('disabled', true);
            }
        });
        $("#search-form input").not("#search_update_date,  #search_by").change( function() {
            $("#search_reg_date").prop('checked', false).trigger('change');
        });


        $("#search_reg_date").change(function() {
            if(this.checked) {
                $('#search_update_date').prop('disabled',false);
                $('#update_at_from').prop('disabled',false);
                $('#update_at_to').prop('disabled',false);
            }else{
                $('#search_update_date').prop('disabled',true);
                $('#update_at_from').prop('disabled',true);
                $('#update_at_to').prop('disabled',true);
            }
        }).trigger('change');

        $("#search_start_time_date").change(function() {
            if(this.checked) {
                $('#search_start_time').prop('disabled',false);
                $('#start_at_from').prop('disabled',false);
                $('#start_at_to').prop('disabled',false);
            }else{
                $('#search_start_time').prop('disabled',true);
                $('#start_at_from').prop('disabled',true);
                $('#start_at_to').prop('disabled',true);
            }
        }).trigger('change');


        $('#btn_add_tag').prop('disabled', true);

        $('#btn_add_tag').click(function(){
            $("#msgsuccess").hide();
            $("#msgerror").hide();
           $('#add_tag_box').modal('show');
            getAllTags();

        });


        $("#win_add_tag").click(function(){

            var custag;
            var playerId =[];
            var postVal;
            event.preventDefault();

            $("#win_add_tag").prop('disabled',true);


            $(".clickTag:checked").each(function(){
                playerId.push(this.value);
            });
            //tags-list
            postVal = $("#tags-list").val();


            $.ajax({
                'url' : site_url('player_management/addTagMultiplayer/'),
                'type' : 'POST',
                'data': {tagPlayerId: playerId, tagVal: postVal},
                'cache' : false,
                'dataType' : "json"
            }).done(
                function(data){
                    if(data['status']){
                        $("#msgsuccess").show();
                    }else{
                        $("#msgerror").show();
                    }
                }
            );


            //$('#add_tag_box').modal('toggle');
            return false;
        });
        $("#cancel_broadcast").click(function(){
            $("#msgsuccess").hide();
            $("#msgerror").hide();
            $('#searchby').trigger( "click" );
            $('#btn_add_tag').prop('disabled', true);
            $('#add_tag_box').modal('hide');



        });


        /*$(".batch-message-cb:checked").each(function(){
            var playerId= $(this).attr('value');
            var playerUsername = $(this).attr('username');
            var player = {id:playerId, username:playerUsername};
            if(playerId && playerUsername){
                batchPlayers.push(player);
            }
        });*/

        $('.export_excel').click(function(){

            // utils.safelog(dataTable.columns());

            var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
            // utils.safelog(d);
            $.post(site_url('/export_data/player_reports'), d, function(data){
                // utils.safelog(data);

                //create iframe and set link
                if(data && data.success){
                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                }else{
                    alert('export failed');
                }
            });
        });

        function getAllTags() {
            $.ajax({
                url: '/player_management/getAllTagsOnly/',
                type: 'GET',
                dataType: "json",
            }).done(function (data) {
                $('#tags-list').find('option').remove().end();
                for (var i = 0; i < data.length; i++) {
                    $("#tags-list").append('<option value="' + data[i].tagId + '" data-color="' + data[i].tagColor + '">' + data[i].tagName + '</option>');
                }

            }).fail(function (jqXHR, textStatus) {

            });
        }

    });

    $(document).ready( function () {
        // Check/Uncheck all box for types
        $('input#rg_type_selectall').click( function () {
            $('input[name="rg_type"]').prop('checked', $(this).is(':checked'));
        });
        // Check/Uncheck all box for statuses
        $('input#rg_status_selectall').click( function () {
            $('input[name="rg_status"]').prop('checked', $(this).is(':checked'));
        });
    });

    function rg_form_reset() {
        // Reset all text inputs
        $('form#form-filter input[type="text"]').not('#search_update_date').val('');

        // Reset all number inputs
        $('form#form-filter input[type="number"]').not('#search_update_date').val('');

        // Reset search_by
        $('input[name=search_by][value=1]').click();

        // Reset updated_at
        $('input#search_reg_date').prop('checked', false);

        // Reset start_at
        $('input#search_start_time_date').prop('checked', false);

        // Reset player level
        $('select[name=player_level]').prop('selectedIndex', 0);

        // Reset tags
        $('select[name=tag_id]').prop('selectedIndex', 0);

        // Reset types
        $('input[name="rg_type"]').prop('checked', true);
        $('input#rg_type_selectall').prop('checked', true);

        // Reset statuses
        $('input[name="rg_status"]').prop('checked', false);
        $('input#rg_status_selectall').prop('checked', false);
        $('input[name="rg_status"][value="1"]').prop('checked', true);
        $('input[name="rg_status"][value="2"]').prop('checked', true);

        // Reset Date Picker
        var default_start = '<?php echo date("Y-m-d 00:00:00")?>';
        var default_end = '<?php echo date("Y-m-d 23:59:59")?>';

        var search_start_time = $('#search_start_time');
        var checked=$(search_start_time).is(":checked");
        search_start_time.prop("disabled", !checked);
        search_start_time.data('daterangepicker').setStartDate(default_start);
        search_start_time.data('daterangepicker').setEndDate(default_end);
        $(search_start_time.data('start')).val(default_start);
        $(search_start_time.data('end')).val(default_end);

        var search_update_date = $('#search_update_date');
        var checked=$(search_update_date).is(":checked");
        search_update_date.prop("disabled", !checked);
        search_update_date.data('daterangepicker').setStartDate(default_start);
        search_update_date.data('daterangepicker').setEndDate(default_end);
        $(search_update_date.data('start')).val(default_start);
        $(search_update_date.data('end')).val(default_end);
    }
</script>

