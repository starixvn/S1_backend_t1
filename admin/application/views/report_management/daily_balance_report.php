<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-info btn-xs"></a>
            </span>
            <i class="fa fa-search"></i> Search 
        </h4>
    </div>
    <div class="panel-collapse">
        <div class="panel-body">
        	<form class="form">
        		<?php if(!empty($limit)) : ?>
        			<label class="control-label" style="font-style: italic; color: red;"><?= sprintf(lang('Note: Report limit %s data to display.'),$limit) ?></label>
        		<?php endif; ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('Date')?> : </label>
                            <input type="text" name="date" class="form-control" value="<?=$date?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('Player Username')?> : </label>
                            <input type="text" name="username" class="form-control" value="<?=$username?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('exclude_player')?> : </label>
                            <select name="tag_list[]" id="tag_list" multiple="multiple" class="form-control">
                                <?php foreach ($tags as $key) {?>
                                    <option value="<?=$key['tagId']?>" <?=is_array($selected_tags) && in_array($key['tagId'], $selected_tags) ? "selected" : "" ?>><?=$key['tagName']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">&nbsp;</label>
                            <br/>
                            <button type="submit" class="btn btn-primary">Search</button>
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
			<?=lang('Daily Player Balance Report')?>
		</h4>
	</div>
	<div class="panel-body" style="overflow-x: scroll;">
		<table class="table table-condensed table-bordered table-hover">
			<thead>
				<tr>
					<th><?=lang('Date')?></th>
					<th><?=lang('Player Username')?></th>
					<th><?=lang('Main Wallet Balance')?></th>
					<?php foreach ($game_platforms as $game_platform): ?>
						<th><?=$game_platform['system_name'] . ' ' . lang('Balance')?></th>
					<?php endforeach ?>
					<th><?=lang('Total Balance')?></th>
					<th><?=lang('Last Update')?></th>
				</tr>
			</thead>
			<tbody class="daily-balance-table-body" style="display: none">
				<?php foreach ($rows as $row): ?>
					<tr>
						<td><?=$row['game_date']?></td>
						<td><?=$row['username']?></td>
						<td align="right"><?=$this->utils->formatCurrencyNoSym($row['main_wallet'])?></td>
						<?php foreach ($game_platforms as $game_platform): ?>
							<td align="right"><?=$this->utils->formatCurrencyNoSym($row['sub_wallet'][$game_platform['id']])?></td>
						<?php endforeach ?>
						<td align="right"><?=$this->utils->formatCurrencyNoSym($row['total_balance'])?></td>
						<td align="right"><?=$row['updated_at']?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript" src="<?=site_url().'resources/datatables/dataTables.buttons.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/jszip.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/buttons.html5.min.js'?>"></script>
<script type="text/javascript">
	$(function(){

		var dataTable = $('.table').DataTable({
			<?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
			dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
			"<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
			"<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			buttons: [
				{
                    extend: 'colvis'
                },
				<?php if( $this->permissions->checkPermissions('daily_player_balance_report') ){ ?>
				{
					extend: 'csvHtml5', // csvHtml5 // excelHtml5 // excelHtml5
					exportOptions: {
						columns: ':visible'
					},
					className:'btn btn-sm btn-primary',
					text: '<?=lang('CSV Export')?>',
					filename:  '<?=lang('Daily Player Balance Report')?>',
					action: function ( e, dt, node, config) {
						let data = {
							  	date: document.querySelector('input[name="date"]').value,
								player_name: document.querySelector('input[name="username"]').value,
								tag_list : <?=json_encode($selected_tags); ?>,
								filename : '<?=lang('Daily Player Balance Report')?>'
			                };
						$.ajax({
						  url: '/api/daily_player_balance_report',
						  type: 'POST',
						  data: data
						});
						$.fn.DataTable.ext.buttons.csvHtml5.action.call(this, e, dt, node, config);
					}
				}
				<?php } ?>
			],
			'order': [[ 0, 'desc' ], [ 1, 'asc' ]],
			drawCallback: function () {
				if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					$(".dt-buttons, .dataTables_filter").remove();
				}
				$(".daily-balance-table-body").show();
            }
		});

		$(document).on("click",".buttons-colvis",function(){
			$("#DataTables_Table_0 th").each(function(){
			    var text = $(this).text();
			    $(".buttons-columnVisibility:contains("+text+")").addClass('active');
			});
		});

		$(document).on("click",".buttons-columnVisibility",function(){
			var active = $(this).hasClass('active');
			if(active){
				$(this).removeClass('active');
			} else{
				$(this).addClass('active');
			}
		});


        $(document).ready(function() {
            $('#tag_list').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                selectAllJustVisible: false,
                buttonWidth: '350px',
                buttonText: function(options, select) {
                    if (options.length === 0) {
                        return 'Select Tags';
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

	});
</script>

