<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="icon-sphere"></i> <?=lang('sys.dm1');?></h3>
			</div>

			<div class="panel-body" id="list_panel_body">
				<div class="<?=$this->permissions->checkPermissions('edit_agent_domain') ? 'col-md-7' : 'col-md-12' ?> table-responsive">
					<div class="alert alert-info">
				    	<?=lang('tip.agency.what_domain_do');?>
				    </div>
					<table class="table table-bordered" id="myTable" style="width:100%;">
						<thead>
							<tr>
								<th></th>
								<th>#</th>
								<th><?=lang('sys.dm2');?></th>
								<th><?= lang('Visibility'); ?></th>
								<th><?=lang('sys.dm3');?></th>
								<th><?=lang('sys.dm4');?></th>
								<?php if($this->permissions->checkPermissions('edit_agent_domain')): ?>
									<th><?=lang('sys.dm5');?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
								<?php
									$count = 0;
								if(!empty($domain_list)){
									foreach ($domain_list as $key => $value) {
										$count++;
										?>
										<tr class="<?=$value['status'] == Agency_model::STATUS_DISABLED ? 'bg-danger' : ''?>">
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
											<?php if($this->permissions->checkPermissions('edit_agent_domain')): ?>
												<td>
													<a href="<?='/agency_management/edit_domain/' . $value['id']?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>" class="edit" ><span class="glyphicon glyphicon-pencil"></span></a>
													<a href="#deleteDomain" onclick="deleteDomain(<?=$value['id']?>)" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>

													<?php if ($value['status'] == Agency_model::STATUS_NORMAL) {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="deactivate" onclick="deactivateDomain('<?=$value['id']?>','<?=$value['domain_name']?>');" ><span class="glyphicon glyphicon-remove-circle"></span></a>
													<?php } else {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="activate" onclick="activateDomain('<?=$value['id']?>','<?=$value['domain_name']?>');"><span class="glyphicon glyphicon-ok-sign"></span></a>
													<?php } ?>
												</td>
											<?php endif; ?>
										</tr>

								<?php
								}
							} ?>
						</tbody>
					</table>
				</div>
				<?php if($this->permissions->checkPermissions('edit_agent_domain')): ?>
					<hr style="margin-top:5px;"/>
					<div class="col-md-5">
						<form class="form-horizontal" method="POST" action="<?='/agency_management/new_domain'?>" accept-charset="utf-8" enctype="multipart/form-data" >
							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<label for="domain" class="control-label"><?=lang('sys.dm6');?> </label>
									<input type="text" name="domain" id="domain" value="<?=set_value('domain')?>" class="form-control input-sm">
									<?php echo form_error('domain', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								</div>
								<div class="col-md-10 col-md-offset-1">
									<label for="notes" class="control-label"><?= lang('Domain Visibility'); ?>: </label>
									<div class="radio">
										<label>
										<input type="radio" name="show_to_agent_type" id="show_to_agent_type1" value="1">
										<?=lang('Hidden to all agents')?>
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="show_to_agent_type" id="show_to_agent_type2" value="2" checked>
											<?=lang('Visible to all agents')?>
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="show_to_agent_type" id="show_to_agent_type3" value="3">
											<?=lang('Visible to agents')?>
										</label>
									</div>
									<input type="file" name="usernames" class="form-control" accept=".csv" required disabled/>
									<span class="help-block"><?=lang('Note: Upload file format must be CSV')?></span>
								</div>
								<div class="col-md-10 col-md-offset-1">
									<label for="notes" class="control-label"><?=lang('sys.dm7');?> </label>
									<textarea name="notes" id="notes" class="form-control" style="resize: none; height: 100px;"><?=set_value('notes')?></textarea>
									<?php echo form_error('notes', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								</div>
								<div class="col-md-10 col-md-offset-1" style="padding-top:15px;">
									<button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>"><i class="fa fa-plus"></i> <?=lang('sys.dm8');?>
								</div>
							</div>
						</form>
					</div>
				<?php endif;?>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">

function activateDomain(domain_id, domain_name) {
    if (confirm("<?=lang('Are you sure you want to activate this domain')?>"+': ' + domain_name + '?')) {
        window.location.href = "/agency_management/activateDomain/" + domain_id + "/" + encode64(domain_name);
    }
}

function deactivateDomain(domain_id, domain_name) {
    if (confirm("<?=lang('Are you sure you want to deactivate this domain')?>"+': '  + domain_name + '?')) {
        window.location.href = "/agency_management/deactivateDomain/" + domain_id + "/" + encode64(domain_name);
    }
}

function encode64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789";

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
}

	function deleteDomain(id){
		if(confirm('<?=lang("Do you want delete domain")?>?')){
			window.location.href="/agency_management/delete_domain/"+id;
		}
	}

    $(document).ready(function(){
        $('#myTable').DataTable({
        	dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
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