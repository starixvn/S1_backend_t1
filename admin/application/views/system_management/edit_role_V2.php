<?php if ($this->utils->isEnabledFeature('use_role_permission_management_v2')): ?>
	<input type="hidden" name="version2" id="version2" value="true">
<?php endif ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">

            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt"><i class="icon-key"></i> <?=lang('lang.edit') . ' ' . lang('system.word31');?>
                    <button class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>" id="button_add_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_add_up"></span></button>
                </h3>
            </div>

            <div class="panel-body" id="add_panel_body">
                <form method="post" action="<?=BASEURL . 'role_management/verifyEditRole/' . $roles['roleId']?>" autocomplete="off">
                    <input type="hidden" name="roleId" value="<?php echo $roles['roleId']; ?>" />
                    <input class="form-control" type="text" name="role_name" id="role_name" value="<?=$roles['roleName']?>">
                    <br>
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

                    <button type="button" id="collapse-all" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" data-action='open'><?=lang('Expand / Close All')?></button>

                    <br><br>

                    <div class="panel-group" id="permission_accordion">
                    	<?php foreach ($parent_functions as $key => $parent_function): ?>
                    	<div class="panel panel-default parent-panel">
                    		<div class="panel-heading">
                    			<h4 class="panel-title">
                    				<a data-toggle="collapse" style="text-decoration: none" data-parent="#<?=$parent_function['funcCode']?>" href="#<?=$parent_function['funcCode']?>">
                    				<?=lang('role.' . $parent_function['funcId']);?> &nbsp;&nbsp;
                    				<span class="text text-success" style="font-size: 12pt"><?=lang('lang.active')?>: <span class="active_count"></span></span>

                    				<span class="text text-warning" style="font-size: 12pt"><?=lang('lang.inactive')?>: <span class="inactive_count"></span></span>
                    				</a>
                    			</h4>
                    		</div>
                    		<div id="<?=$parent_function['funcCode']?>" class="panel-collapse collapse">
                    			<div class="panel-body">
		                    		<div class="row">
		                    			<div class="col-md-6">
		                    				<label><?=lang('sys.ar3');?></label>
		                    				<br/><br/>

		                    				<?php
		                    				$cnt = 0;

		                    				foreach ($functions as $value) {

		                    					if($value['parentId'] != 0 && $value['parentId'] != $parent_function['funcId']){


		                    						$valid = false;
		                    						foreach($functions as $item) {


		                    							if (($value['parentId'] == $item['funcId'] && $item['parentId'] == $parent_function['funcId'])) {
		                    								$valid = true;
		                    								break;
		                    							}
		                    						}

		                    						if(!$valid)
		                    							continue;
		                    					}


		                    					if ($value['status'] == $const_status_disabled) {
													continue;
												}

                                                if(!$this->utils->isEnabledFeature('verification_reference_for_player')){
                                                    if($value['funcId'] == Roles::ROLE_ADJUST_PLAYER_ACCOUNT_VERIFY_STATUS){
                                                        continue;
                                                    }
                                                }

												if(!$this->utils->isEnabledFeature('display_newsletter_subscribe_btn')){
													if($value['funcId'] == Roles::ROLE_ADJUST_NEWSLETTER_SUBSCRIPTION_STATUS){
														continue;
													}
												}

		                    					if(!$this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission'))
		                    					{
		                    						if($value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REQUEST || $value['funcId'] == Roles::ROLE_WITHDRAW_PENDING_REVIEW)
		                    							continue;
		                    					}
		                    					else
		                    					{
		                    						if($value['funcId'] == Roles::ROLE_APPROVE_DECLINE_WITHDRAW)
		                    							continue;
		                    					}

		                    					if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId'])
		                    					{
		                    						if ($cnt != 0) {
		                    							echo '</table>';
		                    						} ?>

		                    						<input type="checkbox" onclick="checkAll(this.id);" id="use_<?=$value['funcId']?>" class="all_use" />
		                    						<label>
			                    						<span style="font-size:16px; font-weight:bold;"><?=lang('sys.ar6');?></span>
			                    					</label>
		                    						<hr/>
		                    						<table class="table table-bordered" >

		                					<?php }
		                						elseif($value['parentId'] == 0)
		                							continue;
		                						else
		                						{
		                							if($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC) : ?>
		                								<tr>
		                									<td>
		                										<div class="">
		                											<label>
		                												<?php
		                													$checked_permission = ($this->rolesfunctions->findIfFunctionExists($rolesfunctions, $value['funcId']) && empty(validation_errors())) ? 'checked' : '';
		                													if($this->utils->isDeprecatedPermission($value['funcCode'])):
		                												?>
		                													<input type="checkbox" class="all_use non-parent use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>" onclick="return false;" readonly="readonly" <?=$checked_permission?> >
		                													<?php if($checked_permission) :?>
		                														<input type="hidden" name="functions[]" value="<?=$value['funcId']?>" />
		                													<?php endif ?>
		                												<?php else:?>
	                														<input type="checkbox" name="functions[]"  class="all_use non-parent use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>" onclick="uncheckRole(this.id);" <?=$checked_permission?>>

	                													<?php endif; ?>
	                													<?= $this->utils->displayPermission($value['funcCode'], lang('role.' . $value['funcId'])) ?>
		                											</label>
		                										</div>
		                									</td>
		                								</tr>
		                						<?php endif; // EOF if($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC)
		                						} // EOF if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId'])
		                							$cnt++;
											} // EOF foreach ($functions as $value) ?>

		                    						</table>
		                    			</div>


		                				<!--------------- start functions giving form ---------------------->
		                				<div style="display:none;" class="functions_giving col-md-6">
		                					<label><?=lang('sys.ar5');?></label>
		                					<br/><br/>

		                					<?php
		                					$cnt = 0;

		                					foreach ($functions as $value) {

		                						if($value['parentId'] != 0 && $value['parentId'] != $parent_function['funcId']){


		                    						$valid = false;
		                    						foreach($functions as $item) {


		                    							if ($value['parentId'] == $item['funcId'] && $item['parentId'] == $parent_function['funcId']) {
		                    								$valid = true;
		                    								break;
		                    							}
		                    						}

		                    						if(!$valid)
		                    							continue;
		                    					}

		                						if ($value['status'] == $const_status_disabled) {
		                							continue;
		                						}

                                                if(!$this->utils->isEnabledFeature('verification_reference_for_player')){
                                                    if($value['funcId'] == Roles::ROLE_ADJUST_PLAYER_ACCOUNT_VERIFY_STATUS){
                                                        continue;
                                                    }
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


		                						if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId'])
		                						{
		                							if ($cnt != 0) {
		                								echo '</table>';
		                							} ?>

		                							<input type="checkbox" onclick="checkAll(this.id);" id="give_<?=$value['funcId']?>" class="all_give"/>
		                							<label>
			                    						<span style="font-size:16px; font-weight:bold;"><?=lang('sys.ar6');?></span>
			                    					</label>
		                							<hr/>

		                							<table class="table table-bordered">
		                					<?php }
		                					elseif($value['parentId'] == 0)
		            							continue;
		                					else
		                					{
		        								if($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC) : ?>
		        									<tr>
		        										<td>
		        											<div class="">
		        												<label>
	                												<?php
	                													$checked_permission = ($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId']) && empty(validation_errors())) ? 'checked' : '';
	                													if($this->utils->isDeprecatedPermission($value['funcCode'])):
	                												?>
	                													<input type="checkbox" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>" onclick="return false;" readonly="readonly" <?=$checked_permission?>>
	                													<?php if($checked_permission) :?>
	                														<input type="hidden" name="functions_giving[]" value="<?=$value['funcId']?>" />
	                													<?php endif ?>
	                												<?php else:?>
                														<input type="checkbox" name="functions_giving[]" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>" onclick="uncheckRole(this.id);" <?=$checked_permission?>>

                													<?php endif; ?>
                													<?= $this->utils->displayPermission($value['funcCode'], lang('role.' . $value['funcId'])) ?>
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
                    			</div>
                    		</div>
                    	</div>
                    	<?php endforeach; ?>
                    </div>

                    <div style="text-align:right;">
						<input type="button" value="<?=lang('lang.cancel')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" onclick="history.back();">
                        <?php if($this->utils->isEnabledFeature('only_admin_modified_role')) : ?>
                            <?php if($isAdminRole) : ?>
                                <input class="btn btn-sm save_roles <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?=lang('Save')?>">
                            <?php endif; ?>
                        <?php else: ?>
                            <input class="btn btn-sm save_roles <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?=lang('Save')?>">
                        <?php endif; ?>
                    </div>

                </form>
            </div>
            <div class="panel-footer"></div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function(){

    	calculateActiveInactiveCounter();

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

        $('#collapse-all').on('click', function () {

        	if($(this).data('action') == 'open'){
		    	$('#permission_accordion .panel-collapse').collapse('show');
		    	$(this).data('action','close');
        	}
        	else{
        		$('#permission_accordion .panel-collapse').collapse('hide');
		    	$(this).data('action','open');
        	}

		});

    });

</script>