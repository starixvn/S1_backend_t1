
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGameDescription" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseGameDescription" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
	    <input type="hidden" id="gameTypeIdHide" name="gameTypeIdHide" value="<?=$conditions['gameType']?>">
        <form id="form-filter" class="form-horizontal" method="get">
	        <div class="panel-body">
	            <div class="row">
                    <div class="col-md-2">
                        <label class="control-label" for="gamePlatformId"><?=lang('sys.gd7')?>:</label>
                        <select name="gamePlatform" id="gamePlatform" class="form-control">
                            <option value="N/A"><?=lang('Select Game Platform')?></option>
                            <?php foreach ($gameapis as $gameApi) { ?>
                                <option value="<?=($gameApi['id'])?>" <?=($gameApi['id']==$conditions['gamePlatform'])?'selected':''?>><?=$gameApi['system_code']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="gameType"><?=lang('sys.gd6')?>:</label>
                        <select name="gameType" id="gameType" class="form-control" disabled>
                            <option value=""><?= lang('Select Game Type'); ?></option>
                        </select>
                    </div>
	                <div class="col-md-2">
	                    <label class="control-label" for="gameName"><?=lang('sys.gd8')?>:</label>
	                    <input type="text" name="gameName" id="gameName" value="<?=($conditions['gameName']) ? $conditions['gameName']:''?>" class="form-control number_only"/>
	                </div>
	                <div class="col-md-2">
	                    <label class="control-label" for="gameCode[]"><?=lang('sys.gd9')?>:</label>
	                    <?php if ($this->utils->getConfig('show_non_active_game_api_game_list')): ?>
	                    	<input type="text" name="gameCode[]" value="<?=(empty($conditions['gameCode']))?null:$conditions['gameCode']?>" class="form-control">
						<?php else: ?>
                       	<select id="selectGameCode" class="multi-select-filter form-control" name="gameCode[]" multiple="multiple" style="width:100%;">
                       		<?php foreach ($gameCodes as $key => $gameCode): ?>
                       			<option value="<?=$gameCode?>"><?=lang($gameCode)?></option>
                       		<?php endforeach ?>
						</select>
	                    <?php endif ?>
	                </div>
	                <div class="col-md-4">
	                    <label class="control-label" for="filters[]"><?=lang('Game attributes');?>:</label>
                       	<select id="filters" class="multi-select-filter form-control" name="filters[]" multiple="multiple" style="width:100%;">
                       		<?php foreach ($filters as $key => $filter): ?>
                       			<option value="<?=$filter?>"><?=lang($key)?></option>
                       		<?php endforeach ?>
						</select>
	                </div>
	            </div>

	            <div class="row">
                    <div class="col-md-2">
                        <label class="control-label" for="gameStatus"><?=lang('sys.gd35')?>:</label>
                        <select name="gameStatus" id="gameStatus" class="form-control">
                        	<?php
                        		if($conditions['gameStatus'] === 'All' || $conditions['gameStatus'] === false || $conditions['gameStatus'] === '') {
                			?>
		                            <option value = 'All' selected=""><?=lang('Select All')?></option>
			                        <option value = '1'><?=lang('Enabled')?></option>
		                            <option value = '0'><?=lang('Disabled')?></option>
                            <?php
                        		} else if($conditions['gameStatus'] === '1') {
                			?>
		                            <option value = 'All'><?=lang('Select All')?></option>
			                        <option value = '1' selected=""><?=lang('Enabled')?></option>
		                            <option value = '0'><?=lang('Disabled')?></option>
                        	?>
                            <?php
                        		} else {
                			?>
		                            <option value = 'All'><?=lang('Select All')?></option>
			                        <option value = '1'><?=lang('Enabled')?></option>
		                            <option value = '0' selected=""><?=lang('Disabled')?></option>
                            <?php
		                        }
	                        ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="gameFlagShow"><?=lang('sys.gd36')?>:</label>
                        <select name="gameFlagShow" id="gameFlagShow" class="form-control">
                        	<?php
                        		if($conditions['gameFlagShow'] === 'All' || $conditions['gameFlagShow'] === false || $conditions['gameFlagShow'] === '') {
                			?>
		                            <option value = 'All' selected=""><?=lang('Select All')?></option>
			                        <option value = '1'><?=lang('Enabled')?></option>
		                            <option value = '0'><?=lang('Disabled')?></option>
                            <?php
                        		} else if($conditions['gameFlagShow'] === '1') {
                			?>
		                            <option value = 'All'><?=lang('Select All')?></option>
			                        <option value = '1' selected=""><?=lang('Enabled')?></option>
		                            <option value = '0'><?=lang('Disabled')?></option>
                        	?>
                            <?php
                        		} else {
                			?>
		                            <option value = 'All'><?=lang('Select All')?></option>
			                        <option value = '1'><?=lang('Enabled')?></option>
		                            <option value = '0' selected=""><?=lang('Disabled')?></option>
                            <?php
		                        }
	                        ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="gameId"><?=lang('lang.external.game.id')?>:</label>
                        <input type="text" name="gameId" id="gameId" value="<?=($conditions['gameId']) ? $conditions['gameId']:''?>" class="form-control number_only"/>
                    </div>

	            </div>
	        </div>
	        <div class="panel-footer text-right">
	             <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
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
                    <?=lang('sys.gd0');?>
					<?php if ($this->permissions->checkPermissions('game_description_history')): ?>
	                    <a href="<?=base_url('game_description/viewGameDescriptionHistory')?>" class="goto btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-left:4px;margin-top:0px"' : ''?>>
	                        <i class="glyphicon glyphicon-list-alt" data-placement="bottom"></i>
	                        <?=lang('Go to Game List History');?>
	                    </a>
                    <?php endif ?>

                    <button type="button" value="" id="addGameDesc" name="btnSubmit" class="btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-left:4px;margin-top:0px"' : ''?>>
                        <i class="glyphicon glyphicon-plus" data-placement="bottom"></i>
                        <?=lang('sys.gd22');?>
                    </button>
                    <?php if ($this->utils->getConfig('allow_batch_game_list_update')): ?>
                    <button type="button" id="batchAddUpdateGameDesc" data-toggle="modal" data-target="#batchAddUpdateModal" class="btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-top:0px"' : ''?>>
                        <i class="glyphicon glyphicon-plus" data-placement="bottom"></i>
                        <?=lang('Batch Add/Update Game Description');?>
                    </button>
                	<?php endif; ?>

                	<button type="button" id="batchUpdateGameDesc" data-toggle="modal" data-target="#batchUpdateModal" class="btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-top:0px"' : ''?>>
                        <i class="glyphicon glyphicon-plus" data-placement="bottom"></i>
                        <?=lang('Batch Update Active Games');?>
                    </button>


                </h3>
                </h3>
            </div>

			<div class="panel-body" id="list_panel_body">
				<div class="table-responsive">
				<div class="clearfix"></div>
					<table class="table table-bordered table-hover dataTable" id="my_table" >
						<thead>
							<tr>
								<th><?=lang('sys.gd33');?></th>
								<th><?=lang('lang.english.name');?></th>
								<th><?=lang("sys.gd6");?></th>
								<th><?=lang("sys.gd7");?></th>
								<th><?=lang("sys.gd8");?></th>
								<th><?=lang("sys.gd9");?></th>
                                <th><?=lang("Atrributes");?></th>
                                <th><?=lang("sys.gd10");?></th>
                                <th><?=lang("Mobile");?></th>
                                <th><?=lang("Flash");?></th>
                                <th><?=lang("HTML5");?></th>
                                <th><?=lang("Available on IOS");?></th>
								<th><?=lang("Available on Android");?></th>
								<th><?=lang("Desktop App");?></th>
								<th><?=lang("Available offline");?></th>
								<th><?=lang("New Game");?></th>
								<th><?=lang("sys.gd11");?></th>
								<?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
								<th><?=lang("sys.gd18");?></th>
								<?php endif; ?>
								<th><?=lang("sys.gd19");?></th>
								<th><?=lang("sys.gd16");?></th>
								<th><?=lang("sys.gd17");?></th>
                                <th><?=lang("sys.gd20");?></th>
                                <th><?=lang("Created At");?></th>
                                <th><?=lang("Last Update");?></th>
                                <th><?=lang("Deleted At");?></th>
                                <th><?=lang('Locked Flag')?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>

			<div class="panel-footer"></div>

		</div>
	</div>

     <!-- The Modal -->
    <div class="modal" id="batchAddUpdateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?=lang('Batch Add/Update of Game List')?>
	                    <button type="button" class="close" data-dismiss="modal">&times;</button>
	                </h4>
                </div>
                    <!-- Modal body -->
                <div class="modal-body">
                    <form id="game-settings-form" class="form-horizontal" action="<?=base_url('/game_description/postBatchInsertUpdateGameList')?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <p class="text-danger"><?=lang('Note: Chinese characters should be utf-8 encoded before addin the batch or else it will ignore the chinese chars')?></p class="text-danger">
                        <div class="form-group">
                            <label class="col-md-3"><?=lang('CSV File')?></label>
                            <div class="col-md-8">
                                <input name="games" class="user-error" aria-invalid="true" type="file" accept=".csv">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3"><?=lang('Game Platform')?></label>
                            <div class="col-md-8">
                                <select name="game_platform_id" id="game_platform_id_select">
                                    <?php foreach ($gameapis as $key => $gameApi): ?>
                                        <option value="<?=$gameApi['id']?>"><?=$gameApi['system_code']." [".$gameApi['id']."]"?></option>
                                    <?php endforeach ?>
                                </select>
                                <input class="form-check-input" type="checkbox" name="game_platform_checkbox" id="game_platform_checkbox">
                                <input type="number" name="game_platform_id_num" id="game_platform_id_num" disabled>
                            </div>
                        </div>
                    </form>
                </div>
                    <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" id="batchAddUpdateModalSubmit" class="btn btn-info"><?=lang("Submit")?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

     <!-- The Modal for BATCH UPDATE OF ACTIVE GAMES -->
    <div class="modal" id="batchUpdateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?=lang('Batch Update Active Games')?>
	                    <button type="button" class="close" data-dismiss="modal">&times;</button>
	                </h4>
                </div>
                    <!-- Modal body -->
                <div class="modal-body">
                    <form id="game-update-active-form" class="form-horizontal" action="<?=base_url('/game_description/postBatchUpdateActiveGameList')?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <p class="text-danger"><?=lang('Note: Use CSV Format with 1 column only for Game Codes')?></p class="text-danger">
                        <div class="form-group">
                            <label class="col-md-3"><?=lang('CSV File')?></label>
                            <div class="col-md-8">
                                <input name="games" class="user-error" aria-invalid="true" type="file" accept=".csv">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3"><?=lang('Game Platform')?></label>
                            <div class="col-md-8">
                                <select name="game_platform_id" id="game_platform_id_select">
                                    <?php foreach ($gameapis as $key => $gameApi): ?>
                                        <option value="<?=$gameApi['id']?>"><?=$gameApi['system_code']." [".$gameApi['id']."]"?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                    <!-- Modal footer -->
                <div class="modal-footer">
					<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>" data-dismiss="modal">Close</button>
                    <button type="button" id="batchUpdateModalSubmit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"><?=lang("Submit")?></button>
                </div>
            </div>
        </div>
    </div>




	<!----------------EDIT-FORM Game Details start---------------->
	<div class="col-md-5" id="edit_game_description_details">

		<div class="panel panel-info <?=$this->utils->getConfig('use_new_sbe_color') ? 'panel-primary' : ''?>">
			<div class="panel-heading <?=$this->utils->getConfig('use_new_sbe_color') ? 'custom-ph' : ''?>">
				<h4 class="panel-title">
					<i class="icon-pencil"></i> <span id="add-edit-panel-title"></span>
					<a href="#close" class="btn pull-right panel-button <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-primary btn-sm'?>" id="closeDetails" ><span class="glyphicon glyphicon-remove"></span></a>
				</h4>

				<div class="clearfix"></div>
			</div>
			<div class="panel panel-body" id="details_panel_body">

				<form method="post" role="form" id="game-description-form">
					<div class="form-group">
						<input type="hidden" id="gd_id" name="gd_id" >
						<label for="game"><?=lang('sys.gd5');?></label>
						<select name="game_platform_id" id="game_platform_id" class="form-control input-sm"></select>
					</div>
					<div class="form-group">
						<label for="game"><?=lang('sys.gd6');?></label>
						<select name="game_type_id" id="game_type_id" class="form-control input-sm" disabled></select>
					</div>
                    <div class="form-group " id="game_name_label">
                        <label for="game_name"><?=lang('sys.gd8');?></label>
                        <input type="checkbox" id="game_name_use_json" name="game_name_use_json" value="1" /> <label for="game_name_use_json"><?=lang('Use JSON');?></label>
                        <input type="text" value="" class="form-control" id="game_name" name="game_name">
                        <pre class="form-control" id="game_name_editor" name="game_name_editor"></pre>
                        <!-- <select name="game_name" id="game_name" class="form-control input-sm"></select> -->
                    </div>
					<div class="form-group " id="game_attributes_label">
						<label for="game_attributes" ><?=lang('Attributes');?></label>
						<input type="checkbox" id="game_attributes_use_json" name="game_attributes_use_json" value="1" /> <label for="game_attributes_use_json"><?=lang('Use JSON');?></label>
						<input type="text" value="" class="form-control" id="game_attributes" name="game_attributes">
						<pre class="form-control" id="game_attributes_editor" name="game_attributes_editor"></pre>
						<!-- <select name="game_attributes" id="game_attributes" class="form-control input-sm"></select> -->
					</div>

					<div class="form-group">
						<label for="game_code"><?=lang('sys.gd9');?></label>
						<input type="text"  value="" class="form-control"  id="game_code" name="game_code">
					</div>
					<div class="form-group">
						<label for="external_game_id"><?=lang('lang.external.game.id');?></label>
						<input type="text" value="" class="form-control" id="external_game_id" name="external_game_id">
					</div>
					<div class="form-group">
						<label for="english_name"><?=lang('lang.english.name');?></label>
						<input type="text" value="" class="form-control" id="english_name" name="english_name">
					</div>
					<div class="form-group">
						<label for="note"><?=lang('sys.gd11');?></label>
						<textarea class="form-control"id="note"  maxlength="1000" name="note"rows="5"></textarea>
					</div>
					<div class="form-group">
						<label for="no_cash_back"><?=lang('sys.gd18');?></label>
						<input type="text" value="" maxlength="1" class="form-control" id="no_cash_back" name="no_cash_back">
					</div>
					<div class="form-group">
						<label for="void_bet"><?=lang('sys.gd19');?></label>
						<input type="text" value="" class="form-control" id="void_bet" name="void_bet">
					</div>
					<div class="form-group">
						<label for="game_order"><?=lang('sys.gd20');?></label>
						<input type="number" value="" class="form-control" id="game_order" name="game_order">
					</div>
					<div class="form-group">
	                    <li class="list-group-item" >
	                        <div class="label-list"><?=lang('sys.gd10');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info">
	                        		<p><?=lang('Atrribute of the game if have Jackpot')?></p>
	                        	</div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="progressive" name="progressive" type="checkbox"/>
	                            <label for="progressive" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('Android');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on android')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="enabled_on_android" name="enabled_on_android" type="checkbox"/>
	                            <label for="enabled_on_android" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('IOS');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on IOS')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="enabled_on_ios" name="enabled_on_ios" type="checkbox"/>
	                            <label for="enabled_on_ios" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd12');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on desktop app')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="dlc_enabled" name="dlc_enabled" type="checkbox"/>
	                            <label for="dlc_enabled" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd13');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on Web')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="flash_enabled" name="flash_enabled" type="checkbox"/>
	                            <label for="flash_enabled" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('HTML5');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on Mobile and Web')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="html_five_enabled" name="html_five_enabled" type="checkbox"/>
	                            <label for="html_five_enabled" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd14');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available offline')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="offline_enabled" name="offline_enabled" type="checkbox"/>
	                            <label for="offline_enabled" class="label-success"></label>
	                        </div>
	                    </li>
						<li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd15');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on Mobile')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="mobile_enabled" name="mobile_enabled" type="checkbox"/>
	                            <label for="mobile_enabled" class="label-success"></label>
	                        </div>
	                    </li>
                        <!-- (Deprecated) Separate updates for these fields -->
						<!-- <li class="list-group-item">
	                        <div class="label-list"><?=lang('New Game');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is new')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="flag_new_game" name="flag_new_game" type="checkbox"/>
	                            <label for="flag_new_game" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd16');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Status of the game if disabled or not')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="status" name="status" type="checkbox"/>
	                            <label for="status" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item">
	                        <div class="label-list"><?=lang('sys.gd17');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info"><p><?=lang('Game attribute if the game is available on site')?></p></div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="flag_show_in_site" name="flag_show_in_site" type="checkbox"/>
	                            <label for="flag_show_in_site" class="label-success"></label>
	                        </div>
	                    </li> -->
					</div>
					<!----------NOTE: BUTTON TITLE WILL BE UPDATE THROUG JAVASCTRIPT---------------->
					<button id="add-update-button"  type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"></button>

				</form>


			</div>
		</div>


	</div>
	<!---------------EDIT FORM Game Details end---------------->

