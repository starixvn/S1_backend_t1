<form method="post" action="<?= BASEURL . 'user_management/typeOfAction'?>" autocomplete="on" class="" id="my_form">
<div class="table-responsive" style="overflow: auto;">
	<div class="navbar-form pull-left">
		<input type="submit" value="" name="btnSubmit" class="btn btn-success btn-sm" style="display: none;">
	<?php
		if($this->permissions->checkPermissions('lock_user')) {
	?>
			<input type="submit" value="Lock" name="btnSubmit" class="btn btn-success btn-sm">
			<input type="submit" value="Unlock" name="btnSubmit" class="btn btn-success btn-sm">
	<?php 
		}
	?>

	<?php
		if($this->permissions->checkPermissions('delete_user')) {
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
					<input type="checkbox" name="checkAll" id="checkAll">
				</th>
				<th><span id="sort_username" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('username')">Username</a></th>
				<th><span id="sort_realname" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('realname')">Realname</a></th>
				<th><span id="sort_department" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('department')">Department</a></th>
				<th><span id="sort_position" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('position')">Position</a></th>
				<th><span id="sort_roleName" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('roleName')">Role</a></th>
				<th><span id="sort_online_status" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('realname')">Online Status</a></th>
				<th><span id="sort_lastLoginIp" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('lastLoginIp')">Login IP</a></th>
				<th><span id="sort_lastLoginTime" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('lastLoginTime')">Last Login Time</a></th>
				<th><span id="sort_lastLogoutTime" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('lastLogoutTime')">Last Logout Time</a></th>
				<th><span id="sort_createTime" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('createTime')">Create Time</a></th>
				<th><span id="sort_createPerson" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('createPerson')">Create by</a></th>
				<th><span id="sort_status" style="display:none;"><?= $filter ?></span><a href="#" onclick="sortBy('status')">Status</a></th>
				<th>Reset Password</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach($users as $row) { ?>
				<tr>
					<td>
						<?php if($currentUser != $row['userId']) { ?>
							<input type="checkbox" name="check[]" value="<?= $row['userId']?>" class="check" onclick="resetCheckAll(this.id);"></td>
						<?php } ?>
					<td title="Click me to view/edit">
						<span class="userName">
							<?php if($currentUser != $row['userId']) { ?>
								<a href="<?= BASEURL . 'user_management/viewUser/' . $row['userId'] ?>">
									<?php
										BASEURL . 'user_management/viewUser/' . $row['userId'];
									?>
									<?= $row['username'] ?>

								</a>
							<?php } else { ?>
									<?= $row['username'] ?>
							<?php } ?>
						</span>
					</td>
					<td><?= $row['realname'] ?></td>
					<td><?= $row['department'] ?></td>
					<td><?= $row['position'] ?></td>
					<td title="Click me to set a role">
						<a href="<?= BASEURL . 'user_management/viewSetRole/' . $row['userId'] ?>"><?= $row['roleName']?></a>	
					</td>
					<td></td>
					<td><?= $row['lastLoginIp'] ?></td>
					<td><?= $row['lastLoginTime'] ?></td>
					<td><?= $row['lastLogoutTime'] ?></td>
					<td><?= $row['createTime'] ?></td>
					<td><?= $row['creator'] ?></td>
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
					<td>
						<?php if($currentUser != $row['userId']) { ?>	
							<a href="<?= BASEURL . 'user_management/viewResetPassword/' . $row['userId'] . '/' . $row['username']?>"  data-id="" data-toggle="modal">Reset</a></td>
						<?php } ?>
				</tr>
			<?php }  ?>
		</tbody>
	</table>
</div>
	<div class="col-md-4 col-offset-0">
	    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul> 
    </div>

    <div class="col-md-6 col-offset-0">
		<span>Total <?= $total_pages?> pages</span>

	<?php if ($total_pages != 1) { ?>
		<input type="text" name="page" style="width: 35px;" id="page_num">
		<input type="button" value="Skip" name="user_skip" class="btn btn-success btn-sm" onclick="get_skip_pages()"> 
	<?php } ?>  
	</div>
</form>
