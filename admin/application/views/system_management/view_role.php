<div class="row" id="roles-container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt pull-left"><i class="icon-key"></i> <?=lang('system.word52');?></h3>
				<!-- <a href="<?=BASEURL . 'role_management/addRole'?>" class="btn btn-sm btn-default pull-right panel-button"><i class="glyphicon glyphicon-plus-sign"></i> <?=lang('system.word35');?> </a> -->
				<a href="<?=BASEURL . 'role_management/addRole'?>" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-primary' : 'btn-sm btn-default panel-button'?>"><i class="glyphicon glyphicon-plus-sign"></i> <?=lang('system.word35');?> </a>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="list_panel_body">

				<form method="post" action="<?=BASEURL . 'role_management/checkAction'?>">
					<div class="table-responsive">
						<table class="table table-bordered table-hover dataTable" id="myTable" style="width:100%;">
							<div class="">
		                    	<button type="submit" value="Delete" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" id="btn-delete" data-toggle="tooltip" data-placement="top" title="<?=lang('sys.vu38');?>" onclick="return validateDelete()">
			                        <i class="glyphicon glyphicon-trash" style="color:white;"></i> <?=lang('system.word54');?>
			                    </button>&nbsp;
			                    <button type="submit" value="Lock" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>" data-toggle="tooltip" data-placement="top" title="<?=lang('sys.vu36');?>" onclick="return confirm('<?=lang('sys.sure')?>')">
			                        <i class="glyphicon glyphicon-lock" style="color:white;"></i> <?=lang('system.word55');?>
			                    </button>&nbsp;
			                    <button type="submit" value="Unlock" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>" data-toggle="tooltip" data-placement="top" title="<?=lang('sys.vu37');?>" onclick="return confirm('<?=lang('sys.sure')?>')">
			                        <i class="icon-unlocked" style="color:white;"></i> <?=lang('system.word56');?>
			                    </button>
		                    </div>
		                    <hr class="hr_between_table">
							<thead>
								<tr>
									<!-- <th></th> -->
									<th style="padding:8px;"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
									<th><?=lang('system.word5f');?></th>
									<th><?=lang('system.word58');?></th>
									<th><?=lang('system.word.active_roles');?></th>
									<th><?=lang('system.word59');?></th>
									<th><?=lang('system.word60');?></th>
									<th><?=lang('system.word61');?></th>
									<th><?=lang('isAdmin.short');?></th>
								</tr>
							</thead>
							<tbody>
        						<?php if (!empty($roles)) {
	?>
									<?php foreach ($roles as $row) {
		?>
										<tr id="role-<?=$row['roleId']?>" data-user-count="<?=$row['user_count']?>" data-role-count="<?=$row['role_count']?>">
											<!-- <td></td> -->
									<?php
switch ($row['roleId']) {
		case 1:
			?>			<td></td>
												<td><?=$row['roleName']?></td>
												<td><?=$this->rolesfunctions->countUsersUsingRoles($row['roleId'])?></td>
												<td><?=$this->rolesfunctions->countActiveRolesByThisRoleId($row['roleId'])?></td>
												<td><?=$row['createTime']?></td>
												<td>system</td>
									<?php
break;
		default:
			?>
											<td style="padding:8px;"><input type="checkbox" class="checkWhite roles" id="<?=$row['roleId']?>" name="role[]" value="<?=$row['roleId']?>" onclick="uncheckAll(this.id)"/></td>
									<?php
if ($this->permissions->checkPermissions('role')) {
				?>
												<td><a href="<?=BASEURL . 'role_management/editRole/' . $row['roleId']?>"><?=$row['roleName']?></a></td>
									<?php
} else {
				?>
												<td><?=$row['roleName']?></td>
									<?php
}
			?>
											<td><?=$row['user_count'] == 0 ? 'Empty' : $row['user_count']?></td>
											<td><?=$row['role_count'] == 0 ? 'Empty' : $row['role_count']?></td>
											<td><?=$row['createTime']?></td>
											<td><?=$row['createPerson']?></td>
									<?php
break;
		}
		?>

									<?php
switch ($row['status']) {
		case 0:
			?>
												<td><?=lang('system.word62');?></td>
									<?php
break;

		case 1:
			?>
												<td><?=lang('system.word63');?></td>
									<?php
break;

		default:
			?>
												<td><?=lang('system.word64');?></td>
									<?php
break;
		}
		?>
											<td><?=$row['isAdmin'] ? lang('lang.yes') : lang('lang.no')?></td>
										</tr>
									<?php }
	?>
						        <?php } else {?>
						        <?php }
?>
							</tbody>
						</table>
					</div>
				</form>
			</div>

		</div>
	</div>
</div>
<?php if ($this->utils->isEnabledFeature('export_excel_on_queue')) {?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
<script type="text/javascript">
        var dataTable = $('#myTable').DataTable({
            autoWidth: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
            		className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },
                <?php if ($this->permissions->checkPermissions('export_view_roles')) { ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                                var d = {};

                                $.post(site_url('/export_data/viewRoles'), d, function(data){
                                    if(data && data.success){
                                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                    }else{
                                        alert('export failed');
                                    }
                                });
                            }
                        }
                <?php } ?>
            ],
            "order": [[ 1, 'asc' ]],
            drawCallback: function () {
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                    dataTable.buttons().disable();
                }
                else {
                    dataTable.buttons().enable();
                }
            }
        });

	function validateDelete() {
		var result = true;
		var user_and_role_message = [];
		var user_message = [];
		var role_message = [];
		$('.roles:checked').each(function(i,v) {
			var id = $(this).val();
			var role = $('#role-'+id);
			var role_name = role.find('td:nth-child(2)').text(); // Get the role Name
			var user_count = role.data('user-count');
			var role_count = role.data('role-count');
			if (user_count > 0 && role_count > 0) {
				user_and_role_message.push(role_name);
				result = false;
			}else if(user_count > 0){
				user_message.push(role_name);
				result = false;
			}else if(role_count > 0){
				role_message.push(role_name);
				result = false;
			}

		});
		if (result) {
			return confirm('<?=lang('sys.sure')?>');
		} else{
			var message = "";
			if(user_and_role_message.length) message +=  '<?=sprintf(lang("role.err_del_fail"), "'+user_and_role_message.join(', ')+'")?>\n';
			if(user_message.length) message +=  '<?=sprintf(lang("role.err_del_fail_users"), "'+user_message.join(', ')+'")?>\n';
			if(role_message.length) message +=  '<?=sprintf(lang("role.err_del_fail_roles"), "'+role_message.join(', ')+'")?>\n';
			alert(message);
			return result;
		};
	}
</script>