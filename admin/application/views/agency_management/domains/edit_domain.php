<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="icon-sphere"></i> <?= lang('sys.dm1'); ?></h3>
			</div>

			<div class="panel-body" id="list_panel_body">
				<div class="col-md-7">
					<p>
					<a href="/agency_management/agent_domain_list" class="btn btn-primary btn-sm"><?php echo lang('New Domain');?></a>
					</p>
					<table class="table table-striped table-bordered" id="myTable" style="width:100%;">
						<thead>
							<tr>
								<th></th>
								<th>#</th>
								<th><?= lang('sys.dm2'); ?></th>
								<th><?= lang('Visibility'); ?></th>
								<th><?= lang('sys.dm3'); ?></th>
								<th><?= lang('sys.dm4'); ?></th>
								<th><?= lang('sys.dm5'); ?></th>
							</tr>
						</thead>

						<tbody>
								<?php
									$count = 0;

									foreach ($domain_list as $key => $value) {
										if($value['id'] == $domain_id) {
											$domain_name = $value['domain_name'];
											$notes_db = $value['notes'];
										}

										$count++;
								?>
										<tr>
											<td></td>
											<td><?=$count?></td>
											<td><?=$value['domain_name']?></td>
											<td>
											<?php if ($value['show_to_agent_type'] == Agency_model::SHOW_TO_AGENT_TYPE_HIDDEN): ?>
												<span class="text-danger"><?php echo lang('Hidden'); ?></span>
											<?php elseif ($value['show_to_agent_type'] == Agency_model::SHOW_TO_AGENT_TYPE_ALL): ?>
												<span class="text-success"><?php echo lang('Visible to All'); ?></span>
											<?php else: ?>
												<a href="/agency_management/domain_agents/<?=$value['id']?>"><?php echo lang('Visible to'); ?> <?=$value['count_of_agent']?> <?php echo lang('Agents');?></a>
											<?php endif ?>
											</td>
											<td><?=($value['status'] == Agency_model::STATUS_NORMAL) ? lang('sys.dm9') : lang('sys.dm10')?></td>
											<td><?=$value['notes']?></td>
											<td>
												<a href="<?='/agency_management/edit_domain/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>" class="edit" ><span class="glyphicon glyphicon-pencil"></span></a>
												<a href="#deleteDomain" onclick="deleteDomain(<?=$value['id']?>)" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>

												<?php if ($value['status'] == Agency_model::STATUS_NORMAL) {?>
													<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="deactivate" onclick="deactivateDomain('<?=$value['id']?>','<?=$value['domain_name']?>');" ><span class="glyphicon glyphicon-remove-circle"></span></a>
												<?php } else {?>
													<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="activate" onclick="activateDomain('<?=$value['id']?>','<?=$value['domain_name']?>');"><span class="glyphicon glyphicon-ok-sign"></span></a>
												<?php } ?>
											</td>
										</tr>
								<?php
									}
								?>
						</tbody>
					</table>
				</div>
				<hr style="margin-top:5px;"/>
				<div class="col-md-5">
					<form class="form-horizontal" method="POST" action="<?='/agency_management/verifyEditDomain/' . $domain_id ?>" accept-charset="utf-8" enctype="multipart/form-data" >
						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<label for="domain" class="control-label"><?= lang('sys.dm6'); ?> </label>
								<input type="hidden" name="domain_name" id="domain_name" value="<?= $domain_name ?>" class="form-control">
								<input type="text" name="domain" id="domain" value="<?= (set_value('domain') != null) ? set_value('domain'):$domain_name ?>" class="form-control">
								<?php echo form_error('domain', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-10 col-md-offset-1">
								<label for="notes" class="control-label"><?= lang('Domain Visibility'); ?>: </label>
								<div class="radio">
									<label>
									<input type="radio" name="show_to_agent_type" id="show_to_agent_type1" value="<?=Agency_model::SHOW_TO_AGENT_TYPE_HIDDEN?>" <?php if ($edit_domain['show_to_agent_type'] == Agency_model::SHOW_TO_AGENT_TYPE_HIDDEN) echo 'checked'?>>
									<?=lang('Hidden to all agents')?>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="show_to_agent_type" id="show_to_agent_type2" value="<?=Agency_model::SHOW_TO_AGENT_TYPE_ALL?>" <?php if ($edit_domain['show_to_agent_type'] == Agency_model::SHOW_TO_AGENT_TYPE_ALL) echo 'checked'?>>
										<?=lang('Visible to all agents')?>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="show_to_agent_type" id="show_to_agent_type3" value="<?=Agency_model::SHOW_TO_AGENT_TYPE_BATCH?>" <?php if ($edit_domain['show_to_agent_type'] == Agency_model::SHOW_TO_AGENT_TYPE_BATCH) echo 'checked'?>>
										<?=lang('Visible to agents')?>
									</label>
								</div>
								<input type="file" name="usernames" class="form-control" accept=".csv" <?php if ($edit_domain['show_to_agent_type'] != Agency_model::SHOW_TO_AGENT_TYPE_BATCH) echo 'disabled'?> <?php if ($agent_domain_count == 0) echo 'required'?>/>
								<?php if ($agent_domain_count == 0): ?>
								<span class="help-block"><?=lang('Note: Upload file format must be CSV')?></span>
								<?php else: ?>
								<span class="help-block"><a href="/agency_management/domain_agents/<?=$domain_id?>"><?=lang('Visible to')?> <?=$agent_domain_count?> <?=lang('Agents')?></a></span>
								<?php endif ?>
							</div>
							<div class="col-md-10 col-md-offset-1">
								<label for="notes" class="control-label"><?= lang('sys.dm7'); ?> </label>
								<input type="hidden" name="notes_db" id="notes_db" value="<?= $notes_db ?>" class="form-control">
								<textarea name="notes" id="notes" class="form-control" style="resize: none; height: 100px;"><?= (set_value('notes') != null) ? set_value('notes'):$notes_db ?></textarea>
								<?php echo form_error('notes', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-10 col-md-offset-1" style="padding-top:15px;">
								<input type="submit" value="<?= lang('lang.edit'); ?>" class="btn btn-info btn-sm"/>
								<a href="<?= '/agency_management/agent_domain_list' ?>" class="btn btn-default btn-sm"><?= lang('lang.cancel'); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function deleteDomain(id){
		if(confirm('<?=lang("Do you want delete domain")?>?')){
			window.location.href="/agency_management/delete_domain/"+id;
		}
	}

    $(document).ready(function(){
        $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });

        $('input[name="show_to_agent_type"]').change(function() {
        	var disabled = $('input[name="show_to_agent_type"]:checked').val() != <?=Agency_model::SHOW_TO_AGENT_TYPE_BATCH?>;
        	$('input[name="usernames"]').prop('disabled', disabled);
        });
    });
</script>