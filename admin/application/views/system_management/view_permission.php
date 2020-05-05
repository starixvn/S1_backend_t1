<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="glyphicon glyphicon-tasks"></i> View Permission</h3>
				<button class="btn btn-info btn-sm pull-right" id="button_add_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_add_up"></span></button>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="add_panel_body">
				<form method="post" action="" autocomplete="off">
					<div class="well well-lg">
						<div class="navbar-form pull-right">
							<input type="text" name="searchUsername" placeholder="Enter user here...">
							<input class="btn btn-success btn-sm" type="submit" name="btnSubmit" value="Search Username">
						</div>

						<div class="navbar-form pull-left">
							User: <b><i><?= $user['username'] ?></i></b> <br/>
							Current role:  <b><i><?= $roles['roleName']?></i></b> <br/>
							Permissions are as follows:
						</div>
						<div class="clearfix"></div>
					</div>

					<?php echo form_error('role_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					<hr/>

						<?php if($this->permissions->checkPermissions('role')) { ?>
							<br/>
							<label>(For Usage)</label>
							<br/><br/>
						<?php } ?>

						<?php
							$funcId = null;
							$cnt = 0;

							foreach ($functions as $value) {
								if ($value['parentId'] == 0) {
									$funcId = $value['funcId'];
						?>
									<label><?= $value['funcName'] ?></label>
									<hr/>
						<?php 
								} else if ($funcId == $value['parentId']) {
						?>
									<table class="table table-bordered">
				  						<tr>
				  							<td colspan='4'>
										      <input type="checkbox" onclick="checkAll(this.id)" id="<?= 'all_'. $value['funcId']?>" disabled> <b><?= $value['funcName']?></b>
											</td>
										</tr>
						<?php
								} else {
						?>

						<?php
									if ($cnt == 4) {
										$cnt= 0;
						?>
										</tr> 
						<?php
									} else if ($cnt == 0) {
						?> 
										<tr>
						<?php
									}
						?>
										<td>
									      <input type="checkbox" name="functions[]" id="<?= $value['funcId']?>" class="<?= 'all_'. $value['parentId']?>" value="<?= $value['funcId']?>" onclick="uncheckAll(this.id)" <?= ($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId'])) ? 'checked' : '' ?> disabled> <?= $value['funcName']?>
									  	</td>
						<?php
									$cnt++;
								}
							}
						?>

						</table>

						<?php if($this->permissions->checkPermissions('role')) { ?>
							<br/><br/><hr><br/><br/>

							<label>(For Giving)</label>
							<br/><br/>

						<?php
								$funcId = null;
								$cnt = 0;

							 	foreach ($functions as $value) {
									if ($value['parentId'] == 0) {
										$funcId = $value['funcId'];
						?>
										<label><?= $value['funcName'] ?></label>
										<hr/>
						<?php 
									} else if ($funcId == $value['parentId']) {
						?>
										<table class="table table-bordered">
					  						<tr>
					  							<td colspan='4'>
											      <input type="checkbox" onclick="checkAll(this.id)" id="<?= 'all_giving_'. $value['funcId']?>" disabled> <b><?= $value['funcName']?></b>
												</td>
											</tr>
						<?php
									} else {
						?>

						<?php
										if ($cnt == 4) {
											$cnt= 0;
						?>
											</tr> 
						<?php
										} else if ($cnt == 0) {
						?> 
											<tr>
						<?php
										}
						?>
											<td>
										      <input type="checkbox" name="functions_giving[]" id="<?= $value['funcId']?>" class="<?= 'all_giving_'. $value['parentId']?>" value="<?= $value['funcId']?>" onclick="uncheckAll(this.id)" <?= ($this->rolesfunctions->findIfFunctionExists($rolesfunctions_giving, $value['funcId'])) ? 'checked' : '' ?> disabled> <?= $value['funcName']?>
										  	</td>
						<?php
										$cnt++;
									}
								}
							?>

							</table>
						<?php } ?>

					<div class="col-md-4 col-md-offset-4">
						<center>
							<input class="btn btn-success" type="submit" value="Update">
							<input type="button" value="Cancel" class="btn btn-default btn-sm" onclick="history.back();" />
						</center>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>

		</div>
	</div>
</div>