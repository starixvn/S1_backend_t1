<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="icon-sphere"></i> <?= lang('sys.dm1'); ?></h3>
			</div>

			<div class="panel-body" id="list_panel_body">
				<div class="col-md-7">
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

									foreach ($domain as $key => $value) { 
										if($value['domainId'] == $domain_id) {
											$domain_name = $value['domainName'];
											$notes_db = $value['notes'];
										}

										$count++;
								?>
										<tr>
											<td></td>
											<td><?= $count ?></td>
											<td><?= $value['domainName'] ?></td>
											<td>
											<?php if ($value['show_to_affiliate'] == 0): ?>
												<span class="text-danger">Hidden</span>
											<?php elseif ($value['show_to_affiliate'] == 1): ?>
												<span class="text-success">Visible to All</span>
											<?php else: ?>
												<a href="/affiliate_management/domain_affiliates/<?=$value['domainId']?>">Visible to <?=$value['affiliates']?> Affiliates</a>
											<?php endif ?>
											</td>
											<td><?= ($value['status'] == 0) ? lang('sys.dm9') : lang('sys.dm10') ?></td>
											<td><?= $value['notes'] ?></td>
											<td>
												<a href="<?= BASEURL . 'affiliate_management/editDomain/' . $value['domainId']?>" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" class="edit" onclick=""><span class="glyphicon glyphicon-pencil"></span></a>
												<a href="<?= BASEURL . 'affiliate_management/deleteDomain/' . $value['domainId']?>" data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>
												
												<?php if($value['status'] == 0) { ?>
													<a href="#" data-toggle="tooltip" title="<?= lang('lang.deactivate'); ?>" class="deactivate" onclick="deactivateDomain('<?= $value['domainId']?>','<?= $value['domainName']?>');" ><span class="glyphicon glyphicon-remove-circle"></span></a>
												<?php } else { ?>
													<a href="#" data-toggle="tooltip" title="<?= lang('lang.activate'); ?>" class="activate" onclick="activateDomain('<?= $value['domainId']?>','<?= $value['domainName']?>');"><span class="glyphicon glyphicon-ok-sign"></span></a>
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
					<form class="form-horizontal" method="POST" action="<?= BASEURL . 'affiliate_management/verifyEditDomain/' . $domain_id ?>" accept-charset="utf-8" enctype="multipart/form-data" >
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
									<input type="radio" name="show_to_affiliate" id="show_to_affiliate1" value="0" <?php if ($edit_domain['show_to_affiliate'] == 0) echo 'checked'?>>
									<?=lang('Hidden to all affiliates')?>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="show_to_affiliate" id="show_to_affiliate2" value="1" <?php if ($edit_domain['show_to_affiliate'] == 1) echo 'checked'?>>
										<?=lang('Visible to all affiliates')?>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="show_to_affiliate" id="show_to_affiliate3" value="2" <?php if ($edit_domain['show_to_affiliate'] == 2) echo 'checked'?>>
										<?=lang('Visible to affiliates: ')?>
									</label>
								</div>
								<input type="file" name="usernames" class="form-control" accept=".csv" <?php if ($edit_domain['show_to_affiliate'] != 2) echo 'disabled'?> <?php if ($affiliate_domain_count == 0) echo 'required'?>/>
								<?php if ($affiliate_domain_count == 0): ?>
								<span class="help-block"><?=lang('Note: Upload file format must be CSV')?></span>
								<?php else: ?>
								<span class="help-block"><a href="/affiliate_management/domain_affiliates/<?=$domain_id?>"><?=lang('Visible to')?> <?=$affiliate_domain_count?> <?=lang('Affiliates')?></a></span>
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
								<a href="<?= BASEURL . 'affiliate_management/viewDomain' ?>" class="btn btn-default btn-sm"><?= lang('lang.cancel'); ?></a>
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

        $('input[name="show_to_affiliate"]').change(function() {
        	var disabled = $('input[name="show_to_affiliate"]:checked').val() != 2;
        	$('input[name="usernames"]').prop('disabled', disabled);
        });
    });
</script>