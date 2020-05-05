<div class="panel panel-primary">
    <div class="panel-body">
        <div class="form-inline">
            <div class="form-group">
                <input type="text" name="username" id="username" class="form-control" placeholder="<?=lang('player.01')?>">
            </div>
            <div class="form-group">
                <button class="btn btn-add <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onClick="return confirmation();"><?=lang('lang.add')?></button>
            </div>
             <div class="form-group">
                <span class="msg-information"></span>
            </div>
            <div class="form-group btn-confirmation">
                <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success'?>" onClick="return add_player_whitelist()"><i class="fa fa-check"></i></span></button>
            </div>
            <div class="form-group btn-confirmation">
                <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" onclick="refresh_modal('duplicate_account_whitelist', '/user_management/viewDuplicateAccountWhitelist/', '<?=lang('whitelist')?>');"><i class="fa fa-close"></i></span></button>
            </div>
        </div>
        <div class="clearfix"><br></div>
        <div class="form-group">
            <table class="table table-bordered" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th class="active"><?=lang('player.01')?></th>
                        <th class="active"><?=lang('lang.action')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $row) { ?>
                    <tr>
                        <td><?=$row['username']?></td>
                        <td>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs btn-remove-single-<?=$row['playerId']?>" onclick="return removeConfirmation('<?=$row['playerId']?>')"><i class="fa fa-minus"></i><span class="hidden-xs"> <?=lang('Remove')?></span></a>
                        <span class="msg-information-<?=$row['playerId']?>"></span>
                        <a href="javascript:void(0)" class="btn btn-success btn-xs btn-remove btn-remove-confirmation-<?=$row['playerId']?>" onclick="return remove_player_whitelist('<?=$row['playerId']?>')"><i class="fa fa-check"></i></a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs btn-remove btn-remove-confirmation-<?=$row['playerId']?>" onclick="refresh_modal('duplicate_account_whitelist', '/user_management/viewDuplicateAccountWhitelist/', '<?=lang('whitelist')?>');"><i class="fa fa-close"></i></span></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.btn-confirmation').hide();
        $('.btn-remove').hide();
        $('#username').on('keypress', function() {
            $('.btn-confirmation').hide();
            $('.btn-add').show();
            $('.msg-information').text('');
        })
    });

    function confirmation()
    {
        if($("#username").val()){
            $('.btn-confirmation').show();
            $('.btn-add').hide();
            $('.msg-information').text('<?=lang('sys.sure')?>');
            $('.msg-information').addClass('text-success');
        } else {
            $('.msg-information').text('<?=lang('con.cb02')?>');
            $('.msg-information').addClass('text-danger');
        }
        return false;
    }

    function removeConfirmation(playerId)
    {
        if (playerId){
            $('.btn-remove-confirmation-'+playerId).show();
            $('.btn-remove-single-'+playerId).hide();
            $('.msg-information-'+playerId).text('<?=lang('sys.sure')?>');
            $('.msg-information-'+playerId).addClass('text-success');
        } else {
            $('.msg-information-'+playerId).text('<?=lang('con.cb02')?>');
            $('.msg-information-'+playerId).addClass('text-danger');
        }
        return false;
    }

    function remove_player_whitelist(playerId) {
        var url = "/user_management/removeDuplicateAccountWhitelist/"+playerId;
        if(playerId) {
            $.post(url, playerId, function(data) {
                if (data.message == "success") {
                    refresh_modal('duplicate_account_whitelist', "/user_management/viewDuplicateAccountWhitelist/", '<?=lang('whitelist')?>');
                }
            });
        }
        return false;
    }

    function add_player_whitelist() {
        var username = $("#username").val();
        var url = "/user_management/addDuplicateAccountWhitelist/"+username;
        if(username) {
            $.post(url, username, function(data) {
                if (data.message == "success") {
                    alert(data.message);
                    refresh_modal('duplicate_account_whitelist', "/user_management/viewDuplicateAccountWhitelist/", '<?=lang('whitelist')?>');
                } else {
                    $('.msg-information').text(data.message);
                    $('.msg-information').addClass('text-danger');
                    $('.btn-confirmation').hide();
                }
            });
        }
        return false;
    }
</script>