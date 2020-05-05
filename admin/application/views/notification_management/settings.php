<style type="text/css">
	.mbottom1{
		margin-bottom: 5px;
	}
</style>
<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-gear"></i> <?=lang('notify.setting')?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<td><?=lang('notify.title')?></td>
								<td width="20%"></td>
							</tr>
						</thead>
						<tbody>
							<tr class="active">
								<td>Promo</td>
								<td>

										<?php
											if( in_array(1, $setting) ){
										?>
												<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
												<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[1]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
												</a>
												&nbsp;
												<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 1)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
										<?php
											}else{
										?>
												<a href="javascript:NotificationManagement.setSoundNotif(1)" title="<?=lang('notify.set.btn')?>">
													<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
												</a>
										<?php
											}
										?>
								</td>
							</tr>
							<tr class="active">
								<td>Messages</td>
								<td>
									<?php
										if( in_array(2, $setting) ){
									?>
											<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
											<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[2]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
												<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
											</a>
											&nbsp;
											<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 2)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
									<?php
										}else{
									?>
											<a href="javascript:NotificationManagement.setSoundNotif(2)" title="<?=lang('notify.set.btn')?>">
												<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
											</a>
									<?php
										}
									?>
								</td>
							</tr>
							<tr class="active">
								<td>Local Bank Deposit</td>
								<td>
									<?php
										if( in_array(3, $setting) ){
									?>
											<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
											<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[3]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
												<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
											</a>
											&nbsp;
											<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 3)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
									<?php
										}else{
									?>
											<a href="javascript:NotificationManagement.setSoundNotif(3)" title="<?=lang('notify.set.btn')?>">
												<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
											</a>
									<?php
										}
									?>
								</td>
							</tr>
							<tr class="active">
								<td>3rd Party Deposit</td>
								<td>
									<?php
										if( in_array(4, $setting) ){
									?>
											<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
											<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[4]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
												<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
											</a>
											&nbsp;
											<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 4)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
									<?php
										}else{
									?>
											<a href="javascript:NotificationManagement.setSoundNotif(4)" title="<?=lang('notify.set.btn')?>">
												<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
											</a>
									<?php
										}
									?>
								</td>
							</tr>
							<tr class="active">
								<td>Withdraw</td>
								<td>
									<?php
										if( in_array(5, $setting) ){
									?>
											<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
											<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[5]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
												<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
											</a>
											&nbsp;
											<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 5)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
									<?php
										}else{
									?>
											<a href="javascript:NotificationManagement.setSoundNotif(5)" title="<?=lang('notify.set.btn')?>">
												<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
											</a>
									<?php
										}
									?>
								</td>
							</tr>
							<tr class="active">
								<td><?= lang('report.sum05') ?></td>
								<td>
									<?php
										if( in_array(6, $setting) ){
									?>
											<span style="color:#008cba;" class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>&nbsp;
											<a href="javascript:NotificationManagement.notificationPlay('<?=$notification[6]?>','<?=$activeCurrencyKeyOnMDB?>')" title="Play Notification">
												<span style="color:#008cba;" class="glyphicon glyphicon-play" aria-hidden="true"></span>
											</a>
											&nbsp;
											<a href="javascript:NotificationManagement.removeNotification('<?=lang('notify.remove.confirm')?>', 6)" title="Remove notification">
													<span style="color:#008cba;" class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</a>
									<?php
										}else{
									?>
											<a href="javascript:NotificationManagement.setSoundNotif(6)" title="<?=lang('notify.set.btn')?>">
												<span style="color: #999;" class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>
											</a>
									<?php
										}
									?>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="notif_form hide">
						<div class="col-md-9 mbottom1">
						<form id="set_notif" method="post">
							<input type="hidden" name="notif_id" id="notif_id" value="">
							<select name="notif_sound" class="form-control" id="notif-sound">
								<option value="">Select Notification</option>
								<?php
									if( ! empty( $records ) ){
										foreach ($records as $key => $value) {
								?>
											<option value="<?=$value['id']?>"><?=$value['title']?></option>
								<?php
										}
									}
								?>
							</select>

						</form>

						<?php
							if( ! empty( $records ) ){
								foreach ($records as $key => $value) {
						?>
									<input type="hidden" name="notif_<?=$value['id']?>" value="<?=$value['file']?>" />
						<?php
								}
							}
						?>
					</div>
					<div class="col-md-3">
						<button class="btn btn-info form-control set-notif">SET</button>
					</div>
					<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="panel-footer ">
				<div class="col-md-5">
					<a href="<?=site_url('notification_management')?>">
						<button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info form-control'?>"><?=lang('notify.back')?></button>
					</a>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		NotificationManagement.init('<?=site_url()?>');
	});
</script>