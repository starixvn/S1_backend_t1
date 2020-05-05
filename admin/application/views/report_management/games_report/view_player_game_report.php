<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="icon-search" id="hide_main_up"></i> <?=lang('lang.search')?>
			<a href="#main" 
              class="btn btn-default btn-sm pull-right hide_sortby">
				<i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
			</a>
			<div class="clearfix"></div>
		</h4>
	</div>
	<div class="panel-body sortby_panel_body">
		<form id="form-filter" class="form-horizontal" method="post">
			<div class="col-md-3">
				<label class="control-label" for="total_bet_from"><?=lang('system.word38')?> </label>
				<input type="text" class="form-control" value="<?=$username?>" disabled="disabled"/>
			</div>
			<div class="col-md-3">
				<label class="control-label"><?=lang('report.sum02')?></label>
                <input class="form-control dateInput" data-start="#date_from" data-end="#date_to"/>
                <input type="hidden" id="date_from" name="date_from"/>
                <input type="hidden" id="date_to" name="date_to"/>
            </div>
			<div class="col-md-3">
				<label class="control-label" for="total_bet_from"><?=lang('report.g09') . " <= "?> </label>
				<input type="text" name="total_bet_from" id="total_bet_from" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="total_bet_to"><?=lang('report.g09') . " >= "?> </label>
				<input type="text" name="total_bet_to" id="total_bet_to" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="total_loss_from"><?=lang('report.g11') . " <= "?> </label>
				<input type="text" name="total_loss_from" id="total_loss_from" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="total_loss_to"><?=lang('report.g11') . " >= "?> </label>
				<input type="text" name="total_loss_to" id="total_loss_to" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="total_gain_from"><?=lang('report.g10') . " <= "?> </label>
				<input type="text" name="total_gain_from" id="total_gain_from" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="total_gain_to"><?=lang('report.g10') . " >= "?> </label>
				<input type="text" name="total_gain_to" id="total_gain_to" class="form-control number_only"/>
			</div>
			<div class="col-md-3">
				<label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
				<select name="group_by" id="group_by" class="form-control">
					<option value="total_player_game_day.game_description_id"><?=lang('sys.gd5')?></option>
					<option value="total_player_game_day.game_type_id"><?=lang('sys.gd6')?></option>
                </select>
            </div>
			<div class="col-md-1" style="text-align:center;padding-top:24px;">
				<input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn col-md-12 btn-info btn-sm">
			</div>
		</form>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="icon-dice"></i> <?=lang('report.s07')?> </h4>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<th><?=lang('sys.gd5')?></th>
					<th><?=lang('sys.gd6')?></th>
					<th><?=lang('report.g09')?></th>
					<th><?=lang('report.g10')?></th>
					<th><?=lang('report.g11')?></th>
					<th><?=lang('sys.rate')?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({

            autoWidth: false,
            searching: false,
            dom: "<'panel-body'l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            columnDefs: [
                { className: 'text-right', targets: [ 2,3,4,5 ] },
            ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/gameReports/<?=$player_id?>", data, function(data) {
                	if ($('#group_by').val() == 'total_player_game_day.game_description_id') {
                		dataTable.column( 0 ).visible( true );
                		dataTable.column( 0 ).order('asc');
                	} else {
                		dataTable.column( 0 ).visible( false );
                		dataTable.column( 1 ).order('asc');
                	}
                    callback(data);
                }, 'json');
            },
        });

         $('#form-filter').submit( function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });
    });
</script>
