<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h3 class="panel-title custom-pt"><i class="icon-sound"></i> <?=lang('notify.notification');?>
					<!-- <button class="btn btn-default btn-sm pull-right" id="button_list_toggle"><span class="glyphicon glyphicon-chevron-up" id="button_span_list_up"></span></button> -->

				</h3>
			</div>
			<div class="panel-body" id="list_panel_body">
					<div class="table-responsive">
						<table class="table table-hover" id="myTable" style="width:100%;">
							<div class="btn-action pull-right" id="notification">
									<a href="<?=site_url('notification_management/settings')?>">
											<button type="submit" value="Delete" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" >
					                        <i class="glyphicon glyphicon-cog" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="<?=lang('notify.setting');?>"></i>&nbsp;<?=lang('notify.setting');?>
					                    </button>
									</a>
									&nbsp;
									<a href="javascript:NotificationManagement.deleteSelected('<?=lang('notify.delete.confirm')?>')">
											<button type="submit" value="Delete" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>">
					                        <i class="glyphicon glyphicon-trash" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="<?=lang('notify.delete');?>"></i>&nbsp;<?=lang('notify.delete');?>
					                    </button>
									</a>
									&nbsp;
				                    <a href="<?=site_url('notification_management/add')?>">
				                    	<button type="submit" value="Freeze" name="type_of_action" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
				                        <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;<?=lang('notify.add');?>
				                    </button>
			                    </a>
								</div>
							<div class="clearfixed"></div>
							<br><br>
							<thead>
								<tr>
									<th style="padding:8px; width:5%"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/> <?=lang('sys.ip07');?></th>
									<th><?=lang('notify.name');?></th>
									<th><?=lang('notify.path');?></th>
									<th style="width: 20%;"><?=lang('notify.action');?></th>
								</tr>
							</thead>
							<tbody>

								<?php
									if( ! empty( $records ) ){
										foreach ($records as $key => $value) {

								?>
										<tr>
											<td>
												<?php
													if( ! in_array($value['id'], $notifications) ){
												?>
												<input type="checkbox" name="item_id" id="item_id" value="<?=$value['id']?>">
												<?php } ?>
											</td>
											<td><?=$value['title']?></td>
											<td><?=$value['file']?></td>
											<td>
												<?php
													if( in_array($value['id'], $notifications) ){
														echo lang('notify.already.use');
													}else{
												?>
														<a class="delete-vip" href="javascript:NotificationManagement.delete(<?=$value['id']?>, '<?=lang('notify.delete.confirm')?>')" >
		                                            		<span data-toggle="tooltip" title="" class="glyphicon glyphicon-trash" data-placement="top" data-original-title="Delete">
		                                            	</span>
		                                        </a>
												<?php
													}
												?>

											</td>
										</tr>
								<?php
										}
									}
								?>

							</tbody>
						</table>
					</div>

			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?=site_url()?>"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#viewNotification').addClass('active');
	});
	$(function(){

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

	function checkAll(id){

		$('input[id="item_id"]').each(function(){

			if( $('#' + id).is(':checked') ) return $(this).prop('checked', true);

			$(this).prop('checked', false);

		});
	}
</script>

