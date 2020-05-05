<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
			<th><?= lang('player.01'); ?></th>
			<?= $this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? '<th id="visible">'. lang("player.40") .'</th>' : '' ?>
			<?= $this->session->userdata('level') == "checked" || !$this->session->userdata('level') ? '<th>'. lang("player.39") .'</th>' : '' ?>
			<?= $this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? '<th id="visible">'. lang("player.06") .'</th>' : '' ?>
			<?= $this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? '<th id="visible">'. lang("player.20") .'</th>' : '' ?>
			<?= $this->session->userdata('tag') == "checked" || !$this->session->userdata('tag') ? '<th id="visible">'. lang("player.41") .'</th>' : '' ?>
			<?= $this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time') ? '<th id="visible">'. lang("player.42") .'</th>' : '' ?>
			<?= $this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? '<th>'. lang("player.43") .'</th>' : '' ?>
			<?= $this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by') ? '<th id="visible">'. lang("player.67") .'</th>' : '' ?>
			<?= $this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? '<th id="visible">'. lang("lang.status") .'</th>' : '' ?>
			<th><?= lang("lang.action"); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			if(!empty($players)) {
				foreach($players as $players) {
					$name = $players['lastName'] . " " . $players['firstName'];
					if($players['status'] == 1 || $players['status'] == 2) {
		?>
						<tr class="danger">
		<?php
					} else {
		?>
						<tr>
		<?php
					}
		?>
							<td><input type="checkbox" class="checkWhite" id="<?= $players['playerId']?>" name="players[]" value="<?= $players['playerId']?>" onclick="uncheckAll(this.id)"></td>
							<td><a href="<?= BASEURL . 'player_management/userInformation/' . $players['playerId']?>"><?= $players['username'] ?></a></td>

							<?php if($this->session->userdata('name') == "checked" || !$this->session->userdata('name')) { ?>
								<td id="visible"><?= ($players['lastName'] == '') && ($players['firstName'] == '') ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $name ?></td>
							<?php } ?>

							<?php if($this->session->userdata('level') == "checked" || !$this->session->userdata('level')) { ?>
								<td><?= $players['groupName'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['groupName'].' '.$players['vipLevel'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('email') == "checked" || !$this->session->userdata('email')) { ?>
								<td id="visible"><?= $players['email'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['email'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('country') == "checked" || !$this->session->userdata('country')) { ?>
								<td id="visible"><?= $players['country'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['country'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('tag') == "checked" || !$this->session->userdata('tag')) { ?>
								<td id="visible"><?= $players['tagName'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['tagName'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('last_login_time') == "checked" || !$this->session->userdata('last_login_time')) { ?>
								<td id="visible"><?= $players['lastLoginTime'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['lastLoginTime'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on')) { ?>
								<td><?= $players['createdOn'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['createdOn'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('registered_by') == "checked" || !$this->session->userdata('registered_by')) { ?>
								<td id="visible"><?= $players['registered_by'] == 'website' ? lang("player.68") : lang("player.69") ?></td>
							<?php } ?>

							<?php if($this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col')) { ?>
								<td id="visible"><?= $players['status'] == 0 ? lang("player.14") : lang("player.15") ?></td>
							<?php } ?>

							<td>
								<!-- <a href="#showDetails" data-toggle="tooltip" class="details" onclick="viewPlayer(<?= $players['playerId']?>, 'overview');"><span class="glyphicon glyphicon-zoom-in"></span></a>-->
								<?php if($this->permissions->checkPermissions('edit_player_vip_level')) { ?>
									<a href="#showPlayerLevel" data-toggle="tooltip" title="<?= lang('tool.pm01'); ?>" class="playerlevel" onclick="viewPlayer(<?= $players['playerId']?>, 'adjustplayerlevel');"><span class="glyphicon glyphicon-edit"></span></a>
								<?php } ?>
								<?php if($this->permissions->checkPermissions('tag_player')) { ?>
									<a href="#tags" data-toggle="tooltip" title="<?= lang('tool.pm04'); ?>" class="tags" onclick="viewPlayerWithCurrentPage(<?= $players['playerId']?>, 'playerTag', 'playerlist');"><span class="glyphicon glyphicon-tag"></span></a>
								<?php } ?>

								<?php if($this->permissions->checkPermissions('lock_player')) { ?>
									<!-- <a href="<?= BASEURL . 'player_management/lockPlayer/' .  $players['playerId'] . '/' . $players['status'] . '/playerlist'?>" data-toggle="tooltip" class="lock"><span class="glyphicon glyphicon-lock"></span></a> -->
									<a href="#lockPlayer" data-toggle="tooltip" title="<?= lang('tool.pm02'); ?>" class="lock" onclick="viewPlayerWithCurrentPage(<?= $players['playerId']?>, 'lockedPlayer', 'playerlist');"><span class="glyphicon glyphicon-lock"></span></a>
								<?php } ?>
								<?php if($this->permissions->checkPermissions('block_player')) { ?>
									<!-- <a href="<?= BASEURL . 'player_management/blockPlayer/' .  $players['playerId'] . '/' . $players['blocked'] . '/playerlist'?>" data-toggle="tooltip" class="block"><span class="glyphicon glyphicon-ban-circle"></span></a> -->
									<a href="#blockPlayer" data-toggle="tooltip" title="<?= lang('tool.pm03'); ?>" class="block" onclick="viewPlayerWithCurrentPage(<?= $players['playerId']?>, 'blockPlayerInGame', 'playerlist');"><span class="glyphicon glyphicon-ban-circle"></span></a>
								<?php } ?>
							</td>
						</tr>
		<?php
				}
			} else {
		?>
						<tr>
                            <td colspan="10" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                        </tr>
        <?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>