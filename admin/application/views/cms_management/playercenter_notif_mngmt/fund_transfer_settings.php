<div class="panel panel-body">
		<div class="table-responsive">
			<table class="table table-striped table-hover" id="cnmTable" style="width:100%">
					<thead>
						<tr>
							<th style="min-width:102px"><?=lang('Game Error Code');?></th>
							<th style="min-width:150px"><?=lang('Transfer Fund Error Type');?></th>
							<th style="min-width:100px"><?=lang('cms.lang');?></th>
							<th style="min-width:120px"><?=lang('Custom Error Code');?></th>
							<th style="min-width:150px"><?=lang('Custom Error Message');?></th>
							<th style="min-width:250px"><?=lang('Player Option Message 1');?></th>
							<th style="min-width:250px"><?=lang('Player Option Message 2');?></th>
							<th style="min-width:50px"><?=lang('cms.status');?></th>
							<th style="min-width:50px"><?=lang('lang.action');?></th>
						</tr>
					</thead>
				<tbody>
				<?php
				foreach ($notif_settings['transfer_fund_notif'] as $key => $value) {
					if(isset($value['is_inuse'])) {
					if($value['is_inuse'] == 'true'){
				?>
					<tr>
						<td><?php echo $key; //$this->utils->getErrGameCodeByOperatorSettingsErrCode($key);  ?> </td>
						<td><?=lang($value['label'])?></td>
						<td><?=$value['multi_lang_messages'][$lang_code]['language']?></td>
						<td><?=$value['custom_error_code']?></td>
						<td><?=$value['multi_lang_messages'][$lang_code]['custom_error_msg']?></td>
						<td><?=$value['multi_lang_messages'][$lang_code]['player_option_msg1']?></td>
						<td><?=$value['multi_lang_messages'][$lang_code]['player_option_msg2']?></td>
						<td><?=$value['is_enabled'] == 'true' ? lang('lang.active') : lang('lang.inactive')?></td>
						<td>
							<a href="<?=BASEURL . 'cms_management/editNotificationItem/' . $key?>" data-toggle="tooltip" title="<?=lang('Edit Notification Message');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
							<?php
								if($value['is_enabled'] == "true"){
							?>
								<a href="<?=BASEURL . 'cms_management/setSettingsStatus/transfer_fund_notif/' . $key.'/false'?>" data-toggle="tooltip" title="<?=lang('system.word92');?>" class="red"><span class="glyphicon glyphicon-remove-sign"></span></a>
							<?php }else{ ?>
								<a href="<?=BASEURL . 'cms_management/setSettingsStatus/transfer_fund_notif/' . $key.'/true'?>" data-toggle="tooltip" title="<?=lang('system.word88');?>" class="blue"><span class="glyphicon glyphicon-ok-sign"></span></a>
							<?php } ?>
						</td>
					</tr>

				<?php }
					}
					}?>
				</tbody>
			</table>
		</div>
	<br>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        $('#cnmTable').DataTable({
						"dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
            {
              extend: 'colvis',
              postfixButtons: [ 'colvisRestore' ]
          }],

            "order": [ 3, 'asc' ]
        });
    });
</script>