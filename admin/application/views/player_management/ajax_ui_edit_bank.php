<form method="POST" action="<?=BASEURL . 'player_management/verifyEditPlayerBankInfo'?>" class="form-horizontal">
    <div class="row" id="bank_form">
        <div class="col-md-12" id="toggleView">
            <div class="panel-primary">
                <div class="panel-body" id="bank_panel_body">
                    <input type="hidden" name="bank_details_id" value="<?=$bank_details['playerBankDetailsId'];?>"/>
                    <input type="hidden" name="bankname" value="<?=lang($bank_details['bankName']);?>"/>
                    <input type="hidden" name="player_id" value="<?=$bank_details['playerId'];?>"/>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bank_type_id"><i style="color:red">*</i> <?=lang('player.ui35');?>:</label>
                            <select name="bank_type_id" class="form-control" required>
                                <?php foreach ($bank_types as $key => $value) {?>
                                    <option value="<?=$value?>" <?=($bank_details['bankTypeId'] == $value) ? 'selected' : ''?> ><?=$key?></option>
                                <?php } ?>
                            </select>
                            <span style="color:red"><?=form_error('bank_type_id');?></span>
                        </div>
                        <div class="form-group">
                            <label for="bank_account_number"><i style="color:red">*</i> <?=lang('player.ui37');?>:</label>
                            <input type="text" required class="form-control number_only" id="bank_account_number" name="bank_account_number" value="<?=(set_value('bank_account_number') == null) ? $bank_details['bankAccountNumber'] : set_value('bank_account_number')?>"/>
                            <span id="bank_account_status" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                            <span style="color:red"><?=form_error('bank_account_number');?></span>
                        </div>
                        <div class="form-group">
                            <label for="bank_full_name"><?=lang('player.ui36');?>:</label>
                            <input type="text" class="form-control" name="bank_full_name" value="<?=(set_value('bank_full_name') == null) ? $bank_details['bankAccountFullName'] : set_value('bank_full_name')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="branch"><?=lang('player.ui54');?>:</label>
                            <input type="text" class="form-control" name="branch" value="<?=(set_value('branch') == null) ? $bank_details['branch'] : set_value('branch')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="bank_address"><?=lang('player.ui38');?>:</label>
                            <input type="text" class="form-control" name="bank_address" value="<?=(set_value('bank_address') == null) ? $bank_details['bankAddress'] : set_value('bank_address')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="province"><?=lang('Province');?>:</label>
                            <?php if($this->utils->isEnabledFeature('disable_chinese_province_city_select')){ ?>
                                <input type="text" class="form-control" name="province" value="<?=(set_value('province') == null) ? $bank_details['province'] : set_value('province')?>"/>
                            <?php } else { ?>
                                <select class="form-control province" name="province" id="inputProvince" data-value="<?=(set_value('province') == null) ? $bank_details['province'] : set_value('province')?>" >
                                    <option value=""></option>
                                </select>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="city"><?=lang('City');?>:</label>
                            <?php if($this->utils->isEnabledFeature('disable_chinese_province_city_select')){ ?>
                                <input type="text" class="form-control" name="city" value="<?=(set_value('city') == null) ? $bank_details['city'] : set_value('city')?>"/>
                            <?php } else { ?>
                            <select class="form-control city" name="city" id="inputCity" data-value="<?=(set_value('city') == null) ? $bank_details['city'] : set_value('city')?>" >
                                <option value=""></option>
                            </select>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <center>
                <input type="submit" id='btn_submit' class="btn btn-success" name="submit" value="<?=lang('lang.save');?>"/>
                <button data-dismiss="modal" aria-label="Close" class="btn btn-warning"><?=lang('lang.cancel');?></button>
            </center>
        </div>
    </div>
</form>

<script type="text/javascript" src="<?=$this->utils->jsUrl('province_city_select.js')?>" ></script>
<script type="text/javascript">
    $("#bank_account_number").keyup(function(e){
        e.preventDefault();
        var isDuplicate = false;
        $.ajax({
            'url' : base_url +'player_management/isPlayerBankAccountNumberExists/'+$(this).val()+'/0/0',
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                   if(data){
                        $("#bank_account_status").text("<?=lang('bankinfo.acctNo.error')?>");
                        $("#btn_submit").attr("disabled", true);
                   }else{
                        $("#bank_account_status").text("");
                        $("#btn_submit").attr("disabled", false);
                   }
               }
            },'json');
    });
    ProvinceCity.bind();
</script>
