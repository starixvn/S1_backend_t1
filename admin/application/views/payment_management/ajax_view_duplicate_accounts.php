<div class="col-md-12">
	<form>
		<fieldset>
			<legend><?=lang('pay.duplicateAccountList')?></legend>
			<div class="col-md-12 ">
				<div class="table-responsive">
					<div class="label-rating" style="float:left;margin-right:10px;">
						<span class="label label-success" style="font-size:12px;">None</span>
						<span class="label label-warning" style="font-size:12px;">Similar</span>
						<span class="label label-danger" style="font-size:12px;">Exact</span>
					</div>

					<table class="table table-hover" id="duplicateTable" style="margin: 0px 0 0 0; width: 100%;">
					    <thead>
					        <th></th>
					        <th>ID</th>
							<th class="col-md-1"> <?=lang('pay.username');?> (Similar: <?= $this->duplicate_account->getRating('username', 'rate_similar'); ?>) </th>
							<th class="col-md-1"> <?=lang('pay.password');?> (Exact: <?= $this->duplicate_account->getRating('password', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('pay.realname');?> (Exact: <?= $this->duplicate_account->getRating('realname', 'rate_exact') ?> / Similar: <?= $this->duplicate_account->getRating('realname', 'rate_similar'); ?>) </th>
							<th class="col-md-1"> <?=lang('pay.mobile');?> (Exact: <?= $this->duplicate_account->getRating('phone', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('pay.email');?> (Similar: <?= $this->duplicate_account->getRating('email', 'rate_similar'); ?>) </th>
							<th class="col-md-1"> <?=lang('pay.city');?> (Exact: <?= $this->duplicate_account->getRating('city', 'rate_exact'); ?>) </th>
							<!-- <th class="col-md-1"> <?=lang('pay.country');?> (Exact: <?= $this->duplicate_account->getRating('country', 'rate_exact'); ?>) </th> -->
							<th class="col-md-1"> <?=lang('pay.address');?> (Exact: <?= $this->duplicate_account->getRating('address', 'rate_exact'); ?>) </th>
							<!-- <th class="col-md-1"> <?=lang('pay.cookies');?> (Exact: <?= $this->duplicate_account->getRating('cookie', 'rate_exact'); ?>) </th> -->
							<!-- <th class="col-md-1"> <?=lang('pay.referrer');?> </th> -->
							<!-- <th class="col-md-1"> <?=lang('pay.useragent');?> (Exact: <?= $this->duplicate_account->getRating('user_agent', 'rate_exact'); ?>) </th> -->
							<th class="col-md-1"> <?=lang('sys.regIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.lastLoginIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.dpstIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.withdrwIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.transMainToSubIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.transSubToMainIP');?> IP (Exact: <?= $this->duplicate_account->getRating('ip', 'rate_exact'); ?>) </th>
							<th class="col-md-1"> <?=lang('sys.totalRate');?></th>
					    </thead>
                        <?php $duplicate_accounts = array_diff_key($duplicate_accounts, array('0' => array())); // a item with '0' as ID,hard to find the couse, so remove it first. ?>
					    <tbody>
					        <?php
					            $duplicate_accounts=array_slice($duplicate_accounts, 0, $this->utils->getConfig('max_size_duplicate_accounts'));
					        	if(!empty($duplicate_accounts)) { ?>
					            <?php
					            	foreach ($duplicate_accounts as $key1 => $value1) {

					            		$player = $this->duplicate_account->getPlayerDetails($key1);
					            		$norecord = "<i style='color:#737373;'>" . lang('lang.norecord') . "</i>";
					            		if(isset($player['firstName'])){$fullname = $player['firstName'] . " " . $player['lastName'];}
					            		else{$fullname = null;}
					            		$totalRate = 0;
					            		$basic_info = array('realname','username','password','mobile','email','city','country','address');

					            		foreach ($basic_info as $bi_value) {
					            			$totalRate += isset($value1[$bi_value]) ? $value1[$bi_value][1] : 0;
					            		}
				            	?>
						                <tr>
						                	<td></td>
						                	<td><?= $key1 ?></td>
						                    <td><?= isset($value1['username']) ? '<span style="color:#f0ad4e;">' . $value1['username'][0] . "</span><b> (" . $value1['username'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['username']) ? $norecord : $player['username']) . '</span>' ?></td>
						                    <td><?= isset($value1['password']) ? '<span style="color:#d9534f;">' . lang('player.56') . "</span><b> (" . $value1['password'][1] . ") </b>" : '<span style="color:#5cb85c;">' . lang('player.56') . '</span>' ?></td>
						                    <td><?= isset($value1['realname']) ? '<span style="color:' . (($value1['realname'][2] == 'rate_exact') ? '#d9534f' : '#f0ad4e') . '">' . $value1['realname'][0] . "</span><b> (" . $value1['realname'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (!empty($player['firstName']) || !empty($player['lastName']) ? $fullname : $norecord) . '</span>'; ?></td>
						                    <td><?= isset($value1['mobile']) ? '<span style="color:#d9534f;">' . $value1['mobile'][0] . "</span><b> (" . $value1['mobile'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['contactNumber']) ? $norecord : $player['contactNumber']) . '</span>' ?></td>
						                    <td><?= isset($value1['email']) ? '<span style="color:#f0ad4e;">' . $value1['email'][0] . "</span><b> (" . $value1['email'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['email']) ? $norecord : $player['email']) . '</span>' ?></td>
						                    <td><?= isset($value1['city']) ? '<span style="color:#d9534f;">' . $value1['city'][0] . "</span><b> (" . $value1['city'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['city']) ? $norecord : $player['city']) . '</span>' ?></td>
						                    <!-- <td><?= isset($value1['country']) ? '<span style="color:#d9534f;">' . $value1['country'][0] . "</span><b> (" . $value1['country'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['country']) ? $norecord : $player['country']) . '</span>' ?></td> -->
						                    <td><?= isset($value1['address']) ? '<span style="color:#d9534f;">' . $value1['address'][0] . "</span><b> (" . $value1['address'][1] . ") </b>" : '<span style="color:#5cb85c;">' . (empty($player['address']) ? $norecord : $player['address']) . '</span>' ?></td>

						              		<?php
						                    	$ip = '';
						                    	$get_ip = array();
						                    	$get_rating = 0;

						                    	if (!empty($value1['ip'])) {
					                    			foreach ($value1['ip'] as $ua_key => $ua_value) {
				                    					$get_ip = $this->duplicate_account->getHTTPRequestById($value1['ip'][$ua_key][0]);
				                    					$ip[$ua_key] = $get_ip['ip'];
				                    					$get_rating = $value1['ip'][$ua_key][1];
				                    				}
					                    		}
						                    ?>

						                    <td>
						                    	<?php
						                    		if (isset($ip[1])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[1] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 1);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                	<td>
						                		<?php
						                    		if (isset($ip[2])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[2] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 2);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                    </td>
						                	<td>
						                		<?php
						                    		if (isset($ip[3])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[3] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 3);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                	</td>
						                	<td>
						                		<?php
						                    		if (isset($ip[4])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[4] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 4);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                	</td>
						                	<td>
						                		<?php
						                    		if (isset($ip[5])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[5] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 5);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                	</td>
						                	<td>
						                		<?php
						                    		if (isset($ip[6])) {
						                    			echo '<span style="color:#d9534f;">' . $ip[6] . '</span> <b>(' . $get_rating . ')</b>';
						                    			$totalRate += $get_rating;
						                    		} else {
						                    			$regIP = $this->duplicate_account->getHTTPRequestByPlayerId($key1, 6);
						                    			echo isset($regIP[0]['ip']) ? '<span style="color:#5cb85c;">' . $regIP[0]['ip'] . '</span>' : $norecord;
						                    		}
						                    	?>
						                	</td>
						                	<td><?= $totalRate; ?></td>
						                </tr>
					        <?php
					                }
					            }
					        ?>
					    </tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#duplicateTable').DataTable({
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
            "order": [ 1, 'asc' ],
            "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.label-rating').prependTo($('.top'));
            }
        });

        $("[data-toggle=popover]").popover({html:true});
    } );
</script>