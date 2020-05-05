<div class="panel panel-primary">

    <div class="panel-heading">
        <a href="<?='/player_management/accountProcess'?>" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default' ?>" id="account_process">
            <span class="glyphicon glyphicon-remove"></span>
        </a>
        <h4><i class="icon-user-plus"></i> <?=lang('player.mp06')?></h4>
    </div>

    <div class="panel-body" id="player_panel_body">
        <form action="<?='/player_management/verifyAddAccountProcess'?>" class="form-horizontal" id="verifyAddAccountProcess" method="POST" autocomplete="off" >

            <input type="hidden" name="type_code" id="type_code" value="<?=$type_code?>">

            <div class="form-group form-group-sm">
                <i class="col-md-offset-3 col-md-8 text-danger"><?=lang('reg.02')?></i>
            </div>

            <div class="form-group form-group-sm">
                <label for="username" class="col-md-3 control-label"><?=lang('Username')?> <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="text" name="username" id="username" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-Z-0-9]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" required="required">
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="count" class="col-md-3 control-label"><?=lang('player.mp03')?> <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="number" name="count" id="count" class="form-control" min="1" required="required" onkeypress="return isNumberKey(event)">
                    <span class="help-block"><?=lang('player.mp16')?></span>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="password" class="col-md-3 control-label"><?=lang('player.mp07')?> <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="password" name="password" id="password" class="form-control" minLength="6" maxLength="20" required="required">
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="agent_name" class="col-md-3 control-label"><?=lang('Parent Agent Username')?></label>
                <div class="col-md-8">
                    <input type="text" name="agent_name" id="agent_name" class="form-control" value="<?=isset($agent_name)? $agent_name:'';?>" />
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="affiliate_name" class="col-md-3 control-label"><?=lang('Parent Affiliate Username')?></label>
                <div class="col-md-8">
                    <input type="text" name="affiliate_name" id="affiliate_name" class="form-control"
                    value="<?=isset($affiliate_name)? $affiliate_name:'';?>" />
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="language" class="col-md-3 control-label"><?=lang('system.word3')?> <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <select name="language" id="language" class="form-control" required="required">
                        <option value=""><?=lang('lang.select')?></option>
                        <option value="English"><?=lang('English')?></option>
                        <option value="Chinese"><?=lang('Chinese')?></option>
                        <option value="Indonesian"><?=lang('Indonesian')?></option>
                        <option value="Vietnamese"><?=lang('Vietnamese')?></option>
                        <option value="Thai"><?=lang('Thai')?></option>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label for="description" class="col-md-3 control-label"><?=lang('player.mp04')?></label>
                <div class="col-md-8">
                    <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <span class="col-md-offset-3 col-md-8 text-danger" id="error"></span>
            </div>

            <div class="form-group form-group-sm">
                <div class="col-md-offset-3 col-md-8">
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?> " id="vap_button" onclick=" // Gitlab issue #1022, 5/04/2017
                        // jquery block in this file mysteriously fails to run, so put code here, dirty indeed
                        if (vap_count < 1) {
                            var vuform = $(this).parents('form');
                            var username = $(vuform).find('#username').val();
                            var count = parseInt($(vuform).find('#count').val());
                            var password = $(vuform).find('#password').val();
                            var lang = $(vuform).find('#language option:selected').val();
                            if (username.length > 0 && count > 0 && password.length > 0 && lang.length > 0) {
                                ++vap_count;
                                vuform.submit();
                            }
                        }
                        else {
                            $(this).attr('disabled', 1);
                        }"
                    >
                        <?=lang('Save')?>
                    </button>
                </div>
            </div>

        </form>
    </div>
    <div class="panel-footer"></div>
</div>

