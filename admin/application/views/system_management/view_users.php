<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseViewUsers" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseViewUsers" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form class="form-horizontal" method="post" action="<?=BASEURL . 'user_management/postFilter'?>" id="my_form" autocomplete="off" role="form">
				<div class="form-group" style="margin-bottom:0;">
					<div class="col-md-3">
						<label class="control-label"><?=lang('sys.vu02');?></label>
						<input type="text" name="username" value="<?=$this->session->userdata('um_username')?>" id="username" class="form-control input-sm">
						<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu03');?></label>
						<input type="text" name="realname" value="<?=$this->session->userdata('um_realname')?>" id="realname" class="form-control input-sm">
						<?php echo form_error('realname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu04');?></label>
						<select name="role" id="role" class="form-control input-sm">
							<option value=""><?=lang('sys.vu05');?></option>
							<?php if (!empty($roles)) { ?>
								<?php foreach ($roles as $role) {?>
									<option value="<?=$role['roleId']?>" <?=$this->session->userdata('um_role') == $role['roleId'] ? 'selected' : ''?>><?=$role['roleName']?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<?php echo form_error('role', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu06');?></label>
						<select name="create_by" id="create_by" class="form-control input-sm">
							<option value=""><?=lang('sys.vu05');?></option>
							<?php if (!empty($user_group)) { ?>
								<?php foreach ($user_group as $user) {?>
									<option value="<?=$user['userId']?>" <?=$this->session->userdata('um_create_by') == $user['userId'] ? 'selected' : ''?>><?=$user['username']?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu07');?></label>
						<input type="text" name="department" value="<?=$this->session->userdata('um_department')?>" id="department" class="form-control input-sm">
						<?php echo form_error('department', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu08');?></label>
						<input type="text" name="position" value="<?=$this->session->userdata('um_position')?>" id="position" class="form-control input-sm">
						<?php echo form_error('position', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu09');?></label>
						<?php $selected = 'selected'; ?>
						<select name="status" id="status" class="form-control input-sm">
							<option value="4" <?=$this->session->userdata('um_status') == 4 ? $selected : ''?>><?=lang('sys.vu10');?></option>
							<option value="0" <?=$this->session->userdata('um_status') == 0 ? $selected : ''?>><?=lang('sys.vu11');?></option>
							<option value="1" <?=$this->session->userdata('um_status') == 1 ? $selected : ''?>><?=lang('sys.vu12');?></option>
							<option value="2" <?=$this->session->userdata('um_status') == 2 ? $selected : ''?>><?=lang('sys.vu13');?></option>
						</select>
						<?php echo form_error('status', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('sys.vu14');?></label>
						<input type="text" name="login_ip" value="<?=$this->session->userdata('um_login_ip')?>" id="login_ip" class="form-control input-sm">
						<?php echo form_error('login_ip', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
				</div>
			</form>
		</div>

		<div class="panel-footer">
			<div class="text-center">
				<input class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" type="reset" value="<?=lang('sys.vu16');?>">
				<button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" type="submit" form="my_form"><i class="fa fa-search"></i> <?=lang('sys.vu15');?></button>
			</div>
		</div>
	</div>
</div>

<div class="row" id="user-container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt pull-left">
					<i class="icon-users"></i> <?=lang('sys.vu35');?>
				</h3>
				<a href="#modal_column" role="button" data-toggle="modal" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary btn-xs' : 'panel-button btn-info btn-sm'?>">
					<span class="glyphicon glyphicon-list"></span> <?=lang('sys.vu57');?>
				</a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="list_panel_body">
				<form method="post" action="<?php echo site_url('user_management/typeOfAction'); ?>" id="my_form">
                    <input type="hidden" name="submit_type" id="submit_type">
					<div class="table-responsive">
						<table class="table table-striped table-hover table-condensed table-bordered" style="width:100%;" id="my_table">
							<div class="btn-action">
								<a class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" type='button' href="<?php echo site_url('user_management/viewAddUser');?>" >
								<i class="fa fa-plus"></i> <?php echo lang('system.word37');?>
								</a>
								<input type="submit" value="" name="btnSubmit" class="btn btn-success btn-sm" style="display: none;">
								<?php if ($this->permissions->checkPermissions('delete_user')) {?>
									<button type="submit" value="<?=lang('sys.vu38');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" onclick="$('#submit_type').val('Delete');return confirm('<?=lang('sys.sure')?>');">
				                        <i class="glyphicon glyphicon-trash" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="color:white;"' : ''?>></i> <?=lang('Delete');?>
				                    </button>&nbsp;
								<?php } ?>
								<?php if ($this->permissions->checkPermissions('lock_user')) {?>
                                    <button type="submit" value="<?=lang('sys.vu36');?>" name="btnSubmit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>" onclick="$('#submit_type').val('Lock');return confirm('<?=lang('sys.sure')?>');">
				                        <i class="glyphicon glyphicon-lock"></i> <?=lang('system.word55');?>
				                    </button>&nbsp;
                                    <button type="submit" value="<?=lang('sys.vu37');?>" name="btnSubmit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>" onclick="$('#submit_type').val('Unlock');return confirm('<?=lang('sys.sure')?>');">
				                        <i class="icon-unlocked"></i> <?=lang('system.word56');?>
				                    </button>&nbsp;
								<?php } ?>
							</div>
							<div class="clearfix"></div>
							<thead>
								<tr>

									<th style="padding:8px;">
										<input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/>
									</th>
									<th><?=lang('sys.vu39');?></th>
									<?=$this->session->userdata('u_realname') == "checked" || !$this->session->userdata('u_realname') ? '<th id="visible">' . lang('sys.vu40') . '</th>' : ''?>
									<?=$this->session->userdata('u_department') == "checked" || !$this->session->userdata('u_department') ? '<th id="visible">' . lang('sys.vu41') . '</th>' : ''?>
									<?=$this->session->userdata('u_position') == "checked" || !$this->session->userdata('u_position') ? '<th id="visible">' . lang('sys.vu42') . '</th>' : ''?>
									<?=$this->session->userdata('u_role') == "checked" || !$this->session->userdata('u_role') ? '<th id="visible">' . lang('sys.vu43') . '</th>' : ''?>
									<?=$this->session->userdata('u_login_ip') == "checked" || !$this->session->userdata('u_login_ip') ? '<th id="visible">' . lang('sys.vu44') . '</th>' : ''?>
									<?=$this->session->userdata('u_last_login_time') == "checked" || !$this->session->userdata('u_last_login_time') ? '<th id="visible">' . lang('sys.vu45') . '</th>' : ''?>
									<?=$this->session->userdata('u_last_logout_time') == "checked" || !$this->session->userdata('u_last_logout_time') ? '<th id="visible">' . lang('sys.vu46') . '</th>' : ''?>
									<?=$this->session->userdata('u_created_by') == "checked" || !$this->session->userdata('u_created_by') ? '<th id="visible">' . lang('sys.vu48') . '</th>' : ''?>
									<?=$this->session->userdata('u_create_time') == "checked" || !$this->session->userdata('u_create_time') ? '<th id="visible">' . lang('sys.vu47') . '</th>' : ''?>
									<?php //$this->session->userdata('u_status') == "checked" || !$this->session->userdata('u_status') ? '<th id="visible">' . lang('sys.vu49') . '</th>' : ''?>
									<?php if ($this->permissions->checkPermissions('reset_password')) {?>
										<th><?=lang('sys.dm5');?></th>
									<?php } ?>
                                    <th><?=lang('sys.dm4');?></th>

								</tr>
							</thead>

							<tbody>
								<?php if (!empty($users)) { ?>

						<?php
                       //export--------------------------------------------------
						$export_users = [
							'header_data' => [lang('sys.vu39'),lang('sys.vu40'),lang('sys.vu41'),lang('sys.vu42'),lang('sys.vu43'),lang('sys.vu44'),lang('sys.vu45'),lang('sys.dm4')],
							'data' =>[]
							];
						$chosenExportFields = ['username', 'realname', 'department','position', 'roleName', 'lastLoginIp','lastLoginTime','note'];
						//export--------------------------------------------------
						?>
									<?php foreach ($users as $row) { ?>
 										 <?php
 										 //export--------------------------------------------------
 										 $arr = [];
 										 foreach ($row as $key => $value) {
 										 	if(in_array($key, $chosenExportFields)){

 										 		$arr = array(
 										 			'username' => $row['username'],
 										 			'realname' => $row['realname'],
 										 			'department' => $row['department'],
 										 			'position' => $row['position'],
 										 			'roleName' => $row['roleName'],
 										 			'lastLoginIp' => $row['lastLoginIp'],
 										 			'lastLoginTime' => $row['lastLoginTime'],
 										 			'note' => $row['note'],
 										 		);
 										 	}
 										 }
 										 array_push($export_users['data'], $arr);
 										 //export--------------------------------------------------
 										 ?>
										<?php
											$isAdmin = $admin_user_id==Users::SUPER_ADMIN_ID || $admin_user_id==Users::ADMIN ? true : false;
											$isDisplayUserAdmin = $row['userId'] ==Users::SUPER_ADMIN_ID || $row['userId']==Users::ADMIN ? true : false;
										?>
                                        <tr class="<?php echo $row['status'] == $const_unlocked ? '' : 'danger' ;?>">
											<td style="padding:8px;">
												<?php if ($currentUser != $row['userId']) {?>
													<?php if($isAdmin || !$isDisplayUserAdmin) { ?>
														<input type="checkbox" class="checkWhite" id="<?=$row['userId']?>" name="check[]" value="<?=$row['userId']?>" onclick="uncheckAll(this.id)">
													<?php }  ?>
												<?php } ?>
											</td>
											<td title="Click me to view/edit">
												<?php if ($currentUser != $row['userId'] ) {?>
													<?php if($isAdmin || !$isDisplayUserAdmin):?>
														<span class="userName"><a href="<?=BASEURL . 'user_management/viewUser/' . $row['userId']?>"><?=$row['username'] . ' ' . lang($row['status'] == $const_unlocked ? 'icon.unlocked' : 'icon.locked')?></a></span>
													<?php else:?>
														<span class="userName"><?=$row['username'] . ' ' . lang($row['status'] == $const_unlocked ? 'icon.unlocked' : 'icon.locked')?></span>
													<?php endif; ?>
												<?php } else {?>
													<span class="userName"><?=$row['username'] . ' ' . lang($row['status'] == $const_unlocked ? 'icon.unlocked' : 'icon.locked')?></span>
												<?php } ?>
											</td>

											<?php if ($this->session->userdata('u_realname') == "checked" || !$this->session->userdata('u_realname')) {?>
												<td id="visible"><?=$row['realname'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['realname']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_department') == "checked" || !$this->session->userdata('u_department')) {?>
												<td id="visible"><?=$row['department'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['department']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_position') == "checked" || !$this->session->userdata('u_position')) {?>
												<td id="visible"><?=$row['position'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['position']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_role') == "checked" || !$this->session->userdata('u_role')) { ?>
												<td title="Click me to change role" id="visible">
													<?php if ($currentUser != $row['userId']) {?>
														<a href="<?=BASEURL . 'role_management/checkRole/' . $row['roleId']?>"><?=$row['roleName']?></a>
													<?php } else {?>
														<?=$row['roleName']?>
													<?php } ?>
												</td>
											<?php } ?>

											<?php if ($this->session->userdata('u_login_ip') == "checked" || !$this->session->userdata('u_login_ip')) {?>
												<td><?=$row['lastLoginIp'] == '' ? '<i class="help-block"><?php echo lang("N/A");?><i/>' : $row['lastLoginIp']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_last_login_time') == "checked" || !$this->session->userdata('u_last_login_time')) {?>
												<td><?=$row['lastLoginTime'] == '0000-00-00 00:00:00' ? '<i class="help-block"><?php echo lang("N/A");?><i/>' : $row['lastLoginTime']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_last_logout_time') == "checked" || !$this->session->userdata('u_last_logout_time')) {?>
												<td><?=$row['lastLogoutTime'] == '0000-00-00 00:00:00' ? '<i class="help-block"><?php echo lang("N/A");?><i/>' : $row['lastLogoutTime']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_created_by') == "checked" || !$this->session->userdata('u_created_by')) {?>
												<td id="visible"><?=$row['created_by'] == '' ? '<i class="help-block">No Record Yet<i/>' : $row['created_by']?></td>
											<?php } ?>

											<?php if ($this->session->userdata('u_create_time') == "checked" || !$this->session->userdata('u_create_time')) {?>
												<td><?=$row['createTime'] == '0000-00-00 00:00:00' ? '<i class="help-block"><?php echo lang("N/A");?><i/>' : $row['createTime']?></td>
											<?php } ?>

											<td>
											<?php if ($currentUser != $row['userId'] && $this->permissions->checkPermissions('reset_password')) {?>
												<?php if($isAdmin || !$isDisplayUserAdmin):?>
													<a href="<?=BASEURL . 'user_management/viewResetPassword/' . $row['userId']?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?> btn-xs" data-id="" data-toggle="modal"><?=lang('sys.vu50');?></a>
												<?php endif; ?>
											<?php } ?>
											<?php if ($this->utils->isEnabledFeature('enable_otp_on_adminusers') && $currentUser != $row['userId'] && $this->permissions->checkPermissions('reset_otp_secret_for_adminusers')) {?>
												<a href="javascript:void(0)" data-userid="<?=$row['userId']?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?> btn-xs btn_reset_2fa" ><?=lang('Reset 2FA');?></a>
											<?php } ?>
												<a href="<?=BASEURL . 'user_management/viewLogs/?username=' . $row['username']?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?> btn-xs" data-id="" data-toggle="modal"><?php echo lang('Logs'); ?></a>
											<?php if ($this->utils->isEnabledFeature('enabled_backendapi') && $this->permissions->checkPermissions('view_backendapi_keys')
													&& (!$this->users->isT1Admin($row['username']) || $currentUsername=='superadmin' ) ) {?>
												<a href="<?=site_url('/system_management/view_user_backendapi/'.$row['userId'])?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?> btn-xs"><?=lang('Backend API Keys');?></a>
											<?php }?>
											</td>
                                            <td>
                                                <?=$row['note']?>
                                            </td>
										</tr>
									<?php } ?>
								<?php } else {?>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!--MODAL for edit column-->
<div id="modal_column" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_column" aria-hidden="true">
<div class="modal-dialog modal-sm">
<div class="modal-content panel-primary">
	<div class="modal-header panel-heading">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?=lang('sys.vu57');?></h3>
	</div>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12">
				<div class="help-block">
					<?=lang('sys.vu58');?>
				</div>
			</div>
		</div>

		<div class="row">
		<form action="<?=BASEURL . 'user_management/postChangeColumns'?>" method="post" role="form" id="modal_column_form">
			<div class="col-md-7 col-md-offset-1">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="realname" value="realname" <?=$this->session->userdata('u_realname') == "checked" || !$this->session->userdata('u_realname') ? 'checked' : ''?>> <?=lang('sys.vu59');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="department" value="department" <?=$this->session->userdata('u_department') == "checked" || !$this->session->userdata('u_department') ? 'checked' : ''?>> <?=lang('sys.vu60');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="position" value="position" <?=$this->session->userdata('u_position') == "checked" || !$this->session->userdata('u_position') ? 'checked' : ''?>> <?=lang('sys.vu61');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="role" value="role" <?=$this->session->userdata('u_role') == "checked" || !$this->session->userdata('u_role') ? 'checked' : ''?>> <?=lang('sys.vu62');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="login_ip" value="login_ip" <?=$this->session->userdata('u_login_ip') == "checked" || !$this->session->userdata('u_login_ip') ? 'checked' : ''?>> <?=lang('sys.vu63');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="last_login_time" value="last_login_time" <?=$this->session->userdata('u_last_login_time') == "checked" || !$this->session->userdata('u_last_login_time') ? 'checked' : ''?>> <?=lang('sys.vu64');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="last_logout_time" value="last_logout_time" <?=$this->session->userdata('u_last_logout_time') == "checked" || !$this->session->userdata('u_last_logout_time') ? 'checked' : ''?>> <?=lang('sys.vu65');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="create_time" value="create_time" <?=$this->session->userdata('u_create_time') == "checked" || !$this->session->userdata('u_create_time') ? 'checked' : ''?>> <?=lang('sys.vu66');?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="create_by" value="create_by" <?=$this->session->userdata('u_create_by') == "checked" || !$this->session->userdata('u_create_by') ? 'checked' : ''?>> <?=lang('sys.vu67');?>
					</label>
				</div>

				<!-- <div class="checkbox">
					<label>
						<input type="checkbox" name="status" value="status" <?=$this->session->userdata('u_status') == "checked" || !$this->session->userdata('u_status') ? 'checked' : ''?>> <?=lang('sys.vu68');?>
					</label>
				</div> -->
			</div>
		</form>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal" aria-hidden="true"><?=lang('sys.vu69');?></button>
		<button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" id="save_changes" name="save_changes"><?=lang('sys.vu70');?></button>
	</div>
</div>
</div>
<?php $module = $this->router->fetch_method();?>
<script type="text/javascript">

	var module = '<?php echo $module; ?>';

    $(document).ready(function(){
     //hide json content in variable

	var jsonObjForExportToCSV = {};
	<?php if(!empty($users)): ?>
		jsonObjForExportToCSV = JSON.parse('<?php echo json_encode($export_users);?>');
	<?php endif; ?>

       $('body').tooltip({
	        selector: '[data-toggle="tooltip"]',
	        placement: "bottom"
	    });

		if(module == 'viewUsers') {
			$('#status option[value="4"]').attr('selected','selected');
		}

		var sortColUsername=1;
        $('#my_table').DataTable({
        	<?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
        	dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "buttons": [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
            		className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },
                 <?php if (isset($export_report_permission) && $export_report_permission):?>
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> _export_csv_btn',
                    action: function ( e, dt, node, config ) {
                        $(this).attr('disabled', 'disabled');
                        $.ajax({
                            url:  site_url('/export_data/adminusers_results'),
                            type: 'POST',
                            data: {json_search: JSON.stringify(jsonObjForExportToCSV)}
                        }).done(function(data) {
                            if(data && data.success){
                            $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            $('._export_csv_btn').removeAttr("disabled");
                            }else{
                            $('._export_csv_btn').removeAttr("disabled");
                            alert('export failed');
                            }
                        }).fail(function(){
                            $('._export_csv_btn').removeAttr("disabled");
                            alert('export failed');
                        });
                    }
                }
                <?php endif; ?>
            ],
            "columnDefs": [ {

                orderable: false,
                targets:   0
            },{
                orderable: false,
                targets:   1
            } ],
            "order": [ sortColUsername, 'asc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });
    });

    $(document).on("click","input:reset",function(){
		$("#my_form").find('input').val('');
		$("#role option:nth(0)").prop("selected","selected");
		$("#status option:nth(0)").prop("selected","selected");
		$("#create_by option:nth(0)").prop("selected","selected");
	});

    $(".btn_reset_2fa").click(function(){
        if(confirm("<?=lang('Do you want to reset 2FA')?>?")){
        	var userId=$(this).data('userid');
            var url="<?=site_url('/user_management/reset_2fa')?>/"+userId;
            $.ajax(url,{
                cache: false,
                dataType: 'json',
                method: 'POST',
                success: function(data){
                    alert(data['message']);
                    if(data['success']){
                        window.location.reload();
                    }
                },
                error: function(){
                    alert("<?=lang('Reset 2FA failed')?>");
                }
            });
        }
        return false;
    });

</script>