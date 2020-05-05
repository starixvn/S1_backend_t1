
<div class="panel panel-primary hidden">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("sys.gm.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGameDescription" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseGameDescription" class="panel-collapse collapse">
        <form id="form-filter" class="form-horizontal" method="get">
	        <div class="panel-body">
	        	<div class="row">
                    <div class="col-md-4">
                        <label class="control-label" for="date_from_search"><?=lang('sys.gm.startdate')?>:</label>
                        <input id="date_from_search" name = "date_from_search" class="form-control input-sm" />
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="game_platform_id"><?=lang('sys.gm.gameplatformlist')?>:</label>
                        <select name="game_platform_id" id="game_platform_id" class="form-control input-sm">
                            <option value=""><?=lang('Select Game Platform')?></option>
                            <?php foreach ($gameapis as $gameApi) { ?>
                                <option value="<?=($gameApi['id'])?>"><?=$gameApi['system_code']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                    	<label class="control-label" for="gamePlatformId"></label>
                        <input type="button" value="<?=lang('lang.search')?>" id="search_main" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>"  style="margin-top:25px">
                    </div>
	            </div>
	        </div>
        </form>
    </div>
</div>

<div class="row" id="user-container">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?=lang('sys.gm.schedule');?>
                    <?php
                    if($this->permissions->checkPermissions('maintenance_schedule_control'))
                    {
                        ?>
                        <button type="button" value="" id="addmaintenancemission" name="addmaintenancemission" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage btn-xs' : 'btn-success btn-sm'?>" style="margin-top:<?=$this->utils->getConfig('use_new_sbe_color') ? '0' : '-5'?>px">
                            <i class="glyphicon glyphicon-plus" data-placement="bottom"></i>
                            <?=lang('sys.gm.addmaintenancemission');?>
                        </button>
                        <?php
                    }
                    ?>

                </h3>
            </div>
			<div class="panel-body" id="list_panel_body">
				<div class="table-responsive">
				<div class="clearfix"></div>
					<table class="table table-bordered table-hover dataTable" id="data_table_game_maintenance_schedule" >
                        <thead>
                            <tr>
                                <th><?=lang('sys.gm.header.gamemaintenance')?></th>
                                <th><?=lang('sys.gm.header.startenddate')?></th>
                                <th><?=lang('sys.gm.header.missionstatus')?></th>
                                <th><?=lang('sys.gm.header.maintenancescheduleby')?></th>
                                <th><?=lang('sys.gm.header.lastupdatedby')?></th>
                                <th><?=lang('mod.updateDate')?></th>
                                <th><?=lang('sys.gd11')?></th>
                                <th><?=lang('lang.action')?></th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center">
                        </tbody>
                    </table>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

        <div class="modal fade " id="modalAddMaintenanceMission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 60%;margin-top: 10%">
            <div class="modal-content">
            <form id="formaddmaintenancemission" class="form-horizontal" method="post" >
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <i class="glyphicon glyphicon-plus" data-placement="bottom"></i> <?=lang('sys.gm.addmaintenanceschedule')?></h4>
              </div>
              <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                           <label class="control-label" for="gameplatformid"><?=lang('sys.gm.gameplatformlist')?>:</label>
                           <select name="gameplatformid" id="gameplatformid" class="form-control input-sm" required="true">
                                <option value=""><?=lang('Select Game Platform')?></option>
                                <?php foreach ($gameapis as $gameApi) { ?>
                                    <option value="<?=($gameApi['id'])?>"><?=$gameApi['system_code']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" for="startdate"><?=lang('lang.start')?>:</label>
                            <input id="startdate" name = "startdate" class="form-control input-sm"/>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" for="enddate"><?=lang('lang.end')?>:</label>
                            <input id="enddate" name="enddate" class="form-control input-sm" />
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-6">
                            <input type = "checkbox" id="hide_wallet_player_center" name="hide_wallet_player_center" />Hide Wallet in Player Center
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-12">
                            <label class="control-label" for="note"><?=lang('sys.gd11')?>:</label>
                            <textarea style="width: 100%;height: 150px;" id="note" name="note" class="form-control" required="true"></textarea>
                        </div>
                    </div>
              </div>
              <div class="modal-footer" style="text-align: center">
                <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary '?>" data-dismiss="modal"><?=lang("lang.cancel")?></button>
                <button type="button" class="btn btn-primary btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary '?>" id="btnaddmaintenancemission"><?=lang("lang.add")?></button>
              </div>
             </form>
            </div>
          </div>
        </div>


        <div class="modal fade " id="modalEditMaintenanceMission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document" style="width: 60%;margin-top: 10%">
            <div class="modal-content">
            <form id="formeditmaintenancemission" class="form-horizontal" method="post" >
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <i class="glyphicon glyphicon-pencil" data-placement="bottom"></i> <?=lang('sys.gm.editmaintenanceschedule')?></h4>
              </div>
              <div class="modal-body">
                    <div class="row">
                        <input type="text" style="display:none" id="gamemaintenanceid" name="gamemaintenanceid">
                        <div class="col-md-6">
                           <label class="control-label" for="e_gameplatformid"><?=lang('sys.gm.gameplatformlist')?>:</label>
                           <select name="e_gameplatformid" id="e_gameplatformid" class="form-control input-sm" required="true">
                                <option value=""><?=lang('Select Game Platform')?></option>
                                <?php foreach ($gameapis as $gameApi) { ?>
                                    <option value="<?=($gameApi['id'])?>"><?=$gameApi['system_code']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" for="e_startdate"><?=lang('lang.start')?>:</label>
                            <input id="e_startdate" name = "e_startdate" class="form-control input-sm"/>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" for="e_enddate"><?=lang('lang.end')?>:</label>
                            <input id="e_enddate" name="e_enddate" class="form-control input-sm dateInput"/>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-6">
                            <input type = "checkbox" id="e_hide_wallet_player_center" name="e_hide_wallet_player_center" />Hide Wallet in Player Center
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-12">
                            <label class="control-label" for="e_note"><?=lang('sys.gd11')?>:</label>
                            <textarea style="width: 100%;height: 150px;" id="e_note" name="e_note" class="form-control" required="true"></textarea>
                        </div>
                    </div>
              </div>
              <div class="modal-footer" style="text-align: center">
                  <button type="button" class="btn btn-primary btn-sm" id="btneditmaintenancemission"><?=lang("lang.save")?></button>
                  <button class="btn btn-primary btn-sm" data-dismiss="modal"><?=lang("lang.cancel")?></button>
              </div>
             </form>
            </div>
          </div>
        </div>

<script type="text/javascript">

    var pending = <?=External_system::MAINTENANCE_STATUS_PENDING?>;
    var cancel = <?=External_system::MAINTENANCE_STATUS_CANCELLED?>;
    var data_table_game_maintenance_schedule = $('#data_table_game_maintenance_schedule').DataTable({
        order: [[1, 'desc']],
        searching: false,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#form-filter').serializeArray();
            console.log(data.extra_search)
            $.post(base_url + "game_api/gameMaintenanceSchedule", data, function(data) {
                callback(data);
            },'json');
        },
    });

     $("#search_main").on('click',function(){
        data_table_game_maintenance_schedule.ajax.reload();
    })

    $("#addmaintenancemission").on('click',function(){
       $('#modalAddMaintenanceMission').modal(true);
    });

    $("#btnaddmaintenancemission").on('click',function(){
        var form = $('#formaddmaintenancemission').serializeArray();
        $.post(base_url + "game_api/addGameMaintenanceSchedule", form, function(data) {
             location.reload();
        },'json');
    });


    $("#data_table_game_maintenance_schedule").on('click','.edit',function(){
        var id = $(this).attr("id");
        $('#modalEditMaintenanceMission').modal(true);
        $.post(base_url + "game_api/getDetailGameMaintenanceSchedule", {id:id}, function(data) {
            $("#e_gameplatformid").val(data[0].game_platform_id);
            $("#e_startdate").val(data[0].start_date);
            $("#e_enddate").val(data[0].end_date);
            $("#e_note").val(data[0].note);
            $("#gamemaintenanceid").val(data[0].id);
        },'json');
    });

    $("#btneditmaintenancemission").on('click',function(){
        var form = $('#formeditmaintenancemission').serializeArray();
        $.post(base_url + "game_api/editGameMaintenanceSchedule", form, function(data) {
           location.reload();
        },'json');
    });

    $("#data_table_game_maintenance_schedule").on('click','.cancel',function(){
        var id = $(this).attr("id");
        if (confirm(lang('sys.gm.cancelmaintenanceschedule'))) {
            $.post(base_url + "game_api/updateGameMaintenanceScheduleStatus", {id:id,counter:0,status:cancel}, function(data) {
                location.reload();
            });
        }
    });

    $("#data_table_game_maintenance_schedule").on('click','.stop',function(){
        var id = $(this).attr("id");
        if (confirm(lang('sys.gm.stopmaintenanceschedule'))) {
            $.post(base_url + "game_api/updateGameMaintenanceScheduleStatus", {id:id,counter:1, status:cancel}, function(data) {
                location.reload();
            })
        }
    })

    $(function() {

      $('#date_from_search').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        startDate: moment(),
        locale: {
          format: 'YYYY-MM-DD'
          // format: 'YYYY-MM-DD HH:mm:ss'
        }
      });

      $('#startdate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        startDate: moment(),
        locale: {
          format: 'YYYY-MM-DD HH:mm:ss'
        }
      });

      $('#e_startdate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        startDate: moment(),
        locale: {
          format: 'YYYY-MM-DD HH:mm:ss'
        }
      });

      $('#enddate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        startDate: moment(),
        locale: {
          format: 'YYYY-MM-DD HH:mm:ss'
        }
      });

      $('#e_enddate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        startDate: moment(),
        locale: {
          format: 'YYYY-MM-DD HH:mm:ss'
        }
      });


    });


</script>