<!-- Latest compiled and minified CSS -->

<style type="text/css">
    .select.modal .modal-header>.close{color:#000}.select.modal .modal-body{max-height:500px;overflow-y:scroll}.select.modal .modal-body>.option{padding:15px 0;margin:0 15px}.select.modal .modal-body>.option>.option-tick{float:right;display:none}.select.modal .modal-body>.option.selected>.option-tick,.selectWrap{display:block}.select.modal .modal-body>.option.selected{color:#fc5510}.selectWrap{position:relative;width:100%;height:auto;min-height:48px;padding:6px 12px;background-color:#fbfbfb;background-image:none;border:1px solid #e8e8e8;border-radius:3px;-webkit-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.selectWrap>.open-options{position:absolute;text-decoration:none;color:#000;right:0}.selectWrap>.open-options>span.glyphicon{font-size:14px}.selectWrap.disabled>.open-options{display:none}.selectWrap>.select-content{display:block}.selectWrap>.select-content>.addedOption{display:inline-block;color:#fff;background-color:#fff;border:1px solid #333;padding:5px;border-radius:3px;margin:2px;float:left;cursor:pointer}.selectWrap>.select-content>.addedOption>.text{color:#333}.selectWrap>.select-content>.addedOption>.removeOption{color:#aaa;margin-left:10px}.selectWrap.disabled>.select-content>.addedOption{cursor:not-allowed}.selectWrap.disabled>.select-content>.addedOption>.text{color:#999}.selectWrap.disabled>.select-content>.addedOption>.removeOption{display:none}.clickable{cursor:pointer} .open-options{margin-top:5px !important}
</style>
<!-- Latest compiled and minified JavaScript -->

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <?php echo lang('Game List Settings'); ?>
            <a href="#" onclick="history.back()" class="close">&times;</a>
        </h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="panel-group" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                    <?=lang('Game List')?>
                                </a>
                            </h4>
                        </div>
                        <div class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading0">
                            <div class="list-group">
                                <a id="batch-add-game-list" title="<?=lang('Batch Add/Update Game List')?>" class="list-group-item"><i class="fa fa-plus"></i> <?=lang('Batch Add/Update Game List')?></a>
                                <a id="batch-add-game-type" title="<?=lang('Batch Add/Update Game Type')?>" class="list-group-item"><i class="fa fa-plus"></i> <?=lang('Batch Add/Update Game Type')?></a>
                                <a title="<?=lang('Game Lobby')?>" class="list-group-item hide" href="<?=site_url('game_description/viewGameLobby')?>"><i class="fa fa-plus"></i> <?=lang('Game Lobby')?></a>
                                <a title="<?=lang('Update Game Lobby Template')?>" id="upload_game_lobby_template" class="list-group-item hide"><i class="fa fa-plus"></i> <?=lang('Upload Game lobby Template')?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!----------------Batch add-FORM Game Details start---------------->
            <div class="col-md-6" id="game-list-settings-form" style="display:none" >

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="icon-pencil"></i> <span id="add-edit-panel-title"><?=lang('Batch Add/Update Game List')?></span>
                            <a href="#close" class="btn btn-primary btn-sm pull-right panel-button closeDetails"><span class="glyphicon glyphicon-remove"></span></a>
                            <a id="sample-file" href="/" class="btn btn-primary btn-sm pull-right panel-button" title="<?=lang('Download Sample CSV File')?>" style="margin-right:1%"><span class="glyphicon glyphicon-download"></span></a>
                        </h4>

                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">

                        <form id="game-settings-form" class="form-horizontal" action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">

                            <div id="batch-form" style="display:none">
                                <p class="text-danger"><?=lang('Note: Chinese characters should be utf-8 encoded before addin the batch or else it will ignore the chinese chars')?></p class="text-danger">
                                <div class="form-group">
                                    <label class="col-md-3"><?=lang('CSV File')?></label>
                                    <div class="col-md-8">
                                        <input name="games" class="user-error" aria-invalid="true" type="file" accept=".csv">
                                    </div>
                                </div>
                                <div class="hide">
                                    <div class="form-group" id="update_multiple_client_form">
                                        <label class="col-md-3"><?=lang('Update Multiple Client')?></label>
                                        <div class="col-md-8">
                                            <input name="update_multiple_client" id="update_multiple_client" class="user-error" aria-invalid="true" type="checkbox" >
                                        </div>
                                    </div>
                                    <div class="form-group hide" id="client_environment_form">
                                        <label class="col-md-3"><?=lang('Select Client Environment')?></label>
                                        <div class="col-md-8">
                                            <input name="client_environment" class="client_environment" value="live" class="user-error" aria-invalid="true" type="radio" ><?=lang('Live')?>
                                            <input name="client_environment" class="client_environment" value="staging" class="user-error" aria-invalid="true" type="radio" ><?=lang('Staging')?>
                                        </div>
                                    </div>
                                    <div class="form-group hide" id="all_client">
                                        <label class="col-md-3"><?=lang('All Client')?></label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="client_list_select_all" class="user-error" aria-invalid="true" id="client_list_select_all" checked>
                                        </div>
                                    </div>
                                    <div class="form-group hide" id="client_list_form">
                                        <label class="col-md-3"><?=lang('Select Client')?></label>
                                        <div class="col-md-8">
                                            <select multiple name="client_list[]" id="client_list">
                                                <?php foreach ($list_of_client as $key) { ?>
                                                    <option value="<?=$key?>"><?=ucwords($key)?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group hide" id="mutiple_game_platform_form">
                                        <label class="col-md-3"><?=lang('Multiple Game Platform')?></label>
                                        <div class="col-md-8">
                                            <input name="mutiple_game_platform" id="mutiple_game_platform" class="user-error" aria-invalid="true" type="checkbox" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3"><?=lang('Game Platform');?></label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="game_platform_id" id="active_game_api" >
                                        <option value=""><?=lang('Select Game Platform')?></option>
                                        <?php foreach ($gameapis as $game) { ?>
                                        <option value="<?=$game['id']?>"><?php echo $game['system_code'] . "[" . $game['id'] . "]"; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="upload-game-lobby-template-form" style="display:none">
                                <div class="form-group" id="">
                                    <label class="col-md-3"><?=lang('Template Name')?></label>
                                    <div class="col-md-8">
                                        <input name="template_name" class="user-error" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3"><?=lang('Template')?></label>
                                    <div class="col-md-8">
                                        <input name="template_file[]" class="user-error" aria-invalid="true" type="file" accept=".tmpl">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3"><?=lang('Image')?></label>
                                    <div class="col-md-8">
                                        <input name="template_image[]" class="user-error" aria-invalid="true" type="file" accept=".png , .jpeg, .jph">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3"><?=lang('Css')?></label>
                                    <div class="col-md-8">
                                        <input name="template_css[]" class="user-error" aria-invalid="true" type="file" accept=".css">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group hide">
                                <label class="col-md-3"><?=lang('pay.reason')?></label>
                                <div class="col-md-9">
                                    <textarea name="reason" class="form-control" rows="5" ></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-primary btn_submit"><?=lang('lang.submit')?></button>
                                    <button type='reset' class="btn btn-default"><?=lang('lang.reset')?></button>
                                </div>
                            </div>

                        </form>
                    </div>
            </div>
            <!---------------Batch add-FORM Game Details end---------------->

        </div>

    </div>
    <div class="panel-footer"></div>


</div>

<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");
    $("a#viewGameListSettings").addClass("active");

    var url = '<?=base_url()?>';
    $("#batch-add-game-list").on('click',function(){
        var game_description_url = url + 'game_description/postBatchInsertUpdateGameList';

        $("#game-list-settings-form").css('display','block');
        $("#batch-form").css('display','block');
        $("#upload-game-lobby-template-form").css('display','none');
        $('#game-settings-form').attr('action',game_description_url);
        $('#sample-file').attr('href','/sample_game_list_add_update.csv');
        $('#add-edit-panel-title').html('<?=lang("Batch Add/Update Game List")?>');
        $('#mutiple_game_platform_form').addClass('hide');
    });

    $('#batch-add-game-type').on('click',function(){
        var game_type_url = url + 'game_type/postBatchInsertUpdateGameType';

        $("#game-list-settings-form").css('display','block');
        $("#batch-form").css('display','block');
        $("#upload-game-lobby-template-form").css('display','none');
        $('#game-settings-form').attr('action',game_type_url);
        $('#sample-file').attr('href','/sample_game_type_add_update.csv');
        $('#add-edit-panel-title').html('<?=lang("Batch Add/Update Game Type")?>');
        $('#mutiple_game_platform_form').removeClass('hide');
    });

    $('#upload_game_lobby_template').on('click',function(){
        var game_type_url = url + 'game_description/uploadGameLobbyTemplate';

        $("#batch-form").css('display','none');
        $("#game-list-settings-form").css('display','block');
        $("#upload-game-lobby-template-form").css('display','block');
        $('#game-settings-form').attr('action',game_type_url);
        $('#sample-file').attr('href','/sample_game_type_add_update.csv');
        $('#add-edit-panel-title').html('<?=lang("Upload Game lobby Template")?>');
        $('#mutiple_game_platform_form').removeClass('hide');
    });

    $('#mutiple_game_platform').change(function(){
        if($(this).prop("checked") == true){
            $('#active_game_api').prop('disabled',true);
            $('#active_game_api').prop('required',false);
        }else{
            $('#active_game_api').prop('disabled',false);
            $('#active_game_api').prop('required',true);
        }
    });

    $('#update_multiple_client').change(function(){
        if($(this).prop("checked") == true){
            $('#client_environment_form').removeClass('hide');
            $('#all_client').removeClass('hide');
        }else{
            $('#client_environment_form').addClass('hide');
            $('#all_client').addClass('hide');
            $('#client_list_form').addClass('hide');
        }
    });

    $('#client_list_select_all').change(function(){
        if($(this).prop("checked") == true){
            $('#client_list_form').addClass('hide');
        }else{
            $('#client_list_form').removeClass('hide');
        }
    });

    $("#client_list").multiselect({
        title: "<?=lang('Select Client')?>",
        maxSelectionAllowed: <?=count($list_of_client)?>
    });

    $(".closeDetails").on('click',function(){
        $("#game-list-settings-form").css('display','none');
    });

    $('#gameListSettings').addClass('active');
</script>