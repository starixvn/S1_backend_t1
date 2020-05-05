<?php if ($this->utils->isEnabledFeature('use_role_permission_management_v2')): ?>
	<input type="hidden" name="version2" id="version2" value="true">
<?php endif ?>

<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="icon-key"></i> <?= lang('system.word25');  ?> </h3>
				<button class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-default btn-sm'?>" id="button_add_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_add_up"></span></button>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="add_panel_body">
				<form method="post" action="<?= BASEURL . 'role_management/editRole/' . $roles['roleId']?>" autocomplete="off">
					<input class="form-control" type="text" name="role_name" id="role_name" value="<?= $roles['roleName']?>" disabled>
					<?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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

		                    						<input disabled type="checkbox" onclick="checkAll(this.id);" id="use_<?=$value['funcId']?>" class="all_use"/>
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
	                													<input disabled type="checkbox" name="functions[]"  class="all_use non-parent use_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>"
		                												parent="use_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="use_<?=$value['funcId']?>"
		                												onclick="uncheckRole(this.id);" <?=($this->rolesfunctions->findIfFunctionExists($rolesfunctions, $value['funcId']) && empty(validation_errors())) ? 'checked' : ''?> >
		                												<?= $this->utils->displayPermission($value['funcCode'], lang('role.' . $value['funcId'])) ?>
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


		                						if ($value['parentId'] == 0 && $value['funcId'] == $parent_function['funcId'])
		                						{
		                							if ($cnt != 0) {
		                								echo '</table>';
		                							} ?>

		                							<input disabled type="checkbox" onclick="checkAll(this.id);" id="give_<?=$value['funcId']?>" class="all_give"/>
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
                													<input disabled type="checkbox" name="functions_giving[]" class="all_give give_<?=$value['parentId']?>" parent_id="<?=$value['parentId']?>" parent="give_<?=$value['parentId']?>" value="<?=$value['funcId']?>" id="give_<?=$value['funcId']?>" onclick="uncheckRole(this.id);" <?=($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId']) && empty(validation_errors())) ? 'checked' : ''?>>
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


				</form>
			</div>

			<div class="panel-footer"></div>

		</div>
	</div>
</div>

<script>
	$(function(){
		calculateActiveInactiveCounter();

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