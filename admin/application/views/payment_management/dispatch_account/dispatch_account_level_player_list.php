<?php $batch_limit = $this->CI->config->item('dispatch_account_batch_move_player_limit');?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="icon-diamond"></i> <?=lang('dispatch_account_level.player_list');?> - <b><?=$data['level_name']?></b>
            <div class="pull-right">
                <a href="<?=BASEURL . 'dispatch_account_management/getDispatchAccountLevelList/'.$data['group_id']?>" class="btn btn-default btn-xs" id="add_news">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div class="container" style="width:100%;">
                    <?=lang('dispatch_account_level.batch_update_player_level');?>
                    <form>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label" for="new_players"><?=lang('dispatch_account_batch.player_list_column');?> (<?=sprintf(lang('dispatch_account_batch.player_list_limit'), $batch_limit);?>)</label>
                                <textarea name="new_players" id="new_players" cols="30" rows="10" class="form-control" ></textarea>
                            </div>
                            <div class="col-md-12 text-right">
                                <a class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" style="margin-top: 5px;" onclick="batchSetPlayersToLevel(<?=$data['id']?>);"><?=lang('submit')?></a>
                            </div>
                        </div>
                    </form>
                    <hr/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="container" style="width:100%;">
                    <fieldset style="padding-bottom: 8px">
                        <legend>
                            <label class="control-label"><?=lang('dispatch_account_level.player_list');?></label>
                            <a id='player_username_list_btn'
                               class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" style='text-decoration:none; border-radius:2px;'
                               onClick='showPlayerUsernameList("<?=$data['id'];?>", "<?=$data['level_name'];?>")'>
                                <span class="fa fa-plus-circle"> <?=lang("Expand All");?></span>
                            </a>
                        </legend>
                        <div id="show_player_username_list">
                            <div id="player_username_list_content" class="row"></div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchProcessModal" style="margin-top:130px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title batch-process-title"><?= lang('Batch Process Summary')?></h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <span class="progressbar-text"><?= lang('Processing....') ?></span>
                    </div>
                </div>
                <div id="procees_summary">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Success Number</td>
                                <td id="success_count"></td>
                            </tr>
                            <tr>
                                <td>Failed Number</td>
                                <td id="failed_count"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center" id="error_message"></div>
                <table class="table table-striped" id="failureListTable">
                    <caption><?= lang('dispatch_account_batch.fail_list') ?></caption>
                    <thead>
                        <tr>
                            <th width="50"><?= lang('Username') ?></th>
                            <th><?= lang('batch.failures_reason') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $('#show_player_username_list').hide();

        $('#batchProcessModal').on('hidden.bs.modal', function () {
            window.location.reload();
        });
    });

    function showPlayerUsernameList(level_id, level_name) {
        $('#show_player_username_list').toggle();
        if($('#player_username_list_btn span').attr('class') == 'fa fa-plus-circle'){
            $('#player_username_list_btn span').attr('class', 'fa fa-minus-circle');
            $('#player_username_list_btn span').html(' <?=lang("Collapse All");?>');
        }
        else{
            $('#player_username_list_btn span').attr('class', 'fa fa-plus-circle');
            $('#player_username_list_btn span').html(' <?=lang("Expand All");?>');
        }

        if ($('#player_username_list_content').is(':empty')) {
            var img = '<div class="col-md-12 text-center"><img id="refresh-loader" src="<?=$this->utils->imageUrl('ajax-loader.gif')?>"/></div>';
            $('#player_username_list_content').append(img);
            $.ajax({
                'url' : base_url + 'dispatch_account_management/getPlayerListInLevel/' + level_id,
                'type' : 'GET',
                'dataType' : "json",
                'success' : function(result_data){
                    $('#player_username_list_content').empty();
                    var player_username_html = '';

                    if(result_data.length > 0) {
                        for(var i=0; i<result_data.length; i++) {
                            var item_html = '<div class="col-md-2"><a href="/player_management/userInformation/'+ result_data[i].playerId + '" target="_blank">' + result_data[i].username + '</a></div>';
                            player_username_html = player_username_html + item_html;
                        }
                    }
                    else {
                        player_username_html = '<div class="col-md-12 text-center"><h3><?=lang("dispatch_account_level.player_list_empty");?></h3></div>';
                    }
                    $('#player_username_list_content').append(player_username_html);
                }
            },'json');
        }
    }


    var success_count = 0;
    var failed_count = 0;
    var lang = {
        "empty_batch_request.message" : "<?php echo lang("dispatch_account_batch.empty_request.message");?>",
        "max_batch_request.message" : "<?php echo lang("dispatch_account_batch.max_request.message");?>",
        "error.default.message" : "<?php echo lang("error.default.message");?>",
    }

    function batchSetPlayersToLevel(level_id){
        start = performance.now();
        if($('#new_players').val()==''){
            alert(lang['empty_batch_request.message']);
            return false;
        }
        var new_players = $('#new_players').val().split("\n").filter(function(item){
          return item != '';
        });

        var totalAdjustment = new_players.length;
        if(totalAdjustment > <?=$batch_limit?>){
            alert(lang['max_batch_request.message']);
            return false;
        }

        $('#batchProcessModal').modal('show');
        $('#procees_summary').hide();
        $('#failureListTable').hide();
        $('#error_message').hide();

        $.ajax({
            'url' : site_url('dispatch_account_management/setPlayersToLevel/' + level_id),
            'type' : 'POST',
            'data' : { new_players: new_players },
            'dataType' : "json"
        }).done(function(data){

            if(data['error']){
                $('#error_message').append('<h4>' + data['error_message'] + '</h4>');
                $('#error_message').show();
                return false;
            }

            var fail_list = data['fail_list'];
            Object.keys(fail_list).forEach(function(key) {
                fail = fail_list[key];
                $('#failureListTable').append('<tr><td>'+fail.username+'</td><td>'+fail.reason+'</td></tr>');
            });
            success_count = data['success_count'];
            failed_count = data['failed_count'];
            completeProcess();
            return true;

        }).fail(function(){
            return false;
        });
    }

    function completeProcess() {
        $( ".progress-bar" ).removeClass('active');
        $( ".progress-bar" ).addClass('progress-bar-warning');
        $( ".progressbar-text" ).text("<?= lang('Done!') ?>");

        $('#procees_summary').show();
        $('#success_count').append(success_count);
        $('#failed_count').append(failed_count);
        if(failed_count > 0){
            $('#failureListTable').show();
        }
    }
</script>