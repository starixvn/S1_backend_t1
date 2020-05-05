<div class="panel panel-primary hidden">
  <div class="panel-heading">
    <h4 class="panel-title">
      <i class="fa fa-search"></i> <?=lang("lang.search")?>
      <span class="pull-right">
        <a data-toggle="collapse" href="#collapseGameApiHistory" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
      </span>
    </h4>
  </div>
  <div id="collapseGameApiHistory" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
    <form id="form-filter" class="form-horizontal" method="get">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <label for="search_by" class="control-label">
              <input type="radio" name="search_by" value="1" checked <?=$conditions['search_by'] == '1' ? 'checked="checked"' : '' ?> />  <?php echo lang('sys.createdon'); ?>
              <input type="radio" name="search_by" value="2" <?=$conditions['search_by'] == '2' ? 'checked="checked"' : ''?> />
              <?php echo lang('sys.updatedon'); ?>
            </label>
            <input class="form-control dateInput input-sm" id="dateRangePicker" data-start="#date_from" data-end="#date_to" data-time="true"/>
            <input type="hidden" id="date_from" name="date_from" value="<?=$conditions['date_from'];?>"/>
            <input type="hidden" id="date_to" name="date_to" value="<?=$conditions['date_to'];?>"/>
            <div class="checkbox" >
              <label>
                <input type="checkbox" name="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
                <?php echo lang('Enabled date'); ?>
              </label>
            </div>
          </div>
          <div class="col-md-2">
            <label class="control-label" for="action"><?=lang('Action')?>:</label>
            <select name="action" id="action" class="form-control">
              <option value=""><?= lang('Select All'); ?></option>
              <option <?php echo $conditions['action'] == External_system::GAME_API_HISTORY_ACTION_ADD   ? 'selected' : "" ?>  value="<?php echo External_system::GAME_API_HISTORY_ACTION_ADD ?>"> <?php echo lang('Add Credential') ?> </option>
              <option <?php echo $conditions['action'] == External_system::GAME_API_HISTORY_ACTION_UPDATE ? 'selected' : ""?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_UPDATE ?>"> <?php echo lang('Update Credential') ?> </option>
              <option  <?php echo $conditions['action'] == External_system::GAME_API_HISTORY_ACTION_DELETE  ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_DELETE ?>"> <?php echo lang('Delete Credential') ?> </option>
              <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_UNDER_MAINTENANCE ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_UNDER_MAINTENANCE ?>"> <?php echo lang('Under Maintenance') ?> </option>
              <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_FINISH_MAINTENANCE ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_FINISH_MAINTENANCE ?>"> <?php echo lang('Finish Maintenance') ?> </option>
              <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_BLOCKED ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_BLOCKED ?>"> <?php echo lang('Blocked') ?> </option>
              <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_UNBLOCKED ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_UNBLOCKED ?>"> <?php echo lang('Unblocked') ?> </option>
               <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_PAUSED_SYNC ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_PAUSED_SYNC ?>"> <?php echo lang('Paused Sync') ?> </option>
                <option <?php echo $conditions['action'] ==  External_system::GAME_API_HISTORY_ACTION_RESUMED_SYNC ? 'selected' : "" ?> value="<?php echo External_system::GAME_API_HISTORY_ACTION_RESUMED_SYNC ?>"> <?php echo lang('Resumed Sync') ?> </option>
            </select>
          </div>
          <div class="col-md-3">
           <label class="control-label" for="gamePlatfomId"><?=lang('Game Platform')?>:</label>
           <select name="game_platform_id" id="game_platform_id" class="form-control">
            <option value=""><?php echo lang('Select All'); ?></option>
            <?php foreach($game_apis_arr  as  $key =>  $value): ?>
              <option <?php echo $conditions['game_platform_id'] == $value ? 'selected' : "" ?> value="<?php echo $value ?>"> <?php echo $value.'-'. $key ?> </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
        </div>
      </div>
      <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
        </div>
      </div>
    </div>
    <div class="panel-footer text-right">
     <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
   </div>
 </form>
