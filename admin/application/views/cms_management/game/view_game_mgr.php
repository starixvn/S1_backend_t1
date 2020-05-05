<!-- Sort Option -->
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary
              " style="margin-bottom:10px;">
            <div class="panel-heading custom-ph">
		        <h4 class="panel-title custom-pt" style="font-family:calibri;font-size:1.4em;">
		        	<i class="icon-sort-amount-desc" id="hide_main_up"></i> <?= lang('lang.sort'); ?>
		        	<a href="#main" 
              id="hide_main" class="btn btn-default btn-sm pull-right hide_sortby">
		            	<i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
		            </a>
		        </h4>
		    </div>
            <div class="panel-body sortby_panel_body main_panel_body" style="display:none;padding-bottom:0;">
            	<form class="form-horizontal" action="<?= BASEURL . 'cms_management/sortGame' ?>" method="post" role="form">                
                     <div class="form-group">
                        <div class="col-md-2">
							<label class="control-label"><?= lang('cms.gameprovider'); ?></label>
							<select class="form-control input-sm" name="gameProvider" id="gameProvider">                                    
								<option value="1" <?= $this->session->userdata('gameProvider') == 'activated' ? 'selected' : ''?>>PT</option>
								<option value="2" <?= $this->session->userdata('gameProvider') == 'nonactivated' ? 'selected' : ''?>>AG</option>                                                                                                                     
							</select>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label"><?= lang('cms.gametype'); ?></label>
							<select class="form-control input-sm" name="gameType" id="gameType">         
								<option value="" <?= $this->session->userdata('gameType') == '' ? 'selected' : ''?>>-- <?= lang('lang.selectall'); ?> --</option>                           
								<option value="video pokers" <?= $this->session->userdata('gameType') == 'video pokers' ? 'selected' : ''?>><?= lang('cms.videopokers'); ?></option>
								<option value="table and card games" <?= $this->session->userdata('gameType') == 'table and card games' ? 'selected' : ''?>><?= lang('cms.tablegames'); ?></option>                                                                                                                     
								<option value="live games" <?= $this->session->userdata('gameType') == 'live games' ? 'selected' : ''?>><?= lang('cms.livegames'); ?></option>
								<option value="slot machines" <?= $this->session->userdata('gameType') == 'slot machines' ? 'selected' : ''?>><?= lang('cms.slot'); ?></option>
								<option value="arcade games" <?= $this->session->userdata('gameType') == 'arcade games' ? 'selected' : ''?>><?= lang('cms.arcade'); ?></option>
							</select>
                        </div> 
                        <div class="col-md-3">
							<label class="control-label"><?= lang('cms.progressive'); ?></label>
							<select class="form-control input-sm" name="progressiveType" id="progressiveType">                                    
								<option value="" <?= $this->session->userdata('progressiveType') == '' ? 'selected' : ''?>>-- <?= lang('lang.selectall'); ?> --</option>
								<option value="dollar ball progressive" <?= $this->session->userdata('progressiveType') == 'dollar ball progressive' ? 'selected' : ''?>><?= lang('cms.dollar'); ?></option>                                                                                                                     
								<option value="marvel jackpot" <?= $this->session->userdata('progressiveType') == 'marvel jackpot' ? 'selected' : ''?>><?= lang('cms.marvel'); ?></option>
								<option value="progressive jackpot" <?= $this->session->userdata('progressiveType') == 'progressive jackpot' ? 'selected' : ''?>><?= lang('cms.progjackpot'); ?></option>
								<option value="jackpot" <?= $this->session->userdata('progressiveType') == 'jackpot' ? 'selected' : ''?>><?= lang('cms.jackpot'); ?></option>
							</select>
                        </div> 
                        <div class="col-md-2">
							<label class="control-label"><?= lang('cms.branded'); ?></label>                    
							<select class="form-control input-sm" name="brandedGame" id="brandedGame"> 
								<option value="" <?= $this->session->userdata('brandedGame') == '' ? 'selected' : ''?>>-- <?= lang('lang.selectall'); ?> --</option>
								<option value="0" <?= $this->session->userdata('brandedGame') == '0' ? 'selected' : ''?>><?= lang('cms.nonbranded'); ?></option>
								<option value="1" <?= $this->session->userdata('brandedGame') == '1' ? 'selected' : ''?>><?= lang('cms.branded'); ?></option>                                                                                                                     
							</select>
                        </div> 
                        <div class="col-md-2">
                        	<label class="control-label">&nbsp;</label>                
							<select class="form-control input-sm" name="activeGame" id="activeGame"> 
								<option value="" <?= $this->session->userdata('activeGame') == '' ? 'selected' : ''?>>-- <?= lang('lang.selectall'); ?> --</option>
								<option value="activated" <?= $this->session->userdata('activeGame') == 'activated' ? 'selected' : ''?>><?= lang('cms.activatedgame'); ?></option>
								<option value="deactivated" <?= $this->session->userdata('activeGame') == 'deactivated' ? 'selected' : ''?>><?= lang('cms.deactivatedgame'); ?></option>                                                                                                                     
							</select>
                        </div>
                        <div class="col-md-1" style="text-align:center;padding-top:23px;">
	                        <input class="btn btn-sm btn-info" type="submit" value="<?= lang('lang.search'); ?>" />
	                    </div>
                    </div>
                </form>
            </div>
       	</div>
    </div>
</div>
<!--end of Sort Information-->

