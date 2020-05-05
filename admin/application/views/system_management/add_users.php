<div id="container">
	<ol class="breadcrumb">
		<li class="text-primary"><b><?=lang('system.word27');?></b></li>
		<li class="text-muted"><?=lang('system.word28');?></li>
		<li class="text-muted"><?=lang('system.word29');?></li>
	</ol>

	<form method="post" action="<?=site_url('user_management/nextAddUser')?>" id="my_form" autocomplete="off" class="form-inline">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading custom-ph">
						<h4 class="panel-title custom-pt pull-left">
							<i class="glyphicon glyphicon-plus-sign"></i> <?=lang('system.word30');?>
						</h4>
<!-- 						<a href="<?=site_url('role_management/addRole')?>" class="btn btn-default btn-sm pull-right">
							<span class="glyphicon glyphicon-plus-sign"></span> <?=lang('system.word35');?>
						</a>
 -->						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<td></td>
										<td><?=lang('system.word31');?></td>
										<td><?=lang('system.word32');?></td>
										<td><?=lang('system.word33');?></td>
										<td><?=lang('system.word34');?></td>
									</tr>
								</thead>
								<tbody>
									<?php
foreach ($roles as $row) {
	?>
											<?php if ($row['roleId'] != 1 && $row['status'] != 1) {?>
												<tr>
													<td><input type="radio" name="roleId" value="<?=$row['roleId']?>"></td>
													<td><a href="<?=site_url('role_management/checkRole/' . $row['roleId'])?>"><?=$row['roleName']?></a></td>
											<?php } else {?>
												<tr class="active">
													<td><input type="radio" name="roleId" value="<?=$row['roleId']?>" disabled="disabled"></td>
													<td><?=$row['roleName']?></td>
											<?php }
	?>
													<td><?=$this->rolesfunctions->countUsersUsingRoles($row['roleId']) == 0 ? 'Empty' : $this->rolesfunctions->countUsersUsingRoles($row['roleId'])?></td>
													<td><?=$row['createTime']?></td>
													<td><?=$row['createPerson']?></td>
											</tr>
									<?php }
?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<div class="nav pull-right">
							<button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>"><?=lang('system.word36');?> <i class="fa fa-arrow-right"></i></button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>