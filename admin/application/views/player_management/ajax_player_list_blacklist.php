<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('player.01'); ?></th>
			<?= $this->session->userdata('black_name') == "checked" || !$this->session->userdata('black_name') ? '<th id="visible">'. lang('player.40') .'</th>' : '' ?>
			<?= $this->session->userdata('black_level') == "checked" || !$this->session->userdata('black_level') ? '<th>'. lang('player.39') .'</th>' : '' ?>
			<?= $this->session->userdata('black_email') == "checked" || !$this->session->userdata('black_email') ? '<th id="visible">'. lang('player.06') .'</th>' : '' ?>
			<?= $this->session->userdata('black_country') == "checked" || !$this->session->userdata('black_country') ? '<th id="visible">'. lang('player.20') .'</th>' : '' ?>
			<?= $this->session->userdata('black_tag') == "checked" || !$this->session->userdata('black_tag') ? '<th id="visible">'. lang('player.41') .'</th>' : '' ?>
			<?= $this->session->userdata('black_last_login_time') == "checked" || !$this->session->userdata('black_last_login_time') ? '<th id="visible">'. lang('player.42') .'</th>' : '' ?>
			<?= $this->session->userdata('black_registered_on') == "checked" || !$this->session->userdata('black_registered_on') ? '<th>'. lang('player.43') .'</th>' : '' ?>
			<?= $this->session->userdata('black_status_col') == "checked" || !$this->session->userdata('black_status_col') ? '<th id="visible">'. lang('lang.status') .'</th>' : '' ?>
			<!-- <th>Action</th> -->
		</tr>
	</thead>

	<tbody>
		<?php
			if(!empty($players)) {
				foreach($players as $players) {
					$name = $players['lastName'] . " " . $players['firstName'];
		?>
					<tr>
						<td><?= $players['username'] ?></td>

						<?php if($this->session->userdata('black_name') == "checked" || !$this->session->userdata('black_name')) { ?>
							<td id="visible"><?= ($players['lastName'] == '') && ($players['firstName'] == '') ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $name ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_level') == "checked" || !$this->session->userdata('black_level')) { ?>
							<td><?= $players['groupName'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['groupName'].' '.$players['vipLevel'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_email') == "checked" || !$this->session->userdata('black_email')) { ?>
							<td id="visible"><?= $players['email'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['email'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_country') == "checked" || !$this->session->userdata('black_country')) { ?>
							<td id="visible"><?= $players['country'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['country'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_tag') == "checked" || !$this->session->userdata('tag')) { ?>
							<td id="visible"><?= $players['tagName'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['tagName'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_last_login_time') == "checked" || !$this->session->userdata('black_last_login_time')) { ?>
							<td id="visible"><?= $players['lastLoginTime'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['lastLoginTime'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_registered_on') == "checked" || !$this->session->userdata('black_registered_on')) { ?>
							<td><?= $players['createdOn'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $players['createdOn'] ?></td>
						<?php } ?>

						<?php if($this->session->userdata('black_status_col') == "checked" || !$this->session->userdata('black_status_col')) { ?>
							<td id="visible"><?= $players['status'] == 0 ? lang('player.14') : lang('player.15') ?></td>
						<?php } ?>
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

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>