<!-- start game list -->
<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title custom-pt">
			<i class="icon-dice"></i> <?= lang('cms.gameslist'); ?>
			<span class="choosenDateRange">&nbsp;<?= isset($choosenDateRange) ? ($choosenDateRange) : '' ?></span>
		</h4>
	</div>
	
	<!-- start data table -->
	<div class="panel-body" id="player_panel_body">
		<div id="paymentList" class="table-responsive">
			<table class="table table-striped table-hover" id="myTable" style="width:100%;">
				<thead>
					<tr>
						<th></th>
						<th><?= lang('cms.gameprovider'); ?></th>
						<th><?= lang('cms.gamename'); ?></th>
						<th><?= lang('cms.gametype'); ?></th>
						<th><?= lang('cms.progressive'); ?></th>
						<th><?= lang('cms.branded'); ?></th>
						<th><?= lang('cms.gamecode'); ?></th>
						<th><?= lang('cms.flash'); ?></th>
						<th><?= lang('cms.downloadclient'); ?></th>
						<th><?= lang('cms.mobile'); ?></th>
						<th><?= lang('lang.status'); ?></th>
						<th><?= lang('lang.action'); ?></th>
					</tr>
				</thead>

				<tbody>
					<?php 
						if(!empty($games)) {
							foreach($games as $game) {
					?>										
									<tr>
										<td></td>																								
										<td><?= $game['gameTypeId'] == 1 ? 'PT' : 'AG' ?></td>
										<td><?= $game['gameName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $game['gameName'] ?></td>
										<td><?= $game['gameType'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $game['gameType'] ?></td>
										<td><?php
												if($game['gameTypeId'] == 1){
													echo $game['progressive'] == '' ? '<i class="help-block">'. lang('cms.nonprogressive') .'<i/>' : $game['progressive'];
												}elseif($game['gameTypeId'] == 2){
												 	echo '<i class="help-block">'. lang('lang.norecyet') .'</i>';
												} 	 ?></td>
										<td><?php
												if($game['gameTypeId'] == 1){
												 	echo $game['branded'] == 0 ? lang("lang.no") : lang("lang.yes");
												 }elseif($game['gameTypeId'] == 2){
												 	echo '<i class="help-block">'. lang('lang.norecyet') .'</i>';
												 } ?></td>
												
										<td><?= $game['gameCode'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $game['gameCode'] ?></td>
										<td><?php
												if($game['gameTypeId'] == 1){
													echo $game['flashGamePlatform'] == 0 ? lang("lang.no") : lang("lang.yes");
												}elseif($game['gameTypeId'] == 2){
												 	echo '<i class="help-block">'. lang('lang.norecyet') .'</i>';
												} ?></td>
										<td><?php
												if($game['gameTypeId'] == 1){
													echo $game['dlcGamePlatform'] == 0 ? lang("lang.no") : lang("lang.yes");
												}elseif($game['gameTypeId'] == 2){
												 	echo '<i class="help-block">'. lang('lang.norecyet') .'</i>';
												}?></td>
										<td><?php
												if($game['gameTypeId'] == 1){
													echo $game['mobileGamePlatform'] == 0 ? lang("lang.no") : lang("lang.yes");
												}elseif($game['gameTypeId'] == 2){
												 	echo '<i class="help-block">'. lang('lang.norecyet') .'</i>';
												}	 ?></td>
										<td><?= $game['status'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : $game['status'] ?></td>
										<td>	
											<div class="btn-group" role="group">
												<?php  if($game['status'] == 'deactivated'){ ?>
													<a class="btn promoActionBtn" href="<?= BASEURL . 'cms_management/activateGame/'.$game['cmsGameId'] ?>">
														<span class="btn-sm btn-success review-btn">
															<?= lang('lang.activate'); ?>
														</span>
													</a>&nbsp;
												<?php }elseif($game['status'] == 'activated'){ ?>
													<a class="btn promoActionBtn" href="<?= BASEURL . 'cms_management/deactivateGame/'.$game['cmsGameId'] ?>">
														<span class="btn-sm btn-danger review-btn">
															<?= lang('lang.deactivate'); ?>
														</span>
													</a>&nbsp;
												<?php } ?>

												<a href="#" data-toggle="modal" data-target="#category_<?= $game['cmsGameId']?>" class="btn promoActionBtn">
													<span class="btn-sm btn-warning review-btn">
														<?= lang('player.08'); ?>
													</span>
												</a>
														
												<div class="modal fade bs-example-modal-sm" id="category_<?= $game['cmsGameId']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-sm">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																<h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?= lang('cms.categgame'); ?>: <?= $game['gameName'] ?></h4>
																<i>(<?= lang('cms.note'); ?>)</i>
															</div>

															<form action="<?= BASEURL . 'cms_management/gameCategory/' . $game['cmsGameId'] ?>" method="POST">
																<div class="modal-body">
																	<?php foreach ($level as $levelvalue) { ?>
																		<input type="checkbox" name="category[]" value="<?= $levelvalue['vipsettingcashbackruleId'] ?>" <?= ($this->cms_model->checkGameCategory($game['cmsGameId'], $levelvalue['vipsettingcashbackruleId'])) ? 'checked':''?> /> <?= $levelvalue['groupName'] . " " . $levelvalue['vipLevel'] ?><br/>
																	<?php } ?>
																</div>

																<div class="modal-footer">
																	<input type="submit" class="btn btn-primary" value="<?= lang('lang.save'); ?>"/>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
					<?php }
						} else { 
					} ?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- end data table -->
	<div class="panel-footer"></div>
</div>
<!-- end promostions list -->

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
    });
</script>
