<style type="text/css">
  .nav-tabs li a {font-size:13px;}
</style>
<div class="row" id="signup_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"> <a href="#signup"
              id="hide_sign_up" class="btn btn-default btn-sm"> <i class="glyphicon glyphicon-chevron-up" id="hide_si_up"></i></a> &nbsp;<strong><?=lang('player.ui03');?></strong></h4>
            </div>

            <div class="panel-body" id="signupinfo_panel_body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" style="margin-bottom:0;">
                                <tr>
                                    <th class="active col-md-2"><?=lang('player.01');?></th>
                                    <td class="col-md-4"><?=$player['username']?></td>
                                    <th class="active"><?=lang('player.18');?></th>
                                    <td><?=$player['invitationCode']?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?=lang('player.ui09');?></th>
                                    <td><?=($player['typeOfPlayer'] == 'real') ? lang('player.ui12') : lang('player.ui13')?></td>
                                    <th class="active"><?=lang('player.70');?></th>
                                    <td>
                                        <?php if (empty($referred_by_code)) {
	echo lang('lang.norecord');
} else {?>
                                            <a href="<?=site_url('player_management/userInformation/' . $referred_by_id)?>" data-toggle="tooltip" title="" data-original-title="<?=lang('player.ur04');?>"><?=$referred_by_code?></a>
                                        <?php }
?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.38');?></th>
                                    <td class="col-md-4"><?=$player['playerCreatedOn']?></td>
                                    <th class="active"><?=lang('player.86');?></th>
                                    <td>
                                        <?php if ($referral_count != 0) {?>
                                            <a href="<?=site_url('player_management/friendReferral/' . $player['playerId'])?>">
                                                <?=$referral_count?>
                                            </a>
                                        <?php } else {
	echo $referral_count;
}
?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="active"><?=lang('player.42');?></th>
                                    <td><?=$player['lastLoginTime']?></td>
                                    <th class="active col-md-2"><?=lang('player.24');?></th>
                                    <td class="col-md-4"><?=(empty($affiliate)) ? lang('lang.norecord') : $affiliate?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?=lang('player.85');?></th>
                                    <td><?=$player['lastLogoutTime']?></td>
                                    <th class="active"><?=lang('player.ui10');?></th>
                                    <td><?=$player['registrationIP'] == '' ? lang('lang.norecord') : $player['registrationIP']?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?="Website " . lang('player.ui11');?></th>
                                    <td class="col-md-4">
                                        <?=$player['online'] == '0' ? lang('lang.yes') : lang('lang.no')?>
                                    </td>
                                    <th class="active"><?=lang('lang.status');?></th>
                                    <td><?=($player['status'] == 0) ? lang('player.14') : lang('player.15')?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="col-md-7" id="player_details" style="display: none;">

    </div>
</div>

<div class="row" id="account_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#balance_info"
              id="hide_balance_info" class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_bi_up"></i>
                    </a>
                    &nbsp;<strong><?=lang('player.ui05');?></strong> (<?=$playeraccount['currency']?>)

                    <a href="#refresh_balance" data-toggle="modal" class="btn btn-sm btn-default pull-right" onclick="refreshAccountInfo(<?=$player['playerId']?>);">
                        <i class="glyphicon glyphicon-refresh"></i>
                    </a>
                </h4>
            </div>

            <div class="panel-body" id="balance_panel_body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" style="margin-bottom:0;">
                                <tr>
                                    <th class="active col-md-2"><?=lang('player.ui20');?></th>
                                    <td class="col-md-4"><?=$playeraccount['currency']?> <?=number_format($mainwallet['totalBalanceAmount'], 2) ? number_format($mainwallet['totalBalanceAmount'], 2) : '0.00'?></td>
                                    <th class="active col-md-2"><?=lang('player.ui14');?></th>
                                    <td><?=$total_deposits['totalNumberOfDeposit']?></td>
                                </tr>

                                <?php if (!empty($subwallet)) {
	?>
                                    <?php
$balance = $mainwallet['totalBalanceAmount'];
	foreach ($subwallet as $row) {
		$balance += $row['totalBalanceAmount'];
		?>
                                            <tr>
                                                <th class="active col-md-2"><?=strtoupper($row['game'])?> Wallet</th>
                                                <td><?=$playeraccount['currency']?> <?=number_format($row['totalBalanceAmount'], 2) ? number_format($row['totalBalanceAmount'], 2) : '0.00'?></td>
                                                <?php if ($row['game'] == 'PT') {?>
                                                    <th class="active col-md-2"><?=lang('player.ui15');?></th>
                                                    <td><?=$playeraccount['currency']?> <?=number_format($total_deposits['totalDeposit'], 2) ? number_format($total_deposits['totalDeposit'], 2) : '0.00'?></td>
                                                <?php }
		?>
                                                <?php if ($row['game'] == 'AG') {?>
                                                    <th class="active col-md-2"><?=lang('player.ui16');?></th>
                                                    <td><?=$playeraccount['currency']?> <?=$average_deposits ? $average_deposits : '0.00'?></td>
                                                <?php }
		?>
                                            </tr>
                                    <?php }
	?>
                                <?php }