<div id="gameDescriptionHistory"  class="modal fade "  data-backdrop="static"
	data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content" >

			<div class="modal-header panel-heading">
				<h4 id=""><?=lang('Game History')?>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table id="gameDescriptionHistoryTable" class="table table-striped">
						<thead>
							<tr>
								<th id="id"><?=lang('id')?></th>
								<th id="game_description_id"><?=lang('Game Description Id')?></th>
								<th id="game_platform_id"><?=lang('Game Platform Id')?></th>
								<th id="action"><?=lang('Action')?></th>
								<th id="game_type_id"><?=lang('Game Type Id')?></th>
								<th id="game_name"><?=lang('Game Name')?></th>
								<th id="game_code"><?=lang('Game Code')?></th>
								<th id="attributes"><?=lang('Attributes')?></th>
								<th id="note"><?=lang('Note')?></th>
								<th id="english_name"><?=lang('English Name')?></th>
								<th id="external_game_id"><?=lang('External Game Id')?></th>
								<th id="clientid"><?=lang('Clientid')?></th>
								<th id="moduleid"><?=lang('Moduleid')?></th>
								<th id="sub_game_provider"><?=lang('Sub Game Provider')?></th>
								<th id="flash_enabled"><?=lang('Flash Enabled')?></th>
								<th id="status"><?=lang('Status')?></th>
								<th id="flag_show_in_site"><?=lang('Flag Show In Site')?></th>
								<th id="no_cash_back"><?=lang('No Cash Back')?></th>
								<th id="void_bet"><?=lang('Void Bet')?></th>
								<th id="game_order"><?=lang('Game Order')?></th>
								<th id="related_game_desc_id"><?=lang('Related Game Desc Id')?></th>
								<th id="dlc_enabled"><?=lang('Dlc Enabled')?></th>
								<th id="progressive"><?=lang('Progressive')?></th>
								<th id="enabled_freespin"><?=lang('Enabled Freespin')?></th>
								<th id="offline_enabled"><?=lang('Offline Enabled')?></th>
								<th id="mobile_enabled"><?=lang('Mobile Enabled')?></th>
								<th id="enabled_on_android"><?=lang('Android')?></th>
								<th id="enabled_on_ios"><?=lang('Ios')?></th>
								<th id="flag_new_game"><?=lang('Flag New Game')?></th>
								<th id="html_five_enabled"><?=lang('Html Five Enabled')?></th>
								<th id="demo_link"><?=lang('Demo Link')?></th>
								<th id="md5_fields"><?=lang('Md5 Fields')?></th>
								<th id="deleted_at"><?=lang('Deleted At')?></th>
								<th id="created_on"><?=lang('Created On')?></th>
								<th id="updated_at"><?=lang('Updated At')?></th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" id="cancel-delete"data-dismiss="modal"><?=lang('Close')?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$("#collapseSubmenuGameDescription").addClass("in");
