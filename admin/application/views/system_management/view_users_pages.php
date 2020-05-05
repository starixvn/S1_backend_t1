<div class="col-md-12">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> View Users</h3>
			<a href="#modal_column" role="button" data-toggle="modal" class="btn btn-primary pull-right" id="edit_column"><span class="glyphicon glyphicon-list"></span></a>
			<div class="clearfix"></div>
		</div>

		<div class="panel-body" id="list_panel_body">
			<form method="post" action="<?=BASEURL . 'user_management/typeOfAction'?>" autocomplete="on" class="" id="my_form">
			<div class="table-responsive" style="overflow: auto;">
				<div class="navbar-form pull-left">
					<input type="submit" value="" name="btnSubmit" class="btn btn-success btn-sm" style="display: none;">
				<?php
if ($this->permissions->checkPermissions('lock_user')) {
	?>
						<input type="submit" value="Lock" name="btnSubmit" class="btn btn-success btn-sm">
						<input type="submit" value="Unlock" name="btnSubmit" class="btn btn-success btn-sm">
				<?php
}
?>

				<?php
if ($this->permissions->checkPermissions('delete_user')) {
	?>
					<input type="submit" value="Delete" name="btnSubmit" class="btn btn-success btn-sm">
				<?php
}
?>
				</div>

				<table class="table table-striped table-hover table-condensed" id="my_table">
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/>
							</th>
							<th>Username</th>
							<?=$this->session->userdata('u_realname') == "checked" || !$this->session->userdata('u_realname') ? '<th id="visible">RealName</th>' : ''?>
							<?=$this->session->userdata('u_department') == "checked" || !$this->session->userdata('u_department') ? '<th id="visible">Department</th>' : ''?>
							<?=$this->session->userdata('u_position') == "checked" || !$this->session->userdata('u_position') ? '<th id="visible">Position</th>' : ''?>
							<?=$this->session->userdata('u_role') == "checked" || !$this->session->userdata('u_role') ? '<th id="visible">Role</th>' : ''?>
							<?=$this->session->userdata('u_login_ip') == "checked" || !$this->session->userdata('u_login_ip') ? '<th id="visible">Login IP</th>' : ''?>
							<!-- <th><span id="sort_online_status" style="display:none;"><?=$filter?></span><a href="#" onclick="sortBy('realname')">Online Status</a></th> -->
							<?=$this->session->userdata('u_last_login_time') == "checked" || !$this->session->userdata('u_last_login_time') ? '<th id="visible">Last Login Time</th>' : ''?>
							<?=$this->session->userdata('u_last_logout_time') == "checked" || !$this->session->userdata('u_last_logout_time') ? '<th id="visible">Last Logout Time</th>' : ''?>
							<?=$this->session->userdata('u_create_time') == "checked" || !$this->session->userdata('u_create_time') ? '<th id="visible">Create Time</th>' : ''?>
							<?=$this->session->userdata('u_create_by') == "checked" || !$this->session->userdata('u_create_by') ? '<th id="visible">Create by</th>' : ''?>
							<?=$this->session->userdata('u_status') == "checked" || !$this->session->userdata('u_status') ? '<th id="visible">Status</th>' : ''?>
							<?php if ($this->permissions->checkPermissions('reset_password')) {?>
								<th>Reset Password</th>
							<?php }
?>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($users as $row) {
	?>
							<tr>

								<td>
									<?php if ($currentUser != $row['userId']) {?>
										<input type="checkbox" class="checkWhite" id="<?=$row['userId']?>" name="check[]" value="<?=$row['userId']?>" onclick="uncheckAll(this.id)">
									<?php }
	?>
								</td>
								<td title="Click me to view/edit">
									<?php if ($currentUser != $row['userId']) {?>
										<span class="userName"><a href="<?=BASEURL . 'user_management/viewUser/' . $row['userId']?>"><?=$row['username']?></a></span>
									<?php } else {?>
										<span class="userName"><?=$row['username']?></span>
									<?php }
	?>
								</td>

								<?php if ($this->session->userdata('u_realname') == "checked" || !$this->session->userdata('u_realname')) {?>
									<td id="visible"><?=$row['realname'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['realname']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_department') == "checked" || !$this->session->userdata('u_department')) {?>
									<td id="visible"><?=$row['department'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['department']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_position') == "checked" || !$this->session->userdata('u_position')) {?>
									<td id="visible"><?=$row['position'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['position']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_role') == "checked" || !$this->session->userdata('u_role')) {?>
									<td title="Click me to change role" id="visible">
										<?php if ($currentUser != $row['userId']) {?>
											<a href="<?=BASEURL . 'user_management/viewSetRole/' . $row['userId']?>"><?=$row['roleName']?></a>
										<?php } else {?>
											<?=$row['roleName']?>
										<?php }
		?>
									</td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_login_ip') == "checked" || !$this->session->userdata('u_login_ip')) {?>
									<td><?=$row['lastLoginIp'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['lastLoginIp']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_last_login_time') == "checked" || !$this->session->userdata('u_last_login_time')) {?>
									<td><?=$row['lastLoginTime'] == '0000-00-00 00:00:00' ? '<i class="help-block">No Record Yet<i/>' : $row['lastLoginTime']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_last_logout_time') == "checked" || !$this->session->userdata('u_last_logout_time')) {?>
									<td><?=$row['lastLogoutTime'] == '0000-00-00 00:00:00' ? '<i class="help-block">No Record Yet<i/>' : $row['lastLogoutTime']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_create_time') == "checked" || !$this->session->userdata('u_create_time')) {?>
									<td><?=$row['createTime'] == '0000-00-00 00:00:00' ? '<i class="help-block">No Record Yet<i/>' : $row['createTime']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_create_by') == "checked" || !$this->session->userdata('u_create_by')) {?>
									<td><?=$row['creator'] == '0000-00-00 00:00:00' ? '<i class="help-block">No Record Yet<i/>' : $row['creator']?></td>
								<?php }
	?>

								<?php if ($this->session->userdata('u_status') == "checked" || !$this->session->userdata('u_status')) {?>
									<td>
										<?php
switch ($row['status']) {
	case 2:
		echo "Locked";
		break;

	case 1:
		echo "Normal";
		break;

	default:
		echo "Not approved yet";
		break;
	}
		?>
									</td>
								<?php }
	?>

								<td>
									<?php if ($currentUser != $row['userId'] && $this->permissions->checkPermissions('reset_password')) {?>
										<a href="<?=BASEURL . 'user_management/viewResetPassword/' . $row['userId']?>" class="btn btn-info btn-xs" data-id="" data-toggle="modal">Reset</a>
									<?php }
	?>
								</td>
							</tr>
						<?php }
?>
					</tbody>
				</table>
			</div>
			</form>
				<div class="col-md-4 col-offset-0">
				    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
			    </div>

			    <div class="col-md-6 col-offset-0">
					<span>Total <?=$total_pages?> pages</span>

		    	<?php if ($total_pages != 1) {?>
					<input type="text" name="page" style="width: 35px;" id="page_num">
					<input type="button" value="Skip" name="user_skip" class="btn btn-success btn-sm" onclick="get_skip_pages()">
				<?php }
?>
				</div>
		</div>

		<div class="panel-footer">
		</div>

	</div>
</div>