<?php
// echo "<pre>";print_r($games);exit;
?>
<link href="//www.fuelcdn.com/fuelux/3.13.0/css/fuelux.min.css" rel="stylesheet">

<style type="text/css">
    .fuelux .wizard .step-content .step-pane h4{
        padding:0 15px;
    }
    .fuelux .wizard .step-content .step-pane .image-container span{
        display:block;
    }
    .fuelux .wizard .step-content .step-pane .image-container img{
        height:195px;
        width:100%;
    }
    .fuelux .wizard .step-content .step-pane .col-md-3{
        margin:15px 0;
    }
    .btn.active {
        background: #a7a7a7;
        color: #fff;
        position: relative;
    }
    .btn.active i {
        color: #fff;
    }
    .current-header:before {
        content: "\e013";
        font-family: 'Glyphicons Halflings';
        font-size: 18px;
        position: absolute;
        left: -1px;
        background: #0fc5cc;
        color: #fff;
        height: 39px;
        top: -1px;
        bottom: 0;
        width: 16%;
        line-height: 39px;
    }
</style>
<body class="fuelux">
    <div class="wizard" data-initialize="wizard" id="myWizard">
        <div class="steps-container">
            <ul class="steps">
                <li data-step="1" data-name="template" id="template_tab" class="active">
                    <span class="badge">1</span><?=lang('Template')?>
                    <span class="chevron"></span>
                </li>
                <li data-step="2" id="game_tab">
                    <span class="badge">2</span><?=lang('Games')?>
                    <span class="chevron"></span>
                </li>
                <li data-step="4" data-name="finalize">
                    <span class="badge">3</span><?=lang('Finalize')?>
                    <span class="chevron"></span>
                </li>
            </ul>
        </div>
        <div class="actions">
            <button type="button" class="btn btn-default btn-prev">
                <span class="glyphicon glyphicon-arrow-left"></span><?=lang('Prev')?></button>
            <button type="button" class="btn btn-primary btn-next" data-last="Complete" disabled onclick="postGameLobbyDetails()"><?=lang('Next')?>
                <span class="glyphicon glyphicon-arrow-right"></span>
            </button>
        </div>
        <div class="step-content">
            <div class="step-pane active sample-pane alert" id="template_step_pane" data-step="1">
                <div class="row">
                    <h4><?=lang('Select Template')?></h4>
                    <?php foreach ($templates as $template): ?>
                        <div class="col-md-3">
                            <label class="btn btn-block btn-default image-container template_name" data-value="<?=$template?>">
                                <span><?=$template?></span>
                                <input type="radio" name="template_name" value="<?=$template?>" style="display: none;">
                                    <img src="<?= base_url().'upload/game_lobby_template/images/image_'.$template?>.jpg" alt="<?=ucfirst($template)?>" height="100%" width="100%" />
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="step-pane sample-pane alert" id="game_provider_step_pane" data-step="2">
                <div class="row">
                    <h4><?=lang('Select Game Provider')?></h4>
                    <?php foreach ($gameapis as $key => $game_provider_details): ?>
                        <div class="col-md-3">
                            <a data-toggle="modal" href="#modal-<?=$game_provider_details['id']?>" class="btn btn-block btn-default select_game_provider" id="modal-button-<?=$game_provider_details['id']?>" data-id="<?=$game_provider_details['id']?>"><?=$game_provider_details['system_code']?>
                                <input type="checkbox" class="game_provider_ids" name="game_provider_ids[]" multiple="true" value="<?=$game_provider_details['id']?>" autocomplete="off" style="display: none;">
                            </a>
                        </div>
                        <!-- Start Modal -->
                        <div class="modal fade" id="modal-<?=$game_provider_details['id']?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title"><?=$game_provider_details['system_code']?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div role="tabpanel">
                                            <div class="selectAllGamesOnCurrentModal" data-game_platform_id="<?=$game_provider_details['id']?>" data-tab_panes_id="#tabpanes-<?=$game_provider_details['id']?>" data-modal_button_id="#modal-button-<?=$game_provider_details['id']?>">
                                                <label><?=lang('Select all games:')?></label>
                                                <input type="checkbox" name="selectAllGames">
                                            </div>
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <?php $cnt=0; ?>
                                                <?php foreach ($game_types as $key => $game_type): ?>

                                                    <?php if ($game_type->game_platform_id == $game_provider_details['id'] && strtolower(lang($game_type->game_type)) != "unknown"): $cnt++;?>
                                                        <li role="presentation" class="<?=$cnt==1 ? 'active':''?> tab-area">
                                                            <a href="#tabid-<?=lang($game_type->id)?>" aria-controls="tabid-<?=$game_type->id?>"  role="tab" data-toggle="tab"><?=lang($game_type->game_type)?></a>
                                                        </li>
                                                    <?php endif ?>

                                                <?php endforeach ?>

                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content" id="tabpanes-<?=$game_provider_details['id']?>" data-modal_button_id="#modal-button-<?=$game_provider_details['id']?>" data-game_platform_id="<?=$game_provider_details['id']?>"  data-game_type_id="<?=$game_type->id?>">
                                                <?php $cnt = 0; ?>
                                                <?php foreach ($game_types as $key => $game_type): ?>
                                                    <?php if ($game_type->game_platform_id == $game_provider_details['id']): $cnt++;?>
                                                    <div role="tabpanel" class="tab-pane <?=$cnt==1 ? 'active':''?>" id="tabid-<?=lang($game_type->id)?>">

                                                        <?php if ($game_type->game_with_lobby): ?>
                                                            <div class="selectAllGamesOnCurrentTable" data-tabid="tabid-<?=lang($game_type->id)?>">
                                                                <input type="checkbox" value="<?=lang($game_type->id)?>"><?=lang('Select this game type')?>
                                                            </div>
                                                        <?php endif ?>
                                                        <table class="main-table table table-striped" data-game_type_id="<?=lang($game_type->id)?>">
                                                            <thead>
                                                                <tr>
                                                                    <?php if ( ! $game_type->game_with_lobby): ?>
                                                                    <th width="5%" class="selectAllGamesOnCurrentTable" data-tabid="tabid-<?=lang($game_type->id)?>">
                                                                        <input type="checkbox" value="<?=lang($game_type->id)?>">
                                                                    </th>
                                                                    <?php endif ?>
                                                                    <th width="40%"><?=lang('Game Name')?></th>
                                                                    <th width="40%"><?=lang('Game Code')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('Desktop/Flash')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('H5/Mobile')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('Desktop')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('Android')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('IOS')?></th>
                                                                    <th class="text-center" width="7%"><?=lang('Free Spin')?></th>
                                                                    <th class="text-center" width="20%"><?=lang('Fun Game')?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody role="tabpanel" class="temp-tbody tab-pane  active in" id="tbody_pane_<?=lang($game_type->id)?>">
                                                            <?php foreach ($games as $key => $game): ?>

                                                                <?php if ($game->gamePlatformId == $game_provider_details['id']): ?>

                                                                    <?php if ($game->game_type_id == $game_type->id): ?>

                                                                        <tr>
                                                                            <?php if ( ! $game_type->game_with_lobby): ?>
                                                                            <td><input type="checkbox" name="game_ids" class="game_ids" value="<?=$game->gameDescriptionId?>"></td>
                                                                            <?php endif ?>
                                                                            <td><?=lang($game->gameName)?></td>
                                                                            <td><?=lang($game->gameCode)?></td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->flash_enabled)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->flash_enabled)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->flash_enabled)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->enabled_on_android)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->enabled_on_ios)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->enabled_freespin)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php if(!empty($game->demo_link)){ ?>
                                                                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"><a href="" class="text-primary">DEMO</a></span>
                                                                                <?php }else{ ?>
                                                                                    <span class="glyphicon glyphicon-no" aria-hidden="true"></span>
                                                                                <?php } ?>
                                                                                </td>
                                                                        </tr>

                                                                    <?php endif ?>

                                                                <?php endif ?>

                                                            <?php endforeach ?>


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php endif ?>
                                                <?php endforeach ?>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="save-games">Save changes</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                    <?php endforeach ?>
                </div>
            </div>
            <input type="hidden" name="game_lobby_id" id="game_lobby_id">
            <div class="step-pane sample-pane alert" id="finalize_step_pane" data-step="3">
                <div class="row">
                    <h4><?=lang('Select Template')?></h4>

                </div>
            </div>
        </div>
    </div>

