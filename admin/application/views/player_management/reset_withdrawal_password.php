<form method="POST" action="<?=site_url('player_management/playerResetWithdrawalPassword/' . $player['playerId'])?>" autocomplete="off">
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <h4><?=lang('Withdraw Reset Message')?></h4>
                        <div class="input-group">
                            <input class="span_value form-control" type="text" id="pwd" name="password" required placeHolder="<?=lang('member.enter.password')?>">

                            <span class="input-group-btn">
                                <button class="btn btn-default btn-md" type="button" id="reset_from"
                                        data-toggle="tooltip" data-placement="top" onclick="getGeneratePassword();" title="<?=lang('member.generate.random.password');?>">
                                    <i class="glyphicon glyphicon-refresh"></i>
                                </button>
                            </span>
                        </div>
                    </center>
                    <div class="col-md-11">
                        <center><span style="color:red;"><?=form_error('hiddenPassword');?></span></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <center>
        <input type="hidden" name="hiddenPassword"  class="form-control">
        <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('lang.reset');?>">
        <a href="<?=site_url('player_management/userInformation/' . $player['playerId'])?>" class="btn btn-sm btn-warning btn-md" id="reset_password"><?=lang('lang.cancel');?></a>
    </center>
</form>
<script type="text/javascript">
    function getGeneratePassword() {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $.ajax({
            'url' : base_url +'player_management/getGeneratePassword',
            'type' : 'GET',
            'dataType' : "html",
            'success' : function(data){
                $('.span_value').val('***********');
                $('#pwd').val(data);
            }
        });
    }
</script>