$("a#view_game_description").addClass("active");
// $("a#viewGameListSettings").addClass("active");
var gameNameEditor;
var gameAttributesEditor;

$(document).ready(function(){

	// Init syntax highlight for JSON string in extra_info
	hljs.initHighlightingOnLoad();

	// Init ACE editor for JSON
    gameAttributesEditor = ace.edit("game_attributes_editor");
    gameAttributesEditor.setTheme("ace/theme/tomorrow");
    gameAttributesEditor.session.setMode("ace/mode/json");

    // Hide the ACE editor for JSON
    $('#game_attributes_editor').hide();

    // Implement switch between text field and rich editor
    $('#game_attributes_use_json').click(function(){
        if($(this).prop('checked')) {
            $('#game_attributes').hide();
            $('#game_attributes_editor').show();
            gameAttributesEditor.resize(); // so that the content refreshes
        } else {
            $('#game_attributes').show();
            $('#game_attributes_editor').hide();
        }
    });

    // Init ACE editor for JSON
	gameNameEditor = ace.edit("game_name_editor");
	gameNameEditor.setTheme("ace/theme/tomorrow");
	gameNameEditor.session.setMode("ace/mode/json");

	// Hide the ACE editor for JSON
	$('#game_name_editor').hide();

	// Implement switch between text field and rich editor
	$('#game_name_use_json').click(function(){
		if($(this).prop('checked')) {
			$('#game_name').hide();
			$('#game_name_editor').show();
			gameNameEditor.resize(); // so that the content refreshes
		} else {
			$('#game_name').show();
			$('#game_name_editor').hide();
		}
	});

	$('body').tooltip({
		selector: '[data-toggle="tooltip"]',
		placement: "bottom"
	});

	var dataTable = $('#my_table').DataTable({

		autoWidth: false,
		searching: false,
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
				<?php

                    if( $this->permissions->checkPermissions('export_game_description') ){

                ?>
			 {
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export_excel',
                action: function ( e, dt, node, config ) {
                    var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};


                    // utils.safelog(d);
                    $.post(site_url('/export_data/gameDescriptionList'), d, function(data){
                        // utils.safelog(data);

                        //create iframe and set link
                        if(data && data.success){
                            $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                        }else{
                            alert('export failed');
                        }
                    });
                }
            }

            <?php
            	}
            ?>
		],

		columnDefs: [
			{ sortable: false, targets: [ 0 ] }
		],
		order: [[1, 'asc']],

		// SERVER-SIDE PROCESSING
		processing: true,
		serverSide: true,
		ajax: function (data, callback, settings) {
			data.extra_search = $('#form-filter').serializeArray();
			data.extra_search[data.extra_search.length] = {
					'name' : 'gameTypeIdHide',
					'value' : $('#gameTypeIdHide').val()
			}
			// console.log(data.extra_search);

			$.post(base_url + "api/gameDescriptionList", data, function(data) {
                // console.log(data);
				callback(data);
				if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
				    dataTable.buttons().disable();
				}
				else {
					dataTable.buttons().enable();
				}
			},'json');
		},

	});

   $('#btn-submit').click( function() {
     dataTable.ajax.reload();
    });

});//end document ready.