</body>
<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");
    $("a#viewGameListSettings").addClass("active");

    $(document).ready(function(){
        $('table').DataTable({
            "iDisplayLength": <?=$this->utils->getConfig('default_items_pre_page')?>,
            columnDefs: [
            {
                'targets': [0],
                'orderable': false,
                'searchable ' : false
            }],
            "drawCallback": function( settings ) {
                if ($(this).find("thead .selectAllGamesOnCurrentTable input[type='checkbox']").is(":checked") == true) {
                    $(this).find("tbody input[type='checkbox']").prop('checked',true);
                }else{
                    $(this).find("tbody input[type='checkbox']").prop('checked',false);
                }
            }
        });

        $('input[name="template_name"]').change(function() {
            $('.row label').removeClass('active');
            $('input[name="template_name"]:checked').parent('label').addClass('active');
        });

        $('.btn-next').click(function(){
            if ($(this).text() == "Complete") {
                $("#finalize_step_pane").addClass("active");
            }else{
                $("#finalize_step_pane").removeClass("active");
            }
        });
    });

    $('.game_provider_step_pane.active').each(function(){
        game_provider_ids.push($(this).data('id'));
    });

    var url = "<?=base_url()?>";
    var template_name = $(".template_name").val();
    var game_provider_ids = [];
    var list_of_game_ids = {};
    var list_of_game_type_ids = [];
    var list_of_game_platform_ids = [];

    $(".selectAllGamesOnCurrentModal").on("click",function(){
        var modal_button_id =  $(this).data('modal_button_id');
        var tab_panes_id = $(this).data('tab_panes_id');
        var game_platform_id = $(this).data('game_platform_id');

        addRemoveIdsToArray("game_platform_id", game_platform_id, null, null);

        if ($(this).find(":checked").length == 1) {
            addActiveClassToModalButton(modal_button_id,true,game_platform_id, null);
            $(tab_panes_id + ' input[type="checkbox"]').prop("checked",true);
        }else{
            addActiveClassToModalButton(modal_button_id,false,game_platform_id, null);
            $(tab_panes_id + ' input[type="checkbox"]').prop("checked",false);
        }
    });

    $('.selectAllGamesOnCurrentTable').change(function (e) {
        var modal_button_id =  $(this).closest(".tab-content").data('modal_button_id');
        var tab_id = $(this).data('tabid');
        var table_id = "#" + tab_id + " td input[type='checkbox']";
        var game_type_id = $(e.target).closest('table').data('game_type_id');
        var game_platform_id = $(this).closest(".tab-content").data("game_platform_id");

        if ( ! game_type_id) {
            game_type_id = $(this).find("input[type='checkbox']").val();
        }

        if ($("#" + tab_id + " .selectAllGamesOnCurrentTable input:checked").length == 1) {
            addRemoveIdsToArray("game_type_id", game_platform_id, game_type_id, null, "set");
            addActiveClassToModalButton(modal_button_id,true,game_platform_id, game_type_id);
            $(table_id).prop("checked",true);
        }else{
            addRemoveIdsToArray("game_type_id", game_platform_id, game_type_id, null, "unset");
            addActiveClassToModalButton(modal_button_id,false,game_platform_id, game_type_id);
            $(table_id).prop("checked",false);
        }
    });

    $(".game_ids").change(function(e){
        var game_id = $(this).val();
        var game_type_id = $(e.target).closest('table').data('game_type_id');
        var game_platform_id = $(e.target).closest(".tab-content").data("game_platform_id");
        var current_table_checkbox_count = $("#tbody_pane_"+ game_type_id +" input[type='checkbox']").length;
        var current_table_checked_checkbox_count = $("#tbody_pane_"+ game_type_id +" input:checked").length;
        var modal_button_id = $(this).closest(".tab-content").data("modal_button_id");

        if($(this).is(':checked')){
            addRemoveIdsToArray("game_id", game_platform_id, game_type_id, game_id, "set");
        }else{
            addRemoveIdsToArray("game_id", game_platform_id, game_type_id, game_id, "unset");
        }

        if (current_table_checked_checkbox_count != current_table_checkbox_count) {
            $("#tabid-"+game_type_id+' .selectAllGamesOnCurrentTable input[type="checkbox"]').prop("checked",false);
        }else{
            $("#tabid-"+game_type_id+' .selectAllGamesOnCurrentTable input[type="checkbox"]').prop("checked",true);
        }

        if (current_table_checked_checkbox_count != 0) {
            addActiveClassToModalButton(modal_button_id,true,game_platform_id, game_type_id);
        }else{
            addActiveClassToModalButton(modal_button_id,false,game_platform_id, game_type_id);
        }

    });

    $(".template_name").bind("click", function(){
        $('.btn-next').prop('disabled',false);
    });

    $(".select_game_provider").click(function(){
        var modal_id = "#modal-" + $(this).attr('data-id');
        $(modal_id).addClass('active_modal');
    });

    $("#save-games").on("click",function(){
        var modal_button_id =  $(this).closest(".tab-content").data('modal_button_id');
    });

    function postGameLobbyDetails(){
        var game_tab = $("#game_tab").hasClass("active"),
            template_name = $("#template_step_pane .template_name.active").data("value");

        if (game_tab) {
            test_data = {
                template_name: template_name,
                game_lobby_id: $("#game_lobby_id").val(),
                game_provider_ids: game_provider_ids,
                list_of_game_ids: list_of_game_ids,
                list_of_game_type_ids: list_of_game_type_ids,
                list_of_game_platform_ids: list_of_game_platform_ids
            };

            $.post(url + 'game_description/postGameLobbyDetails',
            {
                template_name: template_name,
                game_lobby_id: $("#game_lobby_id").val(),
                game_provider_ids: game_provider_ids,
                list_of_game_ids: list_of_game_ids,
                list_of_game_type_ids: list_of_game_type_ids,
                list_of_game_platform_ids: list_of_game_platform_ids
            },
            function(data, status){
                $('#game_lobby_id').val(data);
                alert("Data: " + data + "\nStatus: " + status);
            });
        }

    }

    function addRemoveIdsToArray(platform, game_platform_id, game_type_id, game_id, flag = null){

        if(platform == "game_platform_id"){
            if(jQuery.inArray(game_platform_id,list_of_game_platform_ids) == '-1'){
                list_of_game_platform_ids.push(game_platform_id);
            }else{
                //remove game id to list
                list_of_game_platform_ids = jQuery.grep(list_of_game_platform_ids, function(value) {
                  return value != game_platform_id;
                });
            }
        }

        if (platform == "game_type_id") {

            if (!list_of_game_type_ids.hasOwnProperty(game_platform_id)) {
                list_of_game_type_ids[game_platform_id] = [game_type_id];
            }else{
                if(jQuery.inArray(game_type_id,list_of_game_type_ids[game_platform_id]) == '-1'){
                    list_of_game_type_ids[game_platform_id].push(game_type_id);
                }else{
                    //remove game id to list
                    if (flag == "unset") {
                        list_of_game_type_ids[game_platform_id] = jQuery.grep(list_of_game_type_ids[game_platform_id], function(value) {
                          return value != game_type_id;
                        });
                    }
                }
            }

            if (flag == "unset") {
                if (list_of_game_ids.hasOwnProperty(game_platform_id) && list_of_game_ids[game_platform_id].hasOwnProperty(game_type_id)) {
                    list_of_game_ids[game_platform_id][game_type_id] = [];
                }
            }
        }

        if (platform == "game_id") {
            var tmp = {};
            // check if prop exist
            if (!list_of_game_ids.hasOwnProperty(game_platform_id)) {
                tmp[game_type_id] = [game_id];
                list_of_game_ids[game_platform_id] = tmp;
                return;
            }

            if (!list_of_game_ids[game_platform_id].hasOwnProperty(game_type_id)) {
                list_of_game_ids[game_platform_id][game_type_id] = [game_id];
            } else {
                // check if value exist in array
                if(jQuery.inArray(game_id,list_of_game_ids[game_platform_id][game_type_id]) == '-1'){
                    list_of_game_ids[game_platform_id][game_type_id].push(game_id);
                }else{
                    //remove game id to list
                    if (flag == "unset") {
                        list_of_game_ids[game_platform_id][game_type_id] = jQuery.grep(list_of_game_ids[game_platform_id][game_type_id], function(value) {
                          return value != game_id;
                        });
                    }
                }
            }
        }

    }

    function addActiveClassToModalButton(modal_button_id, status, game_platform_id, game_type_id){
        var modal_button_id = $(modal_button_id);

        if (status) {
            $(modal_button_id).addClass("active current-header");
        }else{

            if (game_type_id != null) {
                if (list_of_game_ids.hasOwnProperty(game_platform_id)) {
                    if (list_of_game_ids[game_platform_id][game_type_id].length == 0 && list_of_game_type_ids.length == 0 && list_of_game_platform_ids.length == 0) {
                        if( $(modal_button_id).hasClass("active current-header") ){
                            $(modal_button_id).removeClass("active current-header");
                        }
                    }
                }else{
                    if( $(modal_button_id).hasClass("active current-header") ){
                        $(modal_button_id).removeClass("active current-header");
                    }
                }
            }else{
                if ((list_of_game_type_ids.length == 0 && list_of_game_platform_ids.length == 0) || (list_of_game_platform_ids.length == 0 && game_type_id == null && status == false)) {
                    if( $(modal_button_id).hasClass("active current-header") ){
                        $(modal_button_id).removeClass("active current-header");
                    }
                }
            }
        }
    }

    function getGamelistPerGameProviders(){
        $.post(url + 'game_description/getGamelistPerGameProviders',
        {
            game_provider_ids: game_provider_ids,
        },
        function(data, status){
            $('#game_lobby_id').val(data);
            alert("Data: " + data + "\nStatus: " + status);
        });
    }

</script>