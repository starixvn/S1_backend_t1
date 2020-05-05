<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="icon-key"></i> <?= lang('system.word25');  ?> </h3>
				<button class="btn btn-default btn-sm pull-right" id="button_add_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_add_up"></span></button>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="add_panel_body">
				<form method="post" action="<?= BASEURL . 'role_management/editRole/' . $roles['roleId']?>" autocomplete="off">
					<input class="form-control" type="text" name="role_name" id="role_name" value="<?= $roles['roleName']?>" disabled>
					<?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					<hr/>
					<div class="row">
						<div class="col-md-6">
							<label><?= lang('sys.ar3'); ?></label>
							<br/><br/>
							<input disabled type="checkbox" onclick="checkAll(this.id);" id="all_use"/> <?= lang('sys.ar4'); ?>
							<br/><br/>

							<?php
								$cnt = 0;

								foreach ($functions as $value) {
									if ($value['parentId'] == 0) {
										if ($cnt != 0) {
							?>
											</table>
							<?php
										}
							?>
										<input disabled type="checkbox" onclick="checkAll(this.id);" id="use_<?= $value['funcId'] ?>" class="all_use"/> <label><?= lang('role.' . $value['funcId']); ?></label>
										<hr/>

										<table class="table table-bordered">
							<?php 
									} else {
							?>
										<tr>
											<td>
												<div class="checkbox">
													<label>
										     			 <input disabled type="checkbox" name="functions[]"  class="all_use use_<?= $value['parentId'] ?>" parent="use_<?= $value['parentId'] ?>" value="<?= $value['funcId'] ?>" id="use_<?= $value['funcId']?>" onclick="uncheckRole(this.id);" <?= ($this->rolesfunctions->findIfFunctionExists($rolesfunctions, $value['funcId']) && empty(validation_errors())) ? 'checked' : '' ?> > <?= lang('role.' . $value['funcId']); ?>
										  			</label>
										  		</div>
										  	</td>
									  	</tr>
							<?php
									}

									$cnt++;
								}
							?>

							</table>
						</div>

						<div id="functions_giving" style="display:none;" class="col-md-6">
							<label><?= lang('sys.ar5'); ?></label>
							<br/><br/>
							<input disabled type="checkbox" onclick="checkAll(this.id);" id="all_give"/> <?= lang('sys.ar6'); ?>
							<br/><br/>

							<?php
									$cnt = 0;

									foreach ($functions as $value) {
										if ($value['parentId'] == 0) {
											if ($cnt != 0) {
							?>
											</table>
							<?php
											}
							?>
											<input disabled type="checkbox" onclick="checkAll(this.id);" id="give_<?= $value['funcId'] ?>" class="all_give"/> <label><?= lang('role.' . $value['funcId']); ?></label>
											<hr/>

										<table class="table table-bordered">
							<?php
										} else {
							?>
											<tr>
												<td>
													<div class="checkbox">
														<label>
											      			<input disabled type="checkbox" name="functions_giving[]" class="all_give give_<?= $value['parentId'] ?>" parent="give_<?= $value['parentId'] ?>" value="<?= $value['funcId'] ?>" id="give_<?= $value['funcId']?>" onclick="uncheckRole(this.id);" <?= ($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId']) && empty(validation_errors())) ? 'checked' : '' ?>> <?= lang('role.' . $value['funcId']); ?>
											  			</label>
											  		</div>
											  	</td>
										  	</tr>
							<?php
										}
										
										$cnt++;
									}
							?>

							</table>
						</div>
					</div>


				</form>
			</div>

			<div class="panel-footer"></div>

		</div>
	</div>
</div>