/*Set Global Variables */
var  GET_GAMES_AND_GET_TYPES_URL ='<?php echo site_url('game_description/getGamesAndGameTypes') ?>/',
GET_GAME_DESCRIPTION_URL ='<?php echo site_url('game_description/editGameDescription') ?>/',
ADD_GAME_DESCRIPTION_URL ='<?php echo site_url('game_description/addGameDescription') ?>/',
UPDATE_GAME_DESCRIPTION_URL ='<?php echo site_url('game_description/updateGameDescription') ?>/',
DELETE_GAME_DESCRIPTION_URL ='<?php echo site_url('game_description/deleteGameDescription') ?>/',
UPDATE_GAME_DESCRIPTION_STATUS_URL ='<?php echo site_url('game_description/updateGameDescriptionStatus') ?>/',
REFRESH_PAGE_URL = '<?php echo site_url('game_description/viewGameDescription') ?>',
GET_GAME_DESCRIPTION_HISTORY = '<?php echo site_url('game_description/getGameDescriptionHistory') ?>';
var game_description_url = '<?php echo site_url('game_description/postBatchInsertUpdateGameList')?>';
var baseUrl = "<?=base_url()?>";
var message = {
    gameType        : '<?= lang('Select Game Type'); ?>'
};
var gameTypeData = "<?=$conditions['gameType']?>",
	gamePlatformId = "<?=$conditions['gamePlatform']?>",
	gameTypeId = '<?=isset($conditions['gameType']) ?$conditions['gameType']:""?>';

var LANG ={
	ADD_PANEL_TITLE: "<?=lang('sys.gd38');?>",
	EDIT_PANEL_TITLE:"<?=lang('sys.gd2');?>",
	ADD_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('Add new game');?>",
	UPDATE_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('sys.gd3');?>",
	DELETE_CONFIRM_MESSAGE:"<?=lang('sys.gd4');?>",
	EDIT:"<?=lang('sys.gd23');?>",
	EDIT_COLUMN:"<?=lang('sys.gd24');?>",
	DELETE_ITEMS:"<?=lang('sys.gd21');?>",
	ADD_GAME_DESC:"<?=lang('sys.gd38');?>"
};

<?php if ($this->utils->getConfig('show_non_active_game_api_game_list')) : ?>
var gameCodes = <?=($conditions['gameCode']) ?"'".$conditions['gameCode']."'":0?>;
<?php else: ?>
var gameCodes = <?=($conditions['gameCode']) ?$conditions['gameCode']:0?>;
<?php endif; ?>

var filters = <?php echo $conditions['filters'];?>;
var show_non_active_game_api_game_list = <?=($this->utils->getConfig('show_non_active_game_api_game_list'))?true:0?>;

function log_game_type_id(id){
// console.log(id);
}

</script>