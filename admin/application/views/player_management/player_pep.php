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
                        $pepMatch = false; 
                        $afterKYCHeader = true;
                    ?>
                    <?php if(!empty($rules)) : ?>
                        <?php foreach ($rules as $data) { ?>
                            <?php if(!isset($data['manual_update'])) : ?>
                                <tr>
                                    <td><?=lang($data['rule_name'])?></td>
                                    <td><?=$data['risk_score']?></td>
                                    <td><input type="radio" id="player_selected_status" name="player_selected_status" value="<?=$data['rule_name']?>" <?=($current_pep_status == $data['rule_name']) ? "checked" : "" ?> ></td>    
                                </tr>
                            <?php else: ?>
                                <?php if($afterKYCHeader) :?>
                                     <tr>
                                        <td colspan="3" align="center" style="font-weight:bold"><?=lang("After Assessment and KYC Documents Received")?></td>  
                                    </tr>
                                    <?php $afterKYCHeader = false ?>
                                <?php endif; ?>
                                <?php if($data['manual_update'] && $this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->utils->getConfig('enable_change_player_pep_status_when_binding_ID3')) : ?>
                                    <tr>
                                        <td><?=lang($data['rule_name'])?></td>
                                        <td><?=$data['risk_score']?></td>
                                        <td><input type="radio" id="player_selected_status_manual" name="player_selected_status_manual" value="<?=$data['rule_name']?>" <?=($current_pep_status == $data['rule_name']) ? "checked" : "" ?> ></td>    
                                    </tr>
                                <?php endif; ?>
                            <?php endif;?>
                                <?php if($current_pep_status == $data['rule_name']) {?>
                                    <?php $pepMatch = true; ?>
                                    <input type="hidden" id="current_pep_status" value="<?=$data['rule_name']?>">
                                <?php } ?>
                            
                        <?php } ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(!$pepMatch && $this->utils->isEnabledFeature('enable_pep_gbg_api_authentication')) : ?>
            <label class="text-danger" id="lbl_msg"><?= lang("PEP Authentication API is enable please check the PEP risk score criteria."); ?></label>
        <?php endif; ?>
    </div>

    <center id="btn_kyc_manual"></center>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#pep_status').text('<?=$current_pep_status?>');
        $('#risk_score').text('<?=$risk_level?> / <?=$total_score?>');
        $('#allowed_withdrawal_status').text('<?=$allowed_withdrawal_status?>');
        <?php if (!$this->permissions->checkPermissions('update_pep_status') || $this->utils->isEnabledFeature('enable_pep_gbg_api_authentication')) { ?>
            $( "input[name='player_selected_status']" ).attr( 'disabled', true );
        <?php } ?>
    });

    $("input[name='player_selected_status']").change(function () {
        $('#btn_kyc_manual').empty();
        if($("input[name='player_selected_status']:checked").val() != $("#current_pep_status").val()) {
            $('#btn_kyc_manual').append($('<input />',{'type': 'submit','class': 'btn btn-primary submit_btn btn-sm', 'value' : '<?=lang('Update Status Manually');?>','onclick' : 'manual_player_pep_status()','title' : '<?=lang("manual.info")?>'}));
        }
    });

    $("input[name='player_selected_status_manual']").change(function () {
        $('#btn_kyc_manual').empty();
        if($("input[name='player_selected_status_manual']:checked").val() != $("#current_pep_status").val()) {
            $('#btn_kyc_manual').append($('<input />',{'type': 'submit','class': 'btn btn-primary submit_btn btn-sm', 'value' : '<?=lang('Update Status Manually');?>','onclick' : 'manual_player_pep_status_id3()','title' : '<?=lang("manual.info")?>'}));
        }
    });

    function manual_player_pep_status() {
        selected_status = $("input[name='player_selected_status']:checked").val();
        $.post("/player_management/update_pep_status/<?=$playerId?>", { pep_status: selected_status }, function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_pep/<?=$playerId?>','<?=lang('player PEP')?>');
            } else {
                alert(result.message);
            }
        });
        
    }

    function manual_player_pep_status_id3() {
        selected_status = $("input[name='player_selected_status_manual']:checked").val();
        $.post("/player_management/update_pep_status/<?=$playerId?>", { pep_status: selected_status }, function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_pep/<?=$playerId?>','<?=lang('player PEP')?>');
            } else {
                alert(result.message);
            }
        });
        
    }
</script>
