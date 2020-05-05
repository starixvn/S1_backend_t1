<div id="roles-container">
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="icon-key"></i> <?= lang('viewuser.08'); ?>  </h3>
				<button class="btn btn-default btn-sm pull-right" id="button_list_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_list_up"></span></button>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="list_panel_body" style="padding-bottom:0;">
				<form method="post" action="<?= BASEURL . 'user_management/postSetRole/' . $user['userId']?>" autocomplete="off">
				<div class="table-responsive">
					<table class="table table-hover" id="myTable">
						<thead>
							<tr>
								<td></td>
								<td><?= lang('system.word31'); ?></td>
								<td><?= lang('system.word58'); ?></td>
								<td><?= lang('system.word33'); ?></td>
								<td><?= lang('system.word34'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($roles as $row) { 
									if ($row['roleId'] != 1) {
							?>
										<tr>
											<?php if($userroles['roleId'] == $row['roleId']) { ?>
												<td>
													<input type="radio" name="roleId" value="<?= $row['roleId']?>" checked>
														<?php echo form_error('roleId', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
												</td>
											<?php } else { ?>
												<td>
													<input type="radio" name="roleId" value="<?= $row['roleId']?>">
														<?php echo form_error('roleId', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
												</td>
											<?php } ?>
											<td><a href="<?= BASEURL . 'role_management/checkRole/' . $row['roleId'] ?>"><?= $row['roleName'] ?></a></td>
											<td><?= $this->rolesfunctions->countUsersUsingRoles($row['roleId']) ?></td>
											<td><?= $row['createTime'] ?></td>
											<td><?= $row['createPerson'] ?></td>
										</tr>
							<?php 
									} 
								} 
							?>
						</tbody>
					</table>
				</div>
				<hr style="margin-bottom:0;">
				<div style="text-align:center;">
					<input type="submit" value="<?= lang('lang.submit'); ?>" class="btn btn-info btn-sm">
					<input type="button" value="<?= lang('lang.cancel'); ?>" class="btn btn-default btn-sm" onclick="history.back();" />
				</div>
				</form>
			</div>
			
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').DataTable();
	});
</script>