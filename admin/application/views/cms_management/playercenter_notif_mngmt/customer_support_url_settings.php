<div class="panel panel-body">
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th width="70%"><?=lang('Customer Support Url');?></th>
						<th width="15%"><?=lang('cms.status');?></th>
						<th width="30%"><?=lang('lang.action');?></th>
					</tr>
					<tr>
						<td>
							<?=$notif_settings['customer_support'][0]['url']?>	
						</td>
						<td><?=$notif_settings['customer_support'][0]['is_enable'] == 'true' ? lang('lang.active') : lang('lang.inactive')?></td>
						<td><a href="<?=BASEURL . 'cms_management/editNotificationItem/true/customer_support_url'?>" data-toggle="tooltip" title="<?=lang('Edit Customer Support Url');?>" class="blue"><span class="glyphicon glyphicon-pencil"></span></a>
							<?php 
								if($notif_settings['customer_support'][0]['is_enable'] == "true"){
							?>
								<a href="<?=BASEURL . 'cms_management/setSettingsStatus/customer_support/0/false'?>" data-toggle="tooltip" title="<?=lang('system.word92');?>" class="red"><span class="glyphicon glyphicon-remove-sign"></span></a>
							<?php }else{ ?>
								<a href="<?=BASEURL . 'cms_management/setSettingsStatus/customer_support/0/true'?>" data-toggle="tooltip" title="<?=lang('system.word88');?>" class="blue"><span class="glyphicon glyphicon-ok-sign"></span></a>
							<?php } ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<br>
</div>