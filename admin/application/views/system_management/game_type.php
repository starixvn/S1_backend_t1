
<div class="row" id="user-container">
	<div class="col-md-12">
		<div class="panel panel-primary hidden">

		    <div class="panel-heading">
		        <h4 class="panel-title">
		            <i class="fa fa-search"></i> <?=lang("lang.search")?>
		            <span class="pull-right">
		                <a data-toggle="collapse" href="#collapseViewGameLogs" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
		            </span>
		        </h4>
		    </div>

		    <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		        <div class="panel-body">
					<form class="form-horizontal" id="search-form">

						<div class="col-md-2">
							<label class="control-label" for="game_platform_id"><?=lang('player.ui29');?> </label>
							<select class="form-control input-sm" name="game_platform_id" id="game_platform_id" required="">
								<option value=""><?=lang('lang.selectall');?></option>
								<?php foreach ($game_platforms as $game_platform) {?>
									<option value="<?=$game_platform['id']?>"><?=$game_platform['system_code'];?></option>
								<?php }?>
			                </select>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="game_type"><?=lang('sys.gt6')?></label>
							<input id="game_type"  name="game_type" class="form-control input-sm"/>
						</div>
			        </form>
				</div>
			   	<div class="panel-footer text-right">
			   		<button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" id="btn-submit"><?=lang('lang.search');?></button>
			    </div>
			</div>
		</div>
	</div>
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt" >
					<i class="icon-list"></i>
					<?=lang('sys.gt0');?>
					<button type="button" value="" id="addGameType" name="btnSubmit" class="btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-top:0px;"' : ''?>>
						<i class="glyphicon glyphicon-plus" style="color:white;" data-placement="bottom" ></i>
						<?=lang('sys.gt22');?>
					</button>
					<?php if ($this->permissions->checkPermissions('game_type_history')): ?>
						<a href="<?=base_url('game_type/viewGameTypeHistory')?>" name="btnSubmit" class="go-back btn btn-primary pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-right:4px;margin-top:0px;"' : ''?>>
							<i class="glyphicon glyphicon-list-alt" style="color:white;" data-placement="bottom" ></i>
							<?=lang('Game Type History');?>
						</a>
					<?php endif ?>
				</h3>

			</div>

			<div class="panel-body" id="list_panel_body">
				<form  autocomplete="on" id="my_form">
					<div class="table-responsive">
						<table class="table table-bordered table-hover dataTable" style="width:100%;" id="my_table" >
							<thead>
								<tr>
									<th><?=lang('sys.gt33');?></th> <!-- Action -->
									<th><?=lang("sys.gt7");?></th> <!-- Game Platform -->
									<th><?=lang("sys.gt6");?></th> <!-- Game Type -->
									<th><?=lang("sys.gt8");?></th> <!-- Language Code -->
									<th><?=lang("sys.gt11");?></th> <!-- Note -->
									<th><?=lang("Game Type Code");?></th> <!-- Game Type Code -->
									<th><?=lang("sys.gt16");?></th> <!-- Status -->
									<th><?=lang("sys.gt17");?></th> <!-- Flag show in site -->
									<th><?=lang("sys.gt19");?></th> <!-- Order ID -->
									<th><?=lang("sys.gt18");?></th> <!-- Auto add new game -->
									<?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
									<th><?=lang("sys.gt34");?></th> <!-- Auto add cashback -->
							    	<?php endif; ?>
									<th><?=lang("pay.createdon");?></th> <!-- created on -->
								</tr>
							</thead>
						</table>
					</div>
				</form>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>

	<div class="col-md-5" id="edit_game_type_details" style="display: none;">
		<div class="panel panel-info <?=$this->utils->getConfig('use_new_sbe_color') ? 'panel-primary' : ''?>">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title">
					<i class="icon-pencil"></i> <span id="add-edit-panel-title"></span>
					<a href="javascript:void(0)" class="btn pull-right panel-button <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-primary btn-sm'?>" id="closeDetails" ><span class="glyphicon glyphicon-remove"></span></a>
				</h4>
				<div class="clearfix"></div>
			</div>
			<div class="panel panel-body" id="details_panel_body">
				<form method="post" role="form" id="game-type-form">

					<div class="form-group form-group-sm">
						<i class="text-danger"><?=lang('reg.02')?></i>
					</div>

					<div class="form-group">
						<input type="hidden" id="gt_id" name="id" >
						<label for="game_platform"><?=lang('sys.gt7');?> <span class="text-danger" id = "">*</span> <span class="text-danger" id = "required_game_platform"></span></label> <!-- Game Platform -->
						<select name="game_platform_id" id="game_platform" class="form-control input-sm" required=""></select>
					</div>
					<div class="form-group">
						<label for="game_type"><?=lang('sys.gt6');?> <span class="text-danger" id = "">*</span> <span class="text-danger" id = "required_game_type_name"></span></label> <!-- Game Type -->
						<input id="game_type_name" name="game_type" class="form-control" type="text" value="" required="" />
					</div>
					<div class="form-group">
						<label for="game_type_lang"><?=lang('sys.gt8');?> <span class="text-danger" id = "">*</span> <span class="text-danger" id = "required_game_type_lang"></span></label> <!-- Language Code -->
						<input id="game_type_lang" name="game_type_lang" class="form-control" type="text" value="" required="" />
					</div>
                    <div class="form-group">
                        <label for="game_type_code"><?=lang('Game Type Code');?> <span class="text-danger" id ="">*</span> <span class="text-danger" id ="required_game_type_code"></span></label> <!-- Language Code -->
                        <input id="game_type_code" name="game_type_code" class="form-control" type="text" value="" required="" />
                    </div>
					<div class="form-group">
						<label for="order_id"><?=lang('sys.gt19');?></label> <!-- Order ID -->
						<input id="order_id" name="order_id" class="form-control" type="text" value=""/>
						<input id="game_tag_id" name="game_tag_id" class="form-control" type="hidden" value=""/>
					</div>
					<div class="form-group">
						<label for="note"><?=lang('sys.gt11');?></label> <!-- Note -->
						<textarea id="note" name="note" class="form-control" maxlength="1000" rows="5"></textarea>
					</div>
					<div class="form-group">
	                    <li class="list-group-item" >
	                        <div class="label-list"><?=lang('sys.gt18');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info">
	                        		<p><?=lang('sys.gt18')?></p>
	                        	</div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="auto_add_new_game" name="auto_add_new_game" type="checkbox"/>
	                            <label for="auto_add_new_game" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item" >
	                        <div class="label-list"><?=lang('sys.gt34');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info">
	                        		<p><?=lang('sys.gt34')?></p>
	                        	</div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="auto_add_to_cashback" name="auto_add_to_cashback" type="checkbox"/>
	                            <label for="auto_add_to_cashback" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item" >
	                        <div class="label-list"><?=lang('sys.gt16');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info">
	                        		<p><?=lang('Status if the game type is disabled or not')?></p>
	                        	</div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="status" name="status" type="checkbox"/>
	                            <label for="status" class="label-success"></label>
	                        </div>
	                    </li>
	                    <li class="list-group-item" >
	                        <div class="label-list"><?=lang('sys.gt17');?></div>
	                        <div class="info-tooltip-wrapper">
	                        	<span class="glyphicon glyphicon-info-sign"></span>
	                        	<div class="tooltip-info">
	                        		<p><?=lang('Flag if the game type is available on website')?></p>
	                        	</div>
	                        </div>
	                        <div class="material-switch pull-right">
	                            <input id="flag_show_in_site" name="flag_show_in_site" type="checkbox"/>
	                            <label for="flag_show_in_site" class="label-success"></label>
	                        </div>
	                    </li>
	                 </div>
					<button id="add-update-button" type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"></button>
				</form>
			</div>
		</div>
	</div>

	<div id="modal_column2" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false"
		 tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel">Confirmation Delete</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="help-block" id="conf-msg">

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" id="cancel-delete"data-dismiss="modal">Cancel</button>
					<button type="button" id="confirm-delete" class="btn btn-primary">Yes</button>
				</div>
			</div>
		</div>
	</div>
	<div id="viewGameTypeHistoryModal" class="modal fade  data-backdrop="static" data-keyboard="false"
		 tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel"><?=lang('Game Type History')?></h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 <?=$this->utils->getConfig('use_new_sbe_color') ? 'table-responsive' : ''?>">
							<table id="gameTypeHistoryTable" class="table table-striped">
								<thead>
									<tr>
										<th id="gt-game_type_id"><?=lang('Game Type Id')?></th>
										<th id="gt-game_platform_id"><?=lang('Game Platform Id')?></th>
										<th id="gt-type"><?=lang('Action Type')?></th>
										<th id="gt-game_type"><?=lang('Game Type')?></th>
										<th id="gt-game_type_lang"><?=lang('Language Code')?></th>
										<th id="gt-note"><?=lang('Note')?></th>
										<th id="gt-status"><?=lang('Status')?></th>
										<th id="gt-flag_show_in_site"><?=lang('Flag Show in Site')?></th>
										<th id="gt-order_id"><?=lang('Order ID')?></th>
										<th id="gt-auto_add_new_game"><?=lang('Auto Add New Game')?></th>
										<th id="gt-related_game_type_id" class=""><?=lang('Related Game type')?></th>
										<th id="gt-auto_add_to_cashback"><?=lang('Auto Add Cashback')?></th>
										<th id="gt-game_type_code"><?=lang('Related Game type')?></th>
										<th id="gt-game_tag_id"><?=lang('Game Tag Id')?></th>
										<th id="gt-created_on"><?=lang('Created at')?></th>
										<th id="gt-updated_at"><?=lang('Updated at')?></th>
										<th id="gt-md5_fields" class=""><?=lang('MD5')?></th>
										<th id="gt-deleted_at"><?=lang('Deleted at')?></th>
									</tr>
								</thead>
								<tbody id="gameTypeHistoryTableBody">

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" id="cancel-delete"data-dismiss="modal"><?=lang('Close')?></button>
				</div>
			</div>
		</div>
	</div>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
	<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");

	$(document).ready(function(){
		// Add tooltip to sidebar menu items (TODO: Move this to sidebar)
		$('body').tooltip({
			selector: '[data-toggle="tooltip"]',
			placement: "bottom"
		});

		// Initialize DataTable jQuery plugin on the main table
		var dataTable = $('#my_table').DataTable({
			dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			autoWidth: false,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

			buttons: [
				{ extend: 'colvis', postfixButtons: [ 'colvisRestore' ], className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'},
				<?php

                    if( $this->permissions->checkPermissions('export_game_type') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                // utils.safelog(d);

                                $.post(site_url('/export_data/getAllGameType'), d, function(data){
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
				{ sortable: false, targets: [ 0 ] },
			],
			"order": [[ 1, 'asc' ]],
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();
				$.post(base_url + "game_type/getAllGameType", data, function(data) {
						callback(data);
						if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
						    dataTable.buttons().disable();
						}
						else {
							dataTable.buttons().enable();
						}
				}, 'json');
			},
		});

		$('#btn-submit').click( function() {
	        dataTable.ajax.reload();
	    });

	    $( "#game_platform_id" ).change(function() {
		  dataTable.ajax.reload();
		});



	});

	var gameTypeHistoryTable = $("#gameTypeHistoryTable").DataTable({
		"searching": true
	});
	var GET_GAME_PLATFORMS_URL	= '<?php echo site_url('game_type/getGamePlatforms'); ?>';
	var CRUD_C_URL				= '<?php echo site_url('game_type/saveGameType'); ?>';
	var CRUD_R_URL				= '<?php echo site_url('game_type/getGameType'); ?>';
	var CRUD_U_URL				= '<?php echo site_url('game_type/saveGameType'); ?>';
	var CRUD_D_URL				= '<?php echo site_url('game_type/deleteGameType'); ?>';
	var REFRESH_PAGE_URL 		= '<?php echo site_url('game_type/viewGameType') ?>';
	var GET_GAME_TYPE_HISTORY 	= '<?php echo site_url('game_type/getGameTypeHistoryById') ?>';

	var LANG ={
		ADD_PANEL_TITLE: "<?=lang('sys.gt1');?>",
		EDIT_PANEL_TITLE:"<?=lang('sys.gt2');?>",
		ADD_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('sys.gt1');?>",
		UPDATE_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('sys.gt3');?>",
		DELETE_CONFIRM_MESSAGE:"<?=lang('sys.gt4');?>",
		EDIT:"<?=lang('sys.gt23');?>",
		EDIT_COLUMN:"<?=lang('sys.gt24');?>",
		DELETE_ITEMS:"<?=lang('sys.gt21');?>",
		ADD_GAME_DESC:"<?=lang('sys.gt1');?>",
		GAME_TYPE_DELETE_LANG:"<?=lang('Are you sure you to delete this game type?');?>",
		SELECT_GAME_PLATFORM:"<?=lang('Select Game Platform');?>",
		REQUIRED: "<?=lang('reg.02');?>"
	};
	</script>
</div>
