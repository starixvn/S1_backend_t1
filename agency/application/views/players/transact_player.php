<div class="content-container">
	<br/>

	<!-- SIGNUP Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4><?=lang('lang.signupinfo');?></h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<table class="table">
					<tbody>
						<tr>
							<th style="width:25%;"><?=lang('reg.03');?></th>
							<td style="width:25%;"><?=$player_signup_info['username'];?></td>
							<th style="width:25%;"><?=lang('player.ui09');?></th>
							<td style="width:25%;"><?=$player_signup_info['typeOfPlayer'];?></td>
						</tr>
						<tr>
							<th style="width:25%;"><?=lang('aff.al24');?></th>
							<td style="width:25%;"><?=$player_signup_info['createdOn'];?></td>
							<th style="width:25%;"><?=lang('aff.ai40');?></th>
							<td style="width:25%;"><?=$player_signup_info['invitationCode'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- End of SIGNUP Information -->

    <!-- Account Information -->
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><?=lang('aff.action.balInfo');?></h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="info_panel_body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: right;"><?=lang('aff.action.mainwallet')?></th>
                                <?php foreach ($game_platforms as $game_platform) {?>
                                    <th style="text-align: right;"><?=$game_platform['system_code']?> <?=lang('pay.walltbal')?></th>
                                <?php } ?>
                                <th style="text-align: right;"><?=lang('pay.totalbal')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="right" class="<?=$playerDetails['main'] ? '' : 'text-muted'?>"><?=number_format($playerDetails['main'], 2)?></td>
                                <?php foreach ($game_platforms as $game_platform) {?>
                                    <td align="right" class="<?=$playerDetails[strtolower($game_platform['system_code'])] ? '' : 'text-muted'?>"><?=number_format($playerDetails[strtolower($game_platform['system_code'])], 2)?></td>
                                <?php } ?>
                                <td align="right" class="<?=$playerDetails['total'] ? '' : 'text-muted'?>"><strong><?=number_format($playerDetails['total'], 2)?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Account Information -->

	<!-- PERSONAL Information -->
	<div class="row">
		<div class="panel panel-info">
			<div class="panel-heading">
				<label><?=$transaction_title;?></label>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="code_panel_body">
				<div class="col-md-6 col-md-offset-3">
					<form method="post" action="<?=site_url('agency/processTransaction/' . $transaction_type)?>">
                        <?php if (!$is_mainwallet) { ?>
                            <label>
                                <?=lang('aff.action.subwallet');?>
                            </label>
                            <br/>
                            <select class="form-control" name="subwallet_id">
                                <?php foreach ($game_platforms as $game_platform) {?>
                                    <option value="<?=$game_platform['id']?>"><?=$game_platform['system_code']?></option>
                                <?php } ?>
                            </select>
                            <br/>
                        <?php } ?>
    					<label>
    						<?=lang('player.ut05');?>
    					</label>
    					<input type="number" min='0.01' step="any" name="transact_amount" required class="form-control" />
                        <input type="hidden" name="player_id" value="<?=$player_account_info['playerId']?>" />

    					<br/><br/>
                        <center>
    					    <input type="submit" value="<?php echo lang('Transfer'); ?>" class="btn btn-success btn-sm" />
                            <a href="#" class="btn btn-default btn-sm" onclick="history.back();"><?=lang("lang.cancel")?></a>
                        </center>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- End of PERSONAL Information -->
</div>