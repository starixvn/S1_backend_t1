<!--main-->
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseTaggedList" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>

    </div>

    <div id="collapseTaggedList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form class="form-inline" action="/player_management/searchLinkedPlayer" method="post">
				<div class="form-group" style="margin-right: 30px;">
					<input type="radio" <?php echo $this->session->userdata('searchType') == Linked_account_model::SEARCH_TYPE_EXACT_USERNAME ? "checked" : "" ?> name="searchType" value="<?php echo Linked_account_model::SEARCH_TYPE_EXACT_USERNAME ?>" />
					<label class="control-label"><?=lang('Exact Username');?></label>
					<input type="radio" <?php echo $this->session->userdata('searchType') == Linked_account_model::SEARCH_TYPE_SIMILAR_USERNAME ? "checked" : "" ?> name="searchType" value="<?php echo Linked_account_model::SEARCH_TYPE_SIMILAR_USERNAME ?>" />
					<label class="control-label"><?=lang('Similar Username');?></label>
	            	<div class="clearfix"></div>
	               	<input type="text" name="username" id="usernameInputTxt" class="form-control" required value="<?php echo $this->session->userdata('linkAccountUsername')?>" />
				</div>
				<div class="form-group">
					<label for="tag" class="control-label"><?=lang('Linked Date');?></label>
					<br/>
					<div class="input-group">
						<input type="date" value="<?php echo $this->session->userdata('linkDatetime')?>" name="linkDatetime" id="linkDatetimeInputTxt" class="form-control">
						<span class="input-group-addon input-sm">
		                    <input type="checkbox" name="linkDateTimeCbx" id="linkDateTimeCbx" value="1" class="user-success" <?php echo $this->session->userdata('linkDatetime') ? "checked" : ""?>>
		                </span>
					</div>
				</div>
				<div class="clearfix"></div>
				<br/>
				<input type="reset" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>">
				<input type="submit" value="<?=lang('lang.search');?>" id="search_main"class="btn btn-primary btn-sm">
			</form>
		</div>
	</div>
</div>
<!--end of main-->

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt pull-left">
					<i class="glyphicon glyphicon-link"></i> <?php echo lang('Linked Account Query Result');?>
				</h4>

				<div class="pull-right">
					<?php if(!empty($players)){ ?>
							<input type="button" value="<?=lang('lang.exporttitle')?>" class="btn btn-info btn-sm export_excel">
					<?php } ?>
				</div>
				<span class="clearfix"></span>

			</div>

			<div class="panel-body" id="player_panel_body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="myTable">
						<thead>
							<tr>
								<th><?php echo lang('From');?></th>
								<th><?php echo lang('Status (From)');?></th>
								<th><?php echo lang('Linked Accounts Count');?></th>
								<th><?php echo lang('Linked Accounts');?></th>
							</tr>
						</thead>

							<?php
								if(!empty($players)){
									foreach ($players as $key) {
										if(count($key['linked_accounts'])){ ?>
									<tr>
										<td><a target="_blank" href="<?php echo site_url('player_management/userInformation/'.$key['playerId'])?>"><?php echo $key['username'] ?></a></td>
										<!-- <td><?php echo $key['blocked'] ? lang('Blocked') : "Active" ?></td> -->
										<td>
											<?php if($key['blocked'] == 1){?>
												<span class="text-danger"><?php echo lang('Blocked'); ?></span>
											<?php } elseif($key['blocked'] == 5){ ?>
												<span class="text-warning"><?php echo lang('Suspended'); ?></span>
											<?php } else { ?>
												<span class="text-success"><?php echo lang('lang.active'); ?></span>
											<?php } ?>
										</td>
										<td><?php echo count($key['linked_accounts']) ?></a></td>
										<td>
											<?php
												if(!empty($key['linked_accounts'])){ ?>
													<table class="table table-striped table-hover" >
														<th><?php echo lang('Action');?></th>
														<th><?php echo lang('Linked To');?></th>
														<th><?php echo lang('Linked Date');?></th>
														<th><?php echo lang('Remark');?></th>
														<th><?php echo lang('Status(To)');?></th>

														<?php
															if(!empty($key['linked_accounts'])){
																foreach ($key['linked_accounts'] as $la) { ?>
																<tr>
																	<td><?php echo $la['action_edit_remarks']." ".$la['action_delete_remarks']; ?></td>
																	<td><a target="_blank" href="<?php echo site_url('player_management/userInformation/'.$la['playerId'])?>"><?php echo $la['username']; ?></a></td>
																	<td><?php echo $la['link_datetime']; ?></td>
																	<td><?php echo $la['remarks']; ?></td>
																	<!-- <td><?php echo $la['blocked'] ? lang('Blocked') : "Active"; ?></td> -->
																	<td>
																		<?php if($la['blocked'] == 1){?>
																			<span class="text-danger"><?php echo lang('Blocked'); ?></span>
																		<?php } elseif($la['blocked'] == 5){ ?>
																			<span class="text-warning"><?php echo lang('Suspended'); ?></span>
																		<?php } else { ?>
																			<span class="text-success"><?php echo lang('lang.active'); ?></span>
																		<?php } ?>
																	</td>
																</tr>
														<?php }
															}else{ ?>
															<tr>
																<td colspan="5"><?php echo lang("No Linked Account") ?></td>
															</tr>
														<?php } ?>
													</table>
												<?php }
												?>
										</td>
									</tr>
								<?php }
									 }
									} ?>
						</tr>
					</table>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>

<!-- Linked Account Edit Remarks Modal Start -->
<?php $this->load->view('player_management/linked_account/linked_account_modals'); ?>
<!-- Linked Account Edit Remarks Modal End -->

<!-- Linked Account Common Script Start -->
<?php $this->load->view('player_management/linked_account/linked_account_script'); ?>
<!-- Linked Account Common Script End -->