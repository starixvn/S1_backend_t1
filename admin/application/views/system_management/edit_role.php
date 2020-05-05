<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">

            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt"><i class="icon-key"></i> <?=lang('lang.edit') . ' ' . lang('system.word31');?>
                    <button class="btn btn-default btn-sm pull-right" id="button_add_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_add_up"></span></button>
                </h3>
            </div>

            <div class="panel-body" id="add_panel_body">
                <form method="post" action="<?=BASEURL . 'role_management/verifyEditRole/' . $roles['roleId']?>" autocomplete="off">
                    <input type="hidden" name="roleId" value="<?php echo $roles['roleId']; ?>" />
                    <div class="row">
                        <div class="col-md-6">
                        <input class="form-control" type="text" name="role_name" id="role_name" value="<?=$roles['roleName']?>">
                        </div>
                        <div class="col-md-6">
                        <?php if ($this->utils->isEnabledMDB()) { ?>
                            <a href="<?=site_url('/role_management/sync_role_to_mdb/'.$roles['roleId'])?>" class="btn btn-success btn-sm">
                                <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
                            </a>
                        <?php } ?>
                        </div>
                    </div>

                    <?php if($this->permissions->canAssignSuperAdmin()){ ?>
                        <input id="isAdmin" type="checkbox" name="isAdmin" value="1" <?php echo $isAdmin ? "checked" : ""; ?>/>
                        <label for="isAdmin"><?=lang('isAdmin');?></label>
                    <?php }else{?>
                        <label for="isAdmin"><?=lang('isAdmin');?> : <?php echo $isAdmin ? lang("True") : lang("False"); ?></label>
                    <?php }?>
                    <?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                    <?php echo form_error('functions', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                    <?php echo form_error('functions_giving', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                    <hr/>

                    <input type="text" name="functions_parent" value="" id="parent_with_child_selected" class="hidden"/>
                    <input type="text" name="functions_parent_giving" value="" id="parent_give_child_selected" class="hidden"/>

                    <div class="row">
                        <div class="col-md-6">
                            <label><?=lang('sys.ar3');?></label>
                            <br/><br/>
                            <input type="checkbox" onclick="checkAll(this.id);" id="all_use"/> <?=lang('sys.ar4');?>
                            <br/><br/>

                            <?php
                            $cnt = 0;

                            foreach ($functions as $value) {

                            if ($value['status'] == $const_status_disabled) {
                                continue;
                            }

                            if(!$this->utils->isEnabledFeature('display_newsletter_subscribe_btn')){
                                if($value['funcId'] == Roles::ROLE_ADJUST_NEWSLETTER_SUBSCRIPTION_STATUS){
                                    continue;
                                }
                            }

                            if(!$this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
                                if($value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REQUEST || $value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REVIEW){
                                    continue;
                                }
                            }else{
                                if($value['funcId'] == Roles::ROLE_APPROVE_DECLINE_WITHDRAW){
                                    continue;
                                }
                            }

                            if ($value['parentId'] == 0) {
                                if ($cnt != 0) {
                                    echo '</table>';
                                } ?>

                                <input type="checkbox" onclick="checkAll(this.id);" id="use_<?=$value['funcId']?>" class="all_use"/> <label><?=lang('role.' . $value['funcId']);?></label>
                                <hr/>
                                <table class="table table-bordered" >

                            <?php } else {
                                    if($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC) : ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="functions[]"  class="all_use use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>"
                                                               parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>"
                                                               onclick="uncheckRole(this.id);"
                                                            <?=($this->rolesfunctions->findIfFunctionExists($rolesfunctions,
                                                                    $value['funcId']) && empty(validation_errors())) ? 'checked' : ''?> >
                                                        <?=lang('role.' . $value['funcId']);?>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif;
                                 }
                                $cnt++;
                            }?>

                            </table>
                        </div>


                        <!--------------- start functions giving form ---------------------->
                        <div id="functions_giving" style="display:none;" class="col-md-6">
                            <label><?=lang('sys.ar5');?></label>
                            <br/><br/>
                            <input type="checkbox" onclick="checkAll(this.id);" id="all_give"/> <?=lang('sys.ar6');?>
                            <br/><br/>

                            <?php
                            $cnt = 0;

                            foreach ($functions as $value) {

                            if ($value['status'] == $const_status_disabled) {
                                continue;
                            }


                            if(!$this->utils->isEnabledFeature('display_newsletter_subscribe_btn')){
                                if($value['funcId'] == Roles::ROLE_ADJUST_NEWSLETTER_SUBSCRIPTION_STATUS){
                                    continue;
                                }
                            }

                            if(!$this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
                                if($value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REQUEST || $value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REVIEW){
                                    continue;
                                }
                            }else{
                                if($value['funcId'] == Roles::ROLE_APPROVE_DECLINE_WITHDRAW){
                                    continue;
                                }
                            }

                            if ($value['parentId'] == 0) {
                                if ($cnt != 0) {
                                   echo '</table>';
                                } ?>

                                <input type="checkbox" onclick="checkAll(this.id);" id="give_<?=$value['funcId']?>" class="all_give"/> <label><?=lang('role.' . $value['funcId']);?></label>
                                <hr/>

                                <table class="table table-bordered">
                            <?php } else {
                                         if($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC) : ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="functions_giving[]" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>"
                                                               onclick="uncheckRole(this.id);" <?=($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId']) && empty(validation_errors())) ? 'checked' : ''?>> <?=lang('role.' . $value['funcId']);?>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif;
                                    }
                                $cnt++;
                                } ?>
                            </table>
                        </div>
                    </div>


                    <div style="text-align:right;">
                        <?php if($this->utils->isEnabledFeature('only_admin_modified_role')) : ?>
                            <?php if($isAdminRole) : ?>
                                <?php echo '<input class="btn btn-sm btn-info save_roles" type="submit" value="' . lang('Save') . '">';?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo '<input class="btn btn-sm btn-info save_roles" type="submit" value="' . lang('Save') . '">';?>
                        <?php endif; ?>
                        <?php echo '<input type="button" value="' . lang('lang.cancel') . '" class="btn btn-sm btn-default" onclick="history.back();" />';?>
                    </div>

                </form>
            </div>
            <div class="panel-footer"></div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.save_roles').click(function(){
            var selected = [];
            $('.all_use:checked').each(function() {
                if($.inArray($(this).attr('parent_id'), selected) === -1) selected.push($(this).attr('parent_id'));
            });
            $('#parent_with_child_selected').val(selected);

            var selected_give = [];
            $('.all_give:checked').each(function() {
                if($.inArray($(this).attr('parent_id'), selected_give) === -1) selected_give.push($(this).attr('parent_id'));
            });
            $('#parent_give_child_selected').val(selected_give);
        });

    });
</script>