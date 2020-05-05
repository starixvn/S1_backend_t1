<div class="clearfix">
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <table class="table table-bordered" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th class="active"><?=lang('PEP（C6）')?></th>
                        <th class="active"><?=lang('Score')?></th>
                        <th class="active"><?=lang('Current PEP Status')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $c6Match = false; 
                        $afterKYCHeader = true;
                    ?>
                    <?php if(!empty($rules)) : ?>
                        <?php foreach ($rules as $data) { ?>
                            <?php if(!isset($data['manual_update'])) : ?>
                                <tr>
                                    <td><?=lang($data['rule_name'])?></td>
                                    <td><?=$data['risk_score']?></td>
                                    <td><input type="radio" id="player_selected_status" name="player_selected_status" value="<?=$data['rule_name']?>" <?=($current_c6_status == $data['rule_name']) ? "checked" : "" ?> ></td>    
                                </tr>
                            <?php else: ?>
                                <?php if($afterKYCHeader) :?>
                                     <tr>
                                        <td colspan="3" align="center" style="font-weight:bold"><?=lang("After Assessment or Documents Received")?></td>  
                                    </tr>
                                    <?php $afterKYCHeader = false ?>
                                <?php endif; ?>
                                <?php if($data['manual_update'] && $this->utils->isEnabledFeature('enable_c6_acuris_api_authentication')) : ?>
                                    <tr>
                                        <td><?=lang($data['rule_name'])?></td>
                                        <td><?=$data['risk_score']?></td>
                                        <td><input type="radio" id="player_selected_status_manual" name="player_selected_status_manual" value="<?=$data['rule_name']?>" <?=($current_c6_status == $data['rule_name']) ? "checked" : "" ?> ></td>    
                                    </tr>
                                <?php endif; ?>
                            <?php endif;?>
                                <?php if($current_c6_status == $data['rule_name']) {?>
                                    <?php $c6Match = true; ?>
                                    <input type="hidden" id="current_c6_status" value="<?=$data['rule_name']?>">
                                <?php } ?>
                            
                        <?php } ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(!$c6Match && $this->utils->isEnabledFeature('enable_c6_acuris_api_authentication')) : ?>
            <label class="text-danger" id="lbl_msg"><?= lang("C6 Authentication API is enable please check the C6 risk score criteria."); ?></label>
        <?php endif; ?>
    </div>

    <center id="btn_c6_manual"></center>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#risk_score').text('<?=$risk_level?> / <?=$total_score?>');
        $('#allowed_withdrawal_status').text('<?=$allowed_withdrawal_status?>');
        $('#c6_status').text('<?=$current_c6_status?>');
        <?php if (!$this->permissions->checkPermissions('update_c6_status') || $this->utils->isEnabledFeature('enable_c6_acuris_api_authentication')) { ?>
            $( "input[name='player_selected_status']" ).attr( 'disabled', true );
        <?php } ?>
    });

    $("input[name='player_selected_status']").change(function () {
        $('#btn_c6_manual').empty();
        if($("input[name='player_selected_status']:checked").val() != $("#current_c6_status").val()) {
            $('#btn_c6_manual').append($('<input />',{'type': 'submit','class': 'btn btn-primary submit_btn btn-sm', 'value' : '<?=lang('Update Status Manually');?>','onclick' : 'manual_player_pep_status()','title' : '<?=lang("manual.info")?>'}));
        }
    });

    $("input[name='player_selected_status_manual']").change(function () {
        $('#btn_c6_manual').empty();
        if($("input[name='player_selected_status_manual']:checked").val() != $("#current_c6_status").val()) {
            $('#btn_c6_manual').append($('<input />',{'type': 'submit','class': 'btn btn-primary submit_btn btn-sm', 'value' : '<?=lang('Update Status Manually');?>','onclick' : 'manual_player_c6_status_acuris()','title' : '<?=lang("manual.info")?>'}));
        }
    });

    function manual_player_pep_status() {
        selected_status = $("input[name='player_selected_status']:checked").val();
        $.post("/player_management/update_c6_status/<?=$playerId?>", { c6_status: selected_status }, function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_c6/<?=$playerId?>','<?=lang('Player C6')?>');
            } else {
                alert(result.message);
            }
        });
        
    }

    function manual_player_c6_status_acuris() {
        selected_status = $("input[name='player_selected_status_manual']:checked").val();
        $.post("/player_management/update_c6_status/<?=$playerId?>", { c6_status: selected_status }, function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_c6/<?=$playerId?>','<?=lang('Player C6')?>');
            } else {
                alert(result.message);
            }
        });
        
    }
</script>