</div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading custom-ph">
    <h3 class="panel-title custom-pt" > <i class="icon-list"></i> <?=lang('Game API History');?></h3>
  </div>
  <style>
    .table > tbody > tr > td, .table > tfoot > tr > td {
      white-space: nowrap;
      vertical-align: middle;
    }
  </style>
  <div class="panel-body" id="list_panel_body" >
    <div class="table-responsive">
      <table class="table table-striped table-hover" style="width:100%"  id="my_table" >
        <thead>
          <tr>
            <th><?=lang('history.id')?></th>
            <th><?=lang('lang.action')?></th>
            <th><?=lang('sys.updatedby')?></th>
            <th><?=lang('sys.createdon')?></th>
            <th ><?=lang('sys.updatedon')?></th>
            <th><?=lang('Game Platform')?></th>
            <th><?=lang('sys.ga.livemode')?></th>
            <th><?=lang('sys.ga.status')?></th>
            <th><?=lang('Maintenance Mode')?></th>
            <th><?=lang('Pause Sync')?></th>
            <th><?=lang('sys.ga.systemcode')?></th>
            <th><?=lang('sys.ga.systemname')?></th>
            <th><?=lang('sys.ga.extrainfo')?></th>
            <th><?=lang('sys.ga.sandboxextrainfo')?></th>
            <th><?=lang('sys.ga.systemtype')?></th>
            <th><?=lang('sys.ga.lastsyncdt')?></th>
            <th><?=lang('sys.ga.note')?></th>
            <th><?=lang('sys.ga.lastsyncid')?></th>
            <th><?=lang('sys.ga.lastsyncdet')?></th>
            <th><?=lang('sys.ga.liveurl')?></th>
            <th><?=lang('sys.ga.sandboxurl')?></th>
            <th><?=lang('sys.ga.livekey')?></th>
            <th><?=lang('sys.ga.sandboxkey')?></th>
            <th><?=lang('sys.ga.livesecret')?></th>
            <th><?=lang('sys.ga.sandboxsecret')?></th>
            <th><?=lang('sys.ga.secondurl')?></th>
            <th><?=lang('sys.ga.liveacct')?></th>
            <th><?=lang('sys.ga.sandboxacct')?></th>
            <th><?=lang('sys.ga.classname')?></th>
            <th><?=lang('sys.ga.localpath')?></th>
            <th><?=lang('sys.ga.manager')?></th>
            <th><?=lang('sys.gd7') . " " . lang('sys.rate');?></th>
            <th><?=lang('Allow Deposit Withdraw')?></th>
            <th><?=lang('sys.ga.amount_float')?></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="panel-footer"></div>
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

 $(document).ready(function () {

   var dataTable = $('#my_table').DataTable({
    autoWidth: false,
    searching: true,
    dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
    <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>

    buttons: [
      {
        extend: 'colvis',
        postfixButtons: [ 'colvisRestore' ],
        className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
      },

       {
        text: "<?php echo lang('CSV Export'); ?>",
        className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export_excel',
        action: function ( e, dt, node, config ) {
           var d = {'extra_search': $('#form-filter').serializeArray(), 'export_format': 'csv', 'export_type': 'queue',
                            'draw':1, 'length':-1, 'start':0};
          $("#_export_excel_queue_form").attr('action', site_url('/export_data/gameApiUpdateHistory'));
          $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
          $("#_export_excel_queue_form").submit();
        }
      }
    ],

    columnDefs: [
      { sortable: false, targets: [ 12,13 ] }
    ],
    order: [[0, 'desc']],

    // SERVER-SIDE PROCESSING
    processing: true,
    serverSide: true,
    ajax: function (data, callback, settings) {

      //console.log(data, callback, settings);

      data.extra_search = $('#form-filter').serializeArray();
       console.log(data.extra_search);

      $.post(base_url + "api/gameApiUpdateHistory", data, function(data) {

        callback(data);

        $('.pre-code').each(function(i, e) {
          hljs.highlightBlock(e)
        });

        $('.pre-code-container').each(function(i, e) {
          $(this).hide();
        })

        $('.code-holder').each(function(i, e) {
          $(this).prepend('<span class="show-code glyphicon glyphicon-chevron-down" style="padding-left: 50%;font-size:15px;cursor:pointer;" aria-hidden="true"></span>');
        });

        $('.show-code').each(function(i, e) {
          $(this).click(function(){
            $(this).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
            $(this).next('.pre-code-container').toggle();
          });
        });

        if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
            dataTable.buttons().disable();
        }
        else {
          dataTable.buttons().enable();
        }

      },'json');
    },

  });


   function ableDisableDate(){

    if(!$('input[name=enable_date]').is(':checked')){
      $('#date_from').attr('disabled','disabled');
      $('#date_to').attr('disabled','disabled');
      $('input[name=search_by]').attr('disabled','disabled');
      $('#dateRangePicker').prop('disabled', true);

    }else{
      $('#date_from').removeAttr('disabled');
      $('#date_to').removeAttr('disabled');
      $('input[name=search_by]').removeAttr('disabled');
      $('#dateRangePicker').prop('disabled', false);
    }

  }

ableDisableDate();

$('input[name=enable_date]').change(function() {
    ableDisableDate();
});


}); //end document ready.


</script>