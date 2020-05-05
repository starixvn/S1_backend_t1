<?php if ($this->utils->isEnabledFeature('use_role_permission_management_v2')): ?>
	<input type="hidden" name="version2" id="version2" value="true">
<?php endif ?>

<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="glyphicon glyphicon-plus-sign"></i> <?=lang('sys.ar1');?> </h3>
			</div>

			<form class="form-inline" method="post" action="<?=site_url('role_management/verifyAddRole')?>" autocomplete="off">
			<div class="panel-body" id="add_panel_body">
					<div class="row">
						<div class="col-md-12">
						  	<div class="form-group">
							<label for="role_name"><?=lang('sys.ar2');?></label>
							<input class="form-control" class="form-control input-sm" type="text" name="role_name" id="role_name" value="<?php echo set_value('role_name'); ?>">
							<?php if($this->permissions->canAssignSuperAdmin()){ ?>
							<input id="isAdmin" type="checkbox" name="isAdmin" value="1" />
							<label for="isAdmin"><?=lang('isAdmin');?></label>
							<?php }?>

							<input type='button' class="btn form-control btn_enable_giving <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?php echo lang('Enable Giving Permission'); ?>">
							<?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<?php echo form_error('functions', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<?php echo form_error('functions_giving', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
						</div>
					</div>
                <input type="text" name="functions_parent" value="" id="parent_with_child_selected" class="hidden"/>
                <input type="text" name="functions_parent_giving" value="" id="parent_give_child_selected" class="hidden"/>
				<hr/>

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

											foreach ($functions as $key => $value):
												if($value['parentId'] != 0 && $value['parentId'] != $parent_function['funcId']){

			                						$valid = false;
			                						foreach($functions as $item) {


			                							if ($value['parentId'] == $item['funcId'] && $item['parentId'] == $parent_function['funcId']) {
			                								$valid = true;
			                								break;
			                							}
			                						}

			                						if(!$valid) continue;
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
			                				?>
												<?php if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId']): ?>

													<?php if ($cnt != 0): ?>
														</table>
													<?php endif ?>

													<input type="checkbox" onclick="checkAll(this.id);" id="use_<?=$value['funcId']?>" class="all_use"/>
													<span style="font-size:16px; font-weight:bold;"><?=lang('sys.ar6');?></span>
												  	<hr/>
													<table class="table table-bordered">

												<?php elseif($value['parentId'] == 0): continue;?>
												<?php else: ?>

													<?php if ($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC ): ?>
														<tr>
															<td>
																<div class="checkbox">
																	<label>

				                                                       <input type="checkbox" name="functions[]" class="all_use non-parent use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>" onclick="<?=($this->utils->isDeprecatedPermission($value['funcCode']) ? "return false;" : "uncheckRole(this.id);")?>">
				                                                       <?= $this->utils->displayPermission($value['funcCode'], lang('role.' . $value['funcId'])) ?>
																	</label>
																</div>
														  	</td>
													  	</tr>
													<?php endif ?>

												<?php endif; $cnt++; ?>

											<?php endforeach ?>
													</table>
										</div>


										<div id="functions_giving" class="functions_giving col-md-6" style="display:none;">
											<label><?=lang('sys.ar5');?></label>
											<br/><br/>
											<?php
											$cnt = 0;

											foreach ($functions as $key => $value):
												if($value['parentId'] != 0 && $value['parentId'] != $parent_function['funcId']){

			                						$valid = false;
			                						foreach($functions as $item) {


			                							if ($value['parentId'] == $item['funcId'] && $item['parentId'] == $parent_function['funcId']) {
			                								$valid = true;
			                								break;
			                							}
			                						}

			                						if(!$valid) continue;
			                					}
			                				?>
												<?php if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId']): ?>

													<?php if ($cnt != 0): ?>
														</table>
													<?php endif ?>

													<input type="checkbox" onclick="checkAll(this.id);" id="give_<?=$value['funcId']?>" class="all_give"/>
													<span style="font-size:16px; font-weight:bold;"><?=lang('sys.ar6');?></span>
													<hr/>
													<table class="table table-bordered">

												<?php elseif($value['parentId'] == 0): continue;?>
												<?php else: ?>

													<?php if ($value['funcId'] != Roles::BANK_INFO_CONTROL_FUNC): ?>
														<tr>
															<td>
																<div class="checkbox">
																	<label>
	                													<input type="checkbox" name="functions_giving[]" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>"
				                                                               parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>" onclick="<?=($this->utils->isDeprecatedPermission($value['funcCode']) ? "return false;" : "uncheckRole(this.id);")?>">
				                                                        <?= $this->utils->displayPermission($value['funcCode'], lang('role.' . $value['funcId'])) ?>
														  			</label>
																</div>
														  	</td>
													  	</tr>
													<?php endif ?>

												<?php endif; $cnt++; ?>

											<?php endforeach ?>
													</table>
										</div>

									</div>


								</div>
							</div>
						</div>
					<?php endforeach ?>
				</div>
			</div>

			<div class="panel-footer">
				<div class="pull-right">
					<input class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" type="button" value="<?=lang('lang.cancel')?>"  onclick="history.back();">
					<input class="btn save_roles <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?=lang('lang.save')?>">
				</div>
				<div class="clearfix"></div>
			</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('.btn_enable_giving').click(function(e){
	if($('#use_2').is(':checked')){
		//change text
		$('.btn_enable_giving').val("<?php echo lang('Enable Giving Permission'); ?>");
	}else{
		$('.btn_enable_giving').val("<?php echo lang('Disable Giving Permission'); ?>");
	}
	$('#use_2').click();

});

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