?>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.ui21');?></th>
                                    <td><?=$playeraccount['currency']?> <?=number_format($balance, 2) ? number_format($balance, 2) : '0.00'?></td>
                                    <th class="active col-md-2"><?=lang('player.firstDepositDateTime')?></th>
                                    <td><?=$first_last_deposit['first'] ?: lang('lang.norecord')?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.ui22');?></th>
                                    <td><?=$playeraccount['currency']?> <?=number_format($total_deposit_bonus, 2) ? number_format($total_deposit_bonus, 2) : '0.00'?></td>
                                    <th class="active col-md-2"><?=lang('player.lastDepositDateTime')?></th>
                                    <td><?=$first_last_deposit['last'] ?: lang('lang.norecord')?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.ui23');?></th>
                                    <td><?=$playeraccount['currency']?> <?=number_format($total_cashback_bonus, 2) ? number_format($total_cashback_bonus, 2) : '0.00'?></td>
                                    <th class="active col-md-2"><?=lang('player.ui17');?></th>
                                    <td><?=$total_withdrawal['totalNumberOfWithdrawal']?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.ui24');?></th>
                                    <td><?=$playeraccount['currency']?> <?=number_format($total_referral_bonus, 2) ? number_format($total_referral_bonus, 2) : '0.00'?></td>
                                    <th class="active col-md-2"><?=lang('player.ui18');?></th>
                                    <td><?=$playeraccount['currency']?> <?=number_format($total_withdrawal['totalWithdrawal'], 2) ? number_format($total_withdrawal['totalWithdrawal'], 2) : '0.00'?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"></th>
                                    <td></td>
                                    <th class="active col-md-2"><?=lang('player.ui19');?></th>
                                    <td><?=$playeraccount['currency']?> <?=$average_withdrawals ? $average_withdrawals : '0.00'?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"></th>
                                    <td></td>
                                    <th class="active col-md-2"><?=lang('player.firstWithdrawDateTime')?></th>
                                    <td><?=$first_last_withdraw['first'] ?: lang('lang.norecord')?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"></th>
                                    <td></td>
                                    <th class="active col-md-2"><?=lang('player.lastWithdrawDateTime')?></th>
                                    <td><?=$first_last_withdraw['last'] ?: lang('lang.norecord')?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"></th>
                                    <td></td>
                                    <th class="active col-md-2"><?=lang('pay.curr')?></th>
                                    <td><?=$player['currency']?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7" id="player_details" style="display: none;">

    </div>
</div>


<div class="row" id="game_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"> <a href="#game_info"
              id="hide_game_info" class="btn btn-default btn-sm"> <i class="glyphicon glyphicon-chevron-up" id="hide_game_up"></i></a> &nbsp;<strong><?=lang('player.ui06');?></strong></h4>
            </div>

            <div class="panel-body" id="game_panel_body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" style="margin-bottom:0;">
                                <?php
