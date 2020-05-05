<style type="text/css">
	.grayout { color: #ccc; }
	.role .cell { vertical-align: middle }
	.role .cell label { margin: 0; vertical-align: middle; }
	.priv.mesg { height: 25px; line-height: 25px;  margin-left: 7px; font-weight: bold; }
	/*#panel_add_binding .priv-group { padding: 0 5px; border-bottom: 3px #ddd solid; }*/
	th.dt-check,td.dt-check { text-align: center; }
	.roles-sec pre { font-size: 9pt; }
</style>
<div id="panel_add_binding" class="panel panel-primary hidden" style="display: none;">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-user-plus"></i> <?= lang('Add Binding') ?>

            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseViewUsers" class="btn btn-info btn-xs collapsed" style="display: none;"></a>
            </span>

        </h4>
    </div>

    <div id="collapseViewUsers" class="panel-collapse collapse">
        <div class="panel-body">
			<form class="form-horizontal" method="post" action="<?=BASEURL . 'user_management/postFilter'?>" id="my_form" autocomplete="off" role="form">
				<div class="row">
					<div class="col-md-3">
						<label class="control-label"><?=lang('sys.vu02');?></label>
						<!-- <input type="text" name="username" value="<?=$this->session->userdata('um_username')?>" id="username" class="form-control input-sm"> -->
						<select name="username" id="username" class="form-control input-sm">
							<option value="">--  <?php echo lang('None'); ?> --</option>
							<?php foreach ($avail_users as $user) : ?>
								<option value="<?= $user['username'] ?>"><?= $user['username'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<!--
					<div class="col-md-3">
						<label  class="control-label"><?=lang('Password');?></label>
						<input type="password" name="passwd" id="passwd" class="form-control input-sm">
					</div>
					<div class="col-md-3">
						<label  class="control-label"><?=lang('Confirm Password');?></label>
						<input type="password" name="passwd_conf" id="passwd_conf" class="form-control input-sm">
					</div>
				-->
				</div>
				<div>
					<label class="control-label"><b><?= lang('Privileges') ?>:</b></label>
				</div>
				<div class="row">
					<div class="col-md-2 marketing">
						<label class="control-label">
							<input type="radio" class="add priv marketing" value="3" name="add_pr">
							<i class="fa fa-users"></i> <?= lang('lbo.marketing') ?>
						</label>
						<div class="pull-right priv-group">
							<!-- <label class="control-label">
								<input type="radio" class="add priv marketing" value="2" name="add_pr_marketing"> <?=lang('RW') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv marketing" value="1" name="add_pr_marketing"> <?=lang('RO') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv marketing" value="0" name="add_pr_marketing"> <?=lang('None') ?>
							</label>-->
						</div>
					</div>
					<div class="col-md-2 games">
						<label class="control-label">
							<input type="radio" class="add priv games" value="2" name="add_pr">
							<i class="icon-dice"></i> <?= lang('lbo.games') ?>
						</label>
						<div class="pull-right priv-group">
							<!-- <label class="control-label">
								<input type="radio" class="add priv games" value="2" name="add_pr_games"> <?=lang('RW') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv games" value="1" name="add_pr_games"> <?=lang('RO') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv games" value="0" name="add_pr_games"> <?= lang('None') ?>
							</label> -->
						</div>
					</div>
					<div class="col-md-2 finance">
						<label class="control-label">
							<input type="radio" class="add priv finance" value="4" name="add_pr">
							<i class="fa fa-money"></i> <?= lang('lbo.finance') ?>
						</label>
						<div class="pull-right priv-group ">
							<!-- <label class="control-label">
								<input type="radio" class="add priv finance" value="2" name="add_pr_finance"> <?= lang('RW') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv finance" value="1" name="add_pr_finance"> <?= lang('RO') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv finance" value="0" name="add_pr_finance"> <?= lang('None') ?>
							</label> -->
						</div>
					</div>
					<div class="col-md-2 admin">
						<label class="control-label">
							<input type="radio" class="add priv admin" value="1" name="add_pr">
							<i class="fa fa-key"></i> <?= lang('lbo.admin') ?>
						</label>
						<div class="pull-right priv-group">
							<!-- label class="control-label">
								<input type="radio" class="add priv admin" value="2" name="add_pr_admin"> <?= lang('RW') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv admin" value="1" name="add_pr_admin"> <?= lang('RO') ?>
							</label>
							<label class="control-label">
								<input type="radio" class="add priv admin" value="0" name="add_pr_admin"><?= lang('None') ?>
							</label> -->
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 10px;">
					<div class="col-md-4">
						<button id="add_panel_reset" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" type="button"><?=lang('sys.vu16');?></button>
						<button id="add_panel_submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="button" form="my_form"><?=lang('lang.submit');?></button>
						<div class="pull-right">
							<button id="add_panel_cancel" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>" type="button"><?= lang('Cancel') ?></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</div>


<div class="row" id="user-container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt pull-left"><i class="fa fa-ticket"></i>
					<?= lang('Lottery Backoffice Role Binding') ?>
				</h3>
				<!--
				<a href="#modal_column" role="button" data-toggle="modal" class="btn btn-sm btn-info pull-right panel-button">
					<span class="glyphicon glyphicon-list"></span> <?=lang('sys.vu57');?>
				</a>
				-->
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="list_panel_body">
				<!-- <form method="post" action="<?php echo site_url('user_management/typeOfAction'); ?>" id="my_form"> -->
                    <!-- <input type="hidden" name="submit_type" id="submit_type"> -->
                    <div style="margin-bottom: 10px;">
                		<div class="btn-action">
							<button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" type='button' onclick="$('#panel_add_binding').show(300);">
								<i class="fa fa-user-plus"></i> <?php echo lang('Add Binding');?>
							</button>

							<button id="del_binding" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" type='button' onclick="op_del_binding();">
								<i class="fa fa-user-times"></i> <?php echo lang('Delete Binding');?>
							</button>

						</div>
						<div class="clearfix"></div>
                    </div>
					<div class="table-responsive">
						<table class="table table-striped table-hover table-condensed table-bordered" style="width:100%;" id="my_table">

							<thead>
								<tr>
									<th rowspan="2" class="dt-check">
										<i class="fa fa-check"></i>
									</th>
									<th rowspan="2"><?= lang('Admin Username') ?></th>
									<th colspan="4"><?= lang('Privileges') ?></th>
									<th rowspan="2"><?= lang('Actions') ?></th>
								</tr>
								<tr>

									<th><?= lang('lbo.marketing') ?></th>
									<th><?= lang('lbo.games') ?></th>
									<th><?= lang('lbo.finance') ?></th>
									<th><?= lang('lbo.admin') ?></th>

								</tr>
							</thead>
							<form method="post" action="">
							<tbody>
								<?php if (!empty($roles)) : ?>
									<?php foreach ($roles as $row) : ?>

                                        <tr class="role" data-user-id="<?= $row['user_id'] ?>" data-dirty="0">

											<td class="cell cbox dt-check">
												<?php if ($row['selectable'] == true) : ?>
													<input type="checkbox" class="user check" id="user_<?=$row['user_id']?>" name="check[]" data-username="<?= $row['username'] ?>" value="<?=$row['user_id']?>">
												<?php endif; ?>
											</td>

											<td class="cell username"><?= $row['username'] ?></td>

											<?php foreach ($row['priv'] as $key => $pvc) : ?>
												<?php /*

												<td class="cell <?= $key ?>">
													<label>
														<input type="checkbox" value="2" name="<?= $cbname ?>" class="<?= $cbclass ?>" data-item="<?= $key ?>" data-oldval="<?= $pvc >= 2 ?>" <?= $pvc >= 2 ? ' checked ' : '' ?> />
														<?=lang('RW') ?></label>
													<label>
														<input type="checkbox" value="1" name="<?= $cbname ?>" class="<?= $cbclass ?>" data-item="<?= $key ?>" data-oldval="<?= $pvc == 1 ?>" <?= $pvc == 1 ? ' checked ' : '' ?> />
														<?=lang('RO') ?></label>
													<label>
														<input type="checkbox" value="0" name="<?= $cbname ?>" class="<?= $cbclass ?>" data-item="<?= $key ?>" data-oldval="<?= $pvc == 0 ?>" <?= $pvc == 0 ? ' checked ' : '' ?> />
														<?=lang('None') ?></label>
												</td>
												*/ ?>
												<td class="cell <?= $key ?>">
													<?php $cbname = "{$key}_{$row['user_id']}"; ?>
													<?php $cbclass = "cb priv {$key}"; ?>
														<input type="checkbox" value="1" name="<?= $cbname ?>" class="<?= $cbclass ?>" data-item="<?= $key ?>" data-oldval="<?= $pvc == 1 ?>" <?= $pvc == 1 ? ' checked ' : '' ?> />
												</td>
											<?php endforeach; ?>
											<td>
												<button class="btn btn-default btn-xs priv update disabled">
													<?= lang('Save') ?>
												</button>
												<button class="btn btn-default btn-xs priv reset disabled">
													<?= lang('Reset') ?>
												</button>
												<div class="priv mesg saved" style="display: none;">
													<?= lang('sys.ga.succsaved') ?>
												</div>
												<div class="priv mesg failed" style="display: none;">
													<?= lang('Saving failed') ?>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
							</form>
						</table>
					</div>
				<!-- </form> -->
			</div>
		</div>
	</div>
</div>
<?php if ($showraw) : ?>
<div class="row">
	<div class="col-md-6 roles-sec">
		<pre><?= print_r($roles, 1) ?></pre>
	</div>
	<div class="col-md-6 roles-sec">
		<pre><?= print_r($roles_raw, 1) ?></pre>
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function () {
		startup_priv_cb_scan();
		init_priv_cb_click();
		init_priv_update_button();
		init_priv_reset_button();
		setup_add_panel();
	});

	function startup_priv_cb_scan() {
		$('input.cb.priv').each(function () {
			// cb_admin_check(this);

		})
	}

	function init_priv_cb_click() {
		$('input.cb.priv').click(function () {
			// $(this).parents('td').find('input.cb.priv').not(this).prop('checked', false);
			$(this).parents('tr').find('input.cb.priv').not(this).prop('checked', false);
			$(this).prop('checked', true);
			// cb_admin_check(this);

			cb_row_dirty_check(this);
		});
	}

	function init_priv_reset_button() {
		$('td button.reset').click(function () {
			$(this).parents('tr').find('input.cb.priv').each(function () {
				var oldval = $(this).data('oldval');
				// console.log($(this).attr('class'), oldval);
				$(this).prop('checked', oldval == '1' ? true : false);
				cb_admin_check(this);
			});

			buttons_disable(this);
		});
	}

	function init_priv_update_button() {
		$('td button.update').click(function () {
			// console.log($(this).parents('tr').find('input.cb.priv:checked'));
			var row = $(this).parents('tr');
			var user_id = $(row).data('user-id');
			var upd_priv = { marketing: 0, games: 0, finance: 0, admin: 0 };
			$(row).find('input.cb.priv:checked').each(function () {
				var item = $(this).data('item');
				var val = $(this).val();
				upd_priv[item] = val;
			});
			console.log('update priv', user_id, upd_priv);

			if (upd_priv.marketing + upd_priv.games + upd_priv.finance + upd_priv.admin == 0) {
				alert('<?= lang('Please enable at least one privilege') ?>');
				$(this).parents('td').find('button.reset').click();
				buttons_disable(this);
			}
			else {
				var self = this;
				var update_xhr = $.post(
					'/user_management/lottery_bo_binding_update',
					{ user_id: user_id, update_priv: upd_priv } ,
					function (resp) {
						console.log(resp);
						if (resp.status == true) {
							buttons_conceal(self);
							row_cbs_disable(self);
							alert('<?= lang('Binding updated successfully') ?>');
						}
						else {
							alert("<?= lang('Error while Updating binding') ?>" + '\n' + resp.mesg + ' (' + resp.code + ')');
						}
					}
				);
			}

		});
	}

	function cb_row_dirty_check(self) {
		var row = $(self).parents('tr');
		var dirty = parseInt($(row).data('dirty'));
		var user_id = parseInt($(row).data('user-id'));
		if (!$(self).data('oldval') && dirty == 0) {
			console.log('row ' + user_id + ' turned dirty');
			buttons_enable(self);
		}
	}

	function buttons_enable(self) {
		var row = $(self).parents('tr');
		$(row).find('button.priv').removeClass('disabled');
		$(row).find('button.priv.update').removeClass('btn-default').addClass('btn-primary');
		$(row).data('dirty', 1);
		console.log('dirty', $(row).data('dirty'));
	}

	function buttons_disable(self, clean_dirty) {
		if (typeof(clean_dirty) == 'undefined') {
			clean_dirty = true;
		}
		var row = $(self).parents('tr');
		$(row).find('button.priv').addClass('disabled');
		$(row).find('button.priv.update').addClass('btn-default').removeClass('btn-primary');
		if (clean_dirty) {
			$(row).data('dirty', 0);
			console.log('dirty', $(row).data('dirty'));
		}
	}

	function buttons_conceal(self) {
		var row = $(self).parents('tr');
		$(row).find('button.priv').hide();
		$(row).find('div.priv.mesg.saved').show();
	}

	/**
	 * Disable/enable other priv checkboxes if admin priv checkbox is clicked
	 * Removed in OGP-7584
	 * @param	ref		self		reference of current ch
	 */
	function cb_admin_check(self) {
		var pr_item = $(self).data('item');
		var pr_val = $(self).val();
		// OGP-7584: no need to check for admin
		// console.log(pr_item, pr_val);
		// if ($(self).is(':checked') && pr_item == 'admin') {
		// 	var other_cbs = $(self).parents('tr').find('input.cb.priv').not('.admin');
		// 	if (pr_val == 0) {
		// 		$(other_cbs).each(function () {
		// 			$(this).removeAttr('disabled');
		// 			$(this).parent('label').removeClass('grayout');

		// 		});
		// 	}
		// 	else {
		// 		$(other_cbs).each(function () {
		// 			$(this).attr('disabled', 1);
		// 			$(this).parent('label').addClass('grayout');
		// 		});
		// 	}
		// }
	}

	function row_cbs_disable(self) {
		var row_cbs = $(self).parents('tr').find('input.cb.priv');
		$(row_cbs).each(function() {
			$(this).attr('disabled', 1);
			$(this).parent('label').addClass('grayout');
		})
	}

	function row_cbs_enable(self) {
		var row_cbs = $(self).parents('tr').find('input.cb.priv');
		$(row_cbs).each(function() {
			$(this).removeAttr('disabled');
			$(this).parent('label').removeClass('grayout');
		})
	}

	function setup_add_panel() {
		// Reset button
		$('#add_panel_reset').click( op_add_panel_reset );

		// Cancel button
		$('#add_panel_cancel').click( function () {
			op_add_panel_reset();
			$('#panel_add_binding').hide(300);
		});

		// Submit button
		$('#add_panel_submit').click( function () {
			op_add_panel_submit();
		});
	}

	function op_add_panel_reset() {
		var panel = $('#panel_add_binding');
		$(panel).find(':text,:password').val('');
		$(panel).find(':checked').prop('checked', false);
	}

	function op_add_panel_submit() {
		var panel = $('#panel_add_binding');
		var username = $('#username').val()
		var priv = { marketing: 0, games: 0, finance: 0, admin: 0 };
		try {
			if (username.length == 0) {
				throw "<?= lang('Please select one username in backend users') ?>";
			}

			for (var i in priv) {
				// var prg_name = 'add_pr_' + i;
				// var prg_val = $('input[name="' + prg_name + '"]:checked').val();
				var prg_val = $('input.add.priv.' + i + ':checked').val();
				console.log('checkbox', i, prg_val);
				if (prg_val) {
					// priv[i] = parseInt(prg_val);
					priv[i] = prg_val > 0 ? 1 : 0;
				}
			}
			console.log(username, priv);

			if (priv.marketing + priv.games + priv.finance + priv.admin == 0) {
				throw "<?= lang('Please enable at least one privilege') ?>";
			}

			var add_xhr = $.post(
				'/user_management/lottery_bo_binding_add' ,
				{ username: username , priv: priv } ,
				function (resp) {
					console.log(resp);
					if (resp.status == true) {
						setTimeout(function() {
							window.location.reload();
						}, 3000);
						alert('<?= lang('Binding added successfully') ?>');
						window.location.reload();
					}
					else {
						alert("<?= lang('Error while adding binding') ?>");
					}
				}
			);
		}
		catch (ex) {
			alert(ex);
			return;
		}
	}

	function op_del_binding() {
		try {
			var user_recv = $('.user.check:checked').map(function() {
				return {id: $(this).val(), username: $(this).data('username')};
			});
			var username_recv = $('.user.check:checked').map(function() {
				return $(this).data('username');
			});

			var users = user_recv.get();
			var usernames = username_recv.get();
			console.log(users, usernames);

			if (users.length == 0) {
				throw "<?= lang('Please check at least one user to delete') ?>";
			}

			var conf = confirm("<?= lang('Are you sure to delete these user bindings?') ?>" + "\n" + usernames.join(' , '));
			console.log('conf', conf)
			if (!conf) {
				throw "<?= lang('Deletion cancelled') ?>";
			}

			var del_xhr = $.post(
				'/user_management/lottery_bo_binding_delete' ,
				{ usernames: usernames } ,
				function (resp) {
					console.log(resp);
					if (resp.status == true) {
						setTimeout(function() {
							window.location.reload();
						}, 3000);
						alert('<?= lang('Binding deleted successfully') ?>');
						window.location.reload();
					}
					else {
						alert('<?= lang("Error while deleting binding") ?>' + '\n' + (resp.mesg ? resp.mesg : resp.result.mesg) + ' (' + resp.code + ')');
					}
				}
			);
		}
		catch (ex) {
			alert(ex);
			return;
		}

		// console.log(user_ids.get().length);
	}
</script>
<?php $module = $this->router->fetch_method();?>
<script type="text/javascript">

	var module = '<?php echo $module; ?>';

    $(document).ready(function(){
    	$('body').tooltip({
	        selector: '[data-toggle="tooltip"]',
	        placement: "bottom"
	    });

		// if(module == 'viewUsers') {
		// 	$('#status option[value="4"]').attr('selected','selected');
		// }

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
                }
            ],
            "columnDefs": [ {
                orderable: false,
                targets:   [ 0, 2, 3, 4, 5, 6 ]
            } ],
            "order": [ sortColUsername, 'asc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });
    });
</script>