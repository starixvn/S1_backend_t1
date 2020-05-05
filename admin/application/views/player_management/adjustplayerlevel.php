<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="icon-stats-bars2"></i> <?=lang('player.apl01');?> </h4>
        <a href="#close" class="btn btn-default btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="col-md-12">
        	<form action="<?=BASEURL . 'player_management/doAdjustPlayerLevel'?>" method="post" role="form">
            <table class="table table-striped table-hover table-responsive" id="myTable">
                <thead>
                    <tr>
                        <th class="col-md-2"><?=lang('player.apl02');?></th>
                        <th class="col-md-2"><?=lang('player.apl03');?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    	<td class="col-md-2">
                    		<input type="hidden" name="playerId" value="<?=$playerId?>" />
                    		<b>(<?=lang($playerCurrentLevel[0]['groupName']) . ')</b> - ' . lang($playerCurrentLevel[0]['vipLevelName'])?>
                    	</td>
                    	<td class="col-md-2">
                           <select name="newPlayerLevel" id="paymentReportSortByPlayerLevel" class="form-control input-sm">
                                    <?php foreach ($allLevels as $key => $value) {?>
                                        <option value="<?=$value['vipsettingcashbackruleId']?>"><?=lang($value['groupName']) . ' - ' . lang($value['vipLevelName'])?></option>
                                    <?php }
?>
                                   </select>
                    	</td>
                    	<td class="col-md-1">
    						<input type="submit" class="btn btn-sm btn-info" value="<?=lang('lang.save');?>" />
                    	</td>
                    </tr>
                </tbody>
            </table>
           	</form>
        </div>
    </div>
</div>