$block_game = null;
if (!empty($blocked_games)) {
	foreach ($blocked_games as $key => $value) {
		if (empty($block_game)) {
			$block_game = $value['game'];
		} else {
			$block_game .= ", " . $value['game'];
		}
	}
}

$total_bets = 0;
$total_wins = 0;
$total_loss = 0;

foreach ($games as $key => $value) {
	$bets = 0;
	$wins = 0;
	$loss = 0;

	foreach ($api_details as $key => $api_value) {
		if ($value['gameId'] == $api_value['apitype']) {
			$total_bets += $api_value['bet'];
			$total_wins += $api_value['win'];
			$total_loss += $api_value['loss'];

			$bets += $api_value['bet'];
			$wins += $api_value['win'];
			$loss += $api_value['loss'];
		}
	}
	?>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui25');?> <?=$value['game']?> <?=lang('player.ui26');?></th>
                                        <td class="col-md-4"><?=($bets != 0) ? $bets : '0'?></td>
                                        <?php if ($value['game'] == 'PT') {?>
                                            <th class="active col-md-2"><?=lang('player.ui29');?></th>
                                            <td>
                                                <?php foreach ($games as $platform) {?>
                                                    <div class="label label-<?=$platform['status'] ? 'success' : 'danger'?>"><?=$platform['game']?></div>
                                                <?php }
		?>
                                            </td>
                                        <?php }
	?>
                                        <?php if ($value['game'] == 'AG') {?>
                                            <th class="active col-md-2"><?=lang('player.ui32');?></th>
                                            <td><?=$total_wins?></td>
                                        <?php }
	?>
                                    </tr>

                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui25');?> <?=$value['game']?> <?=lang('player.ui27');?></th>
                                        <td><?=($wins != 0) ? $wins : '0'?></td>
                                        <?php if ($value['game'] == 'PT') {?>
                                            <th class="active col-md-2"><?=lang('player.ui30');?></th>
                                            <td><?=$block_game == '' ? lang('lang.norecord') : $block_game?></td>
                                        <?php }
	?>
                                        <?php if ($value['game'] == 'AG') {?>
                                            <th class="active col-md-2"><?=lang('player.ui33');?></th>
                                            <td><?=$total_loss?></td>
                                        <?php }
	?>
                                    </tr>

                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui25');?> <?=$value['game']?> <?=lang('player.ui28');?></th>
                                        <td><?=($loss != 0) ? $loss : '0'?></td>
                                        <?php if ($value['game'] == 'PT') {?>
                                            <th class="active col-md-2"><?=lang('player.ui31');?></th>
                                            <td><?=number_format($total_bets, 2, '.', ',');?></td>
                                        <?php }
	?>
                                    </tr>
                                <?php }
?>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-7" id="player_details" style="display: none;">

    </div>
</div>

<div class="row" id="players_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"> <a href="#log_info"
              id="hide_log_info" class="btn btn-default btn-sm"> <i class="glyphicon glyphicon-chevron-up" id="hide_log_up"></i></a> &nbsp;<strong><?=lang('player.ui08');?></strong></h4>
            </div>

            <div class="panel panel-body" id="log_panel_body">

                <div class="row">
                    <div class="col-md-12" id="toggleView">
                        <div class="panel panel-primary">
                            <div class="panel-body" id="log_panel_body">
                                <div>
                                    <input type="button" class="btn btn-info btn-sm" id="transaction_log" value="<?=lang('pay.transhistory');?>" onclick="transactionHistory();">
                                    <input type="button" class="btn btn-info btn-sm" id="game_logs" value="<?=lang('player.ui48');?>" onclick="gamesHistory()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="changeable_table"></div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#bankInfoDepositTable').DataTable({
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

        $('#bankInfoWithdrawalTable').DataTable({
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

    function checkStatus(username,gameId,playerId) {
        $('#btn-checkStatus').text("<?=lang('text.loading')?>").prop('disabled', true);
        $.post("<?=BASEURL . 'player_management/is_online/'?>" + gameId + "/" + username + "/" + playerId, function(data) {
            $('#game-' + gameId).html(data);
        });
    }

</script>