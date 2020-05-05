<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="icon-settings"></i>
					<?=lang('Livechat Setting');?>
				</h3>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="panel_body">
				<div class="col-md-12">
					<form action="<?=site_url('livechat_management/saveLivechatSetting');?>" method="post" id="livechat-setting-form">
						<table class="table table-striped table-hover table-bordered" style="width: 100%;">
							<thead>
				    			<tr>
				    				<th style="text-align: left;"><?=lang('Name');?></th>
				    				<th style="text-align: center;"><?=lang('Description');?></th>
				    				<th style="text-align: center;"><?=lang('Data');?></th>
				    			</tr>
				    		</thead>
				    		<tbody style="text-align: center;">
				    			<?php foreach ($items as $key => $value) {?>
				        			<tr>
				        				<td class="col-md-5" style="text-align: left;"><?php echo $value['livechatSettingName']; ?> </td>
				        				<td class="col-md-2" style="text-align: left;">
											<?php echo $value['description']; ?>
				        				</td>
				        				<td class="col-md-2">
				        					
				        				<input type="number" name='<?php echo $value['livechatSettingName']; ?>' class="form-control input-sm number_only" value='<?php echo $value['livechatData'];?>' min="1" max="10000" maxlength="5" required />
				        				</td>
				        			</tr>
				    			<?php }?>
				    		</tbody>
						</table>
						<center>
							<button type="submit" class="btn btn-info input-xs" ><i class="fa fa-check"></i> <?=lang('lang.save');?></button>
						</center>
					</form>
				</div>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">


</script>