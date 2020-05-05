<div class="panel panel-primary
              " id="addIpForm">
	<div class="panel-body">
		<form class="form-horizontal" method="post" action="<?php echo isset($ip_by_id) ? site_url('ip_management/verifyEditIp/' . $ip_by_id['ipId']) : site_url('ip_management/addIp'); ?>" autocomplete="off">
			<div>
				<?php if (!$isMyIpExists) {?>
					<h4>
					<?=lang('sys.ip14');?> <b><?=$this->input->ip_address()?></b>
					</h4>
					<button class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" type="submit" name="type_of_action" value="Submit" <?=(!empty($disable)) ? 'disabled' : ''?>><?=lang('sys.ip15');?></button>
				<?php }
?>

			</div>
			<br/>
			<div class="form-group">
				<div class="col-md-5">
					<input class="form-control input-sm" type="text" name="ip_name" id="ip_name" placeholder="<?=lang('sys.ip12');?> " value="<?=isset($ip_by_id) ? $ip_by_id['ipName'] : ''?>">
					<?php echo form_error('ip_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
				</div>
				<div class="col-md-5">
					<textarea style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);" name="remarks" placeholder="<?php echo lang('cashier.134') ?>" maxlength="300" class="form-control input-sm"><?=isset($ip_by_id) ? $ip_by_id['remarks'] : ''?></textarea>
				</div>
				<div class="col-md-2">
					<input type="reset" class="btn-sm btn mb-2 <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" name="reset" value="<?php echo lang('lang.reset') ?>">
					<button class="btn btn-sm mb-2 <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="submit" name="type_of_action" value="Submit"><?=lang('sys.ip13');?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="icon-tree"></i> <?=lang('system.ipList');?>
					<!-- <button class="btn btn-default btn-sm pull-right" id="button_list_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_list_up"></span></button> -->
					<?php if ($ipList == 'true') {?>
						<a href="#" class="btn btn-danger btn-sm pull-right panel-button" data-iprules="false" data-href="/ip_management/setIpList/false" data-tooltip="true" data-toggle="modal" data-target="#confirm-iprules">
							<i class="glyphicon glyphicon-ban-circle"></i>&nbsp;<?=lang('system.disableIpWhitelisting');?>
						</a>
						<button style="margin-right: 5px;" class="btn-info btn btn-sm pull-right panel-button" data-placement="top" data-toggle='tooltip' title="<?=lang('system.tooltip.disableIpWhitelisting');?>">?</button>
					<?php } else {?>
						<a href="#" class="btn pull-right btn-primary <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm panel-button'?>" data-iprules="true" data-href="/ip_management/setIpList/true" data-toggle="modal" data-target="#confirm-iprules">
							<i class="glyphicon glyphicon-ok-sign" data-placement="top"  data-toggle='tooltip' <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="color:white;"' : ''?>></i>&nbsp;<?=lang('system.enableIpWhitelisting');?>
						</a>
						<button style="margin-right: 5px;" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary btn-xs' : 'btn-success btn-sm panel-button'?>" data-placement="top" data-toggle='tooltip' title="<?=lang('system.tooltip.enableIpWhitelisting');?>">?</button>
					<?php }?>
				</h3>
			</div>

			<div class="panel-body" id="list_panel_body">
				<form method="post" action="<?php echo site_url('ip_management/checkIp'); ?>">
					<div class="table-responsive">
						<table class="table table-hover" id="myTable" style="width:100%;">
							<div class="btn-action" id="ipList">
								<button type="submit" value="Delete" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" >
			                        <i class="glyphicon glyphicon-trash" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="<?=lang('sys.vu38');?>"></i>&nbsp;<?=lang('system.deleteIpFromTheList');?>
			                    </button>&nbsp;
			                    <button type="submit" value="Freeze" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>" >
			                        <i class="glyphicon glyphicon-ban-circle" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="<?=lang('tool.pm08');?>"></i>&nbsp;<?=lang('system.blockIp');?>
			                    </button>&nbsp;
			                    <button type="submit" value="UnFreeze" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>" >
			                        <i class="glyphicon glyphicon-check" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="<?=lang('tool.pm09');?>"></i>&nbsp;<?=lang('system.allowIp');?>
			                    </button>&nbsp;
								</div>
							<br><br>
							<thead>
								<tr>
									<th style="padding:8px;"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/> <?=lang('sys.ip07');?></th>
									<th><?=lang('sys.ip08');?></th>
									<th><?=lang('cashier.134');?></th>
									<th><?=lang('sys.ip09');?></th>
									<th><?=lang('sys.ip10');?></th>
									<th><?=lang('sys.ip11');?></th>
									<th><?=lang('mess.07');?></th>
								</tr>
							</thead>
							<tbody>
								<?php
