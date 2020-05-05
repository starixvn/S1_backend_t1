<div class="container">
	<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>

		<form class="form-horizontal" id="search-form">

			<input type="hidden" name="affiliate_id" value="<?=$conditions['affiliate_id']?>"/>

		    <div class="panel panel-primary">
		        <div class="panel-heading">
		            <h4 class="panel-title">
		                <i class="fa fa-search"></i> <?=lang("lang.search")?>
		                <span class="pull-right">
		                    <a data-toggle="collapse" href="#collapseViewGameLogs"
		                        class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
		                    </a>
		                </span>
		            </h4>
		        </div>

		        <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		            <div class="panel-body">
		                <div class="col-md-6">
		                    <label class="control-label" for="search_game_date"><?=lang('report.sum02');?></label>
		                    <input id="search_game_date" class="form-control input-sm dateInput" data-start="#start_date" data-end="#end_date"/>
		                    <input type="hidden" id="start_date" name="start_date" value="<?php echo $conditions['start_date']; ?>" />
		                    <input type="hidden" id="end_date" name="end_date"  value="<?php echo $conditions['end_date']; ?>"/>
		                </div>

		                <div class="col-md-6">
		                    <label class="control-label" for="game_platform_id"><?=lang('player.ui29');?> </label>
		                    <div class="row">
		                        <?php foreach ($game_platforms as $game_platform) {?>
			                    	<div class="col-md-4">
					                    <label>
					                    	<input type="checkbox" name="game_platform_id[]" value="<?=$game_platform['id']?>" <?=in_array($game_platform['id'], $conditions['game_platform_id']) ? 'checked="checked"' : ''?>>
					                    	<?=$game_platform['system_code'];?>
					                    </label>
				                    </div>
		                        <?php }?>
		                    </div>
		                </div>

		            </div>
		            <div class="panel-footer text-right">
		                <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
		            </div>
		        </div>
		    </div>

		</form>
	<?php endif ?>
	<div class="panel panel-primary">

		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('IP History');?></h4>
		</div>

		<div class="panel-body">
			<div class="well">
				<div class="input-group">
					<span class="input-group-addon">
						<?=lang('Player Username')?>
					</span>
					<input type="text" class="form-control" value="<?=$player['username']?>" readonly="readonly" style="background-color: #fff; cursor: not-allowed;">
				</div>
			</div>
	        <table id="ip-table" class="table table-striped table-hover">
	            <thead>
	                <th><?=lang('traffic.playerip');?></th>
	                <th><?=lang('player.sd08');?></th>
	                <th><?=lang('pay.referrer');?></th>
	                <th><?=lang('pay.useragent');?></th>
	                <th><?=lang('player.ub01');?></th>
	            </thead>
	        </table>
		</div>

	</div>


</div>
<script type="text/javascript">
    $(document).ready(function() {
		$('#ip-table').DataTable({
		    autoWidth: false,
		    searching: true,
		    processing: true,
		    serverSide: true,
		    ajax: function (data, callback, settings) {
		        data.extra_search = $('#changeable_table #search-form').serializeArray();
		        $.post(base_url + 'api/ip_history/' + <?=$player['playerId']?>, data, function(data) {
		            callback(data);
		        },'json');

		    }
		});
    });
</script>