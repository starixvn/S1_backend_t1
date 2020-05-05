
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGameDescription" class="btn btn-default btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseGameDescription" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
        <form id="form-filter" class="form-horizontal" method="get">
	        <div class="panel-body">
	            <div class="row">
                    <div class="col-md-4">
                        <label class="control-label" for="gamePlatformId"><?=lang('sys.gp0')?>:</label>
                        <select name="gamePlatform" id="gamePlatform" class="form-control">
                            <option value="N/A"><?=lang('Select Game Platform')?></option>
                            <?php foreach ($gameapis as $gameApi) { ?>
                                <option value="<?=($gameApi['id'])?>" <?=($gameApi['id']==$conditions['gamePlatform'])?'selected':''?>><?=$gameApi['system_code']?></option>
                            <?php } ?>
                        </select>
                    </div>
	                <div class="col-md-4">
	                    <label class="control-label" for="gameUsername"><?=lang('sys.gp1')?>:</label>
	                    <input type="text" name="gameUsername" id="gameUsername" value="<?=($conditions['gameUsername']) ? $conditions['gameUsername']:''?>" class="form-control number_only"/>
	                </div>
	            </div>
	        </div>
	        <div class="panel-footer text-right">
	             <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-info btn-sm">
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
                    <?=lang('Game Provider Auth');?>
                </h3>
            </div>
			<div class="panel-body" id="list_panel_body">
				<div class="table-responsive">
				<div class="clearfix"></div>
					<table class="table table-bordered table-hover dataTable" id="my_table" >
						<thead>
							<tr>
								<th><?=lang('sys.gp1');?></th>
                                <th><?=lang("sys.gp0");?></th>
								<th><?=lang("sys.gp2");?></th>
                                <th><?=lang('sys.gp3');?></th>
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

<script type="text/javascript">
$("#collapseSubmenuGameDescription").addClass("in");
$("a#view_game_description").addClass("active");
// $("a#viewGameListSettings").addClass("active");
var gameNameEditor;
var gameAttributesEditor;

$(document).ready(function(){

	// Init syntax highlight for JSON string in extra_info
	hljs.initHighlightingOnLoad();

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
				postfixButtons: [ 'colvisRestore' ]
			},
				<?php

                    if( $this->permissions->checkPermissions('export_game_description') ){

                ?>
			 {
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm btn-primary export_excel',
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

			$.post(base_url + "api/gameProviderAuthList", data, function(data) {
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
DELETE_AND_CREATE_GAME_PROVIDER_AUTH_URL ='<?php echo site_url('game_description/deleteAndCreateGameProviderAuthUsername') ?>/',
UPDATE_GAME_DESCRIPTION_STATUS_URL ='<?php echo site_url('game_description/updateGameDescriptionStatus') ?>/',
REFRESH_PAGE_URL = '<?php echo site_url('game_description/viewGameDescription') ?>',
GET_GAME_DESCRIPTION_HISTORY = '<?php echo site_url('game_description/getGameDescriptionHistory') ?>';
var game_description_url = '<?php echo site_url('game_description/postBatchInsertUpdateGameList')?>';
var baseUrl = "<?=base_url()?>";
var message = {
    gameType        : '<?= lang('Select Game Type'); ?>'
};
var gamePlatformId = "<?=$conditions['gamePlatform']?>",
	gameUsername = '<?=isset($conditions['gameUsername']) ?$conditions['gameUsername']:""?>';

var LANG ={
	ADD_PANEL_TITLE: "<?=lang('Add new game');?>",
	EDIT_PANEL_TITLE:"<?=lang('sys.gd2');?>",
	ADD_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('Add new game');?>",
	UPDATE_BUTTON_TITLE:"<i class='fa fa-check'></i> <?=lang('sys.gd3');?>",
	DELETE_CONFIRM_MESSAGE:"<?=lang('sys.gd4');?>",
	EDIT:"<?=lang('sys.gd23');?>",
	EDIT_COLUMN:"<?=lang('sys.gd24');?>",
	DELETE_ITEMS:"<?=lang('sys.gd21');?>",
	ADD_GAME_DESC:"<?=lang('Add new game');?>"
};

var show_non_active_game_api_game_list = <?=($this->utils->getConfig('show_non_active_game_api_game_list'))?true:0?>;

function log_game_type_id(id){
// console.log(id);
}

</script>