$disable = null;

foreach ($ip as $row) {
	if ($row['ipName'] == $this->input->ip_address()) {
		$disable = 'disable';
	}

	if ($row['status'] == 1) {
		?> 			<tr class="info">
								<?php } else {?>
											<tr>
								<?php }
	?>
								<?php if ($row['ipName'] != $this->input->ip_address()) {?>
											<td style="padding:8px;"><input type="checkbox" class="checkWhite" id="<?=$row['ipId']?>" name="ip[]" value="<?=$row['ipId']?>" onclick="uncheckAll(this.id)"/></td>
											<td><b><?=$row['ipName']?></b></td>
								<?php } else {?>
											<td></td>
											<td><b><?=$row['ipName']?></b></td>
								<?php }
	?>
										<td><?=$row['remarks'] == '' ? lang('N/A') : $row['remarks']?></td>
										<td><?=$row['createTime']?></td>
										<td><?=$row['realname']?></td>

										<td>
											<span class="help-block" style="<?=$row['status'] == 1 ? 'color:#ff6666;' : 'color:#66cc66;'?>">
												<?=$row['status'] == 1 ? lang('sys.ip16') : lang('sys.ip17')?>
											</span>
										</td>
	</form>
										<td>
											<?php
if ($row['ipName'] != $this->input->ip_address()) {
		if ($row['status'] == Ip::STATUS_ALLOW) {?>
												<a href="<?=BASEURL . 'ip_management/manageIp/' . $row['ipId'] . '/' . Ip::STATUS_BLOCK?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>">
												<i class="glyphicon glyphicon-ban-circle"></i>
												<?=lang('system.blockIp')?></a>
											<?php } elseif ($row['status'] == Ip::STATUS_BLOCK) {
			?>
												<a href="<?=BASEURL . 'ip_management/manageIp/' . $row['ipId'] . '/' . Ip::STATUS_ALLOW?>" class="btn btn-success btn-sm">
													<i class="glyphicon glyphicon-check"></i>
													<?=lang('system.allowIp')?></a>
												<?php
}
	} else {
		echo '<b>' . lang('system.myIP') . '</b>';
	}
	?>
											<?php
if ($row['ipName'] != $this->input->ip_address()) {?>
													<a href="<?=BASEURL . 'ip_management/manageIp/' . $row['ipId'] . '/' . Ip::STATUS_DELETE?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>">
													<i class="glyphicon glyphicon-trash" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="color:white;"' : ''?>></i>
													<?=lang('system.deleteIp')?></a>
													<a href="<?=BASEURL . 'ip_management/editIp/' . $row['ipId']?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>">
													<i class="glyphicon glyphicon-edit" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="color:white;"' : ''?>></i>
													<?=lang('system.editIp')?></a>
											<?php }
	?>

										</td>
									</tr>
								<?php }
?>
							</tbody>
						</table>
					</div>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirm-iprules" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('sys.ip.message1')?></h4>
            </div>
            <div class="modal-body">
                <p class="ip-rules-desc"></p>
                <?php if (!$isMyIpExists) {?>
                <p><?=lang('sys.ip.message3')?></p>
                <?php }
?>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal"><?=lang('lang.cancel')?></button>
                <a class="btn btn-ok <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('lang.yes')?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	function autogrow(textarea){
        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
        if (adjustedHeight>textarea.clientHeight){
            textarea.style.height = adjustedHeight + 'px';
        }
    }
    $(document).ready(function(){
    	var isIpWhiteListEnable = "<?=$ipList?>";
    	if(isIpWhiteListEnable == 'true'){
    		//$('#addIpForm').show();
    		// $('#list_panel_body').show();
    	}else{
    		//$('#addIpForm').hide();
    		// $('#list_panel_body').hide();
    	}


    	$('#confirm-iprules').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            if($(e.relatedTarget).data('iprules')){
            	//console.log('');
            	$('.ip-rules-desc').html('<?=lang('system.message.enableIpWhitelisting')?>');
            }else{
            	// console.log('ip rules is false');
            	$('.ip-rules-desc').html('<?=lang('system.message.disableIpWhitelisting')?>');
            }
        });

    	$('body').tooltip({
	        selector: '[data-toggle="tooltip"]',
	        placement: "bottom"
	    });

        $('#myTable').DataTable({
        	dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                orderable: false,
                targets:   0
            } ],
            "order": [ 2, 'desc' ],
           // "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });
    });
</script>