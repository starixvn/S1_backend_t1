<div class="panel panel-body">
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th width="30%"><?=lang('Cashback Notification Error');?></th>
						<th width="20%"><?=lang('cms.lang');?></th>
						<th width="20%"><?=lang('Custom Error Code');?></th>
						<th width="30%"><?=lang('Custom Error Message');?></th>
						<th width="30%"><?=lang('Player Option Message');?></th>
						<th width="15%"><?=lang('cms.status');?></th>
						<th width="30%"><?=lang('lang.action');?></th>
					</tr>
					<?php
						foreach ($notif_settings['cashback_notif'] as $key => $value) {
							if($value['is_inuse'] == 'true'){
					?>
						<tr>
							<td><?=lang($value['label'])?></td>
							<td><?=$value['multi_lang_messages'][$lang_code]['language']?></td>
							<td><?=$value['custom_error_code']?></td>
							<td><?=$value['multi_lang_messages'][$lang_code]['claim_error_notif_msg']?></td>
							<td><?=$value['multi_lang_messages'][$lang_code]['player_option_msg']?></td>
							<td><?=$value['is_enabled'] == 'true' ? lang('lang.active') : lang('lang.inactive')?></td>
							<td width="10%">
								<a href="<?=BASEURL . 'cms_management/editNotificationItem/'.$key.'/cashback_claim'?>" data-toggle="tooltip" title="<?=lang('Edit Notification Message');?>" class="blue">
									<span class="glyphicon glyphicon-pencil"></span>
								</a>
								<?php 
									if($value['is_enabled'] == "true"){
								?>
									<a href="<?=BASEURL . 'cms_management/setSettingsStatus/cashback_notif/' . $key.'/false'?>" data-toggle="tooltip" title="<?=lang('system.word92');?>" class="red"><span class="glyphicon glyphicon-remove-sign"></span></a>
								<?php }else{ ?>
									<a href="<?=BASEURL . 'cms_management/setSettingsStatus/cashback_notif/' . $key.'/true'?>" data-toggle="tooltip" title="<?=lang('system.word88');?>" class="blue"><span class="glyphicon glyphicon-ok-sign"></span></a>
								<?php } ?>
							</td>
						</tr>
					<?php }
					} ?>
				</table>
				</table>
			</div>
		</div>
	</div>
	<br>
</div>