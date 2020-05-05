<div class="clearfix">
    <?php if ($this->permissions->checkPermissions('update_kyc_status')) { ?>
        <input type="button" name="" class="btn btn-success submit_btn btn-sm" value="<?=lang('Generate Automatic')?>" onclick="automatic_player_kyc_status();" title="<?=lang('Generate Automatic')?>">
    <?php } ?>

    <?php if ($this->utils->isEnabledFeature('show_upload_documents') && $this->permissions->checkPermissions('kyc_attached_documents')) { ?>
        <input type="button" name="" class="btn btn-primary submit_btn btn-sm pull-right" value="<?=lang('attached_file')?>" onclick="modal('/player_management/player_attach_document/<?=$playerId?>','Attached Document')" title="<?=lang('attached_file')?>">
    <?php } ?>

    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <table class="table table-bordered" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th class="active"><?=lang('KYC Rate')?></th>
                        <th class="active"><?=lang('sys.description')?></th>
                        <!--<th class="active"><?=lang('sys.gd16')?></th>-->
                        <th class="active"><?=lang('Player KYC Status')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kyc_status as $data) { ?>
                    <tr>
                        <td><?=lang($data['rate_code'])?></td>
                        <td><?=$data['description']?></td>
                        <td><input type="radio" id="player_selected_status" name="player_selected_status" value="<?=$data['id']?>" <?=($data['current_kyc_status']) ? "checked" : "" ?> ></td>
                    </tr>
                    <?php if($data['current_kyc_status']) {?>
                        <input type="hidden" id="current_kyc_status" value="<?=$data['id']?>">
                    <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <label><?=$generated_by?></label>
    </div>

    <center id="btn_kyc_manual"></center>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#kyc_status').text('<?=$current_kyc_level.' / '.$current_kyc_status?>');
        $('#allowed_withdrawal_status').text('<?=$allowed_withdrawal_status?>');

        <?php if (!$this->permissions->checkPermissions('update_kyc_status')) { ?>
            $( "input[name='player_selected_status']" ).attr( 'disabled', true );
        <?php } ?>
    });

    $("input[name='player_selected_status']").change(function () {
        $('#btn_kyc_manual').empty();
        if($("input[name='player_selected_status']:checked").val() != $("#current_kyc_status").val()) {
            $('#btn_kyc_manual').append($('<input />',{'type': 'submit','class': 'btn btn-primary submit_btn btn-sm', 'value' : '<?=lang('Update Status Manually');?>','onclick' : 'manual_player_kyc_status()','title' : '<?=lang("manual.info")?>'}));
        }
    });

    function manual_player_kyc_status() {
        selected_status = $("input[name='player_selected_status']:checked").val();
        $.post("/player_management/manual_player_kyc_status/<?=$playerId?>/"+selected_status, function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_kyc/<?=$playerId?>','<?=lang('player kyc')?>');
            } else {
                alert(result.message);
            }
        });

    }

    function automatic_player_kyc_status() {
        $.post("/player_management/automatic_player_kyc_status/<?=$playerId?>/", function(result){
            if(result.status == "success"){
                alert(result.message);
                modal('/player_management/player_kyc/<?=$playerId?>','<?=lang('player kyc')?>');
            } else {
                alert(result.message);
            }
        },'json');

    }
</script>
