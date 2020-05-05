<table class="table table-striped table-hover" style="margin: 10px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
			<th><?= lang('aff.aj01'); ?></th>
			<?= $this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? '<th id="visible">'. lang('aff.aj02') .'</th>' : '' ?>
			<?= $this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? '<th id="visible">'. lang('aff.aj03') .'</th>' : '' ?>
			<?= $this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? '<th id="visible">'. lang('aff.aj04') .'</th>' : '' ?>
			<?= $this->session->userdata('tag') == "checked" || !$this->session->userdata('tag') ? '<th id="visible">'. lang('aff.aj05') .'</th>' : '' ?>
			<?= $this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? '<th>'. lang('aff.aj06') .'</th>' : '' ?>
			<?= $this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? '<th id="visible">'. lang('aff.aj07') .'</th>' : '' ?>
			<th><?= lang('aff.aj08'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			if(!empty($affiliates)) {
				foreach($affiliates as $affiliates) {
					$name = $affiliates['lastname'] . ", " . $affiliates['firstname'];
					if($affiliates['status'] == 1) {
		?>
						<tr class="danger">
							<td></td>
		<?php
					} else {
		?>
						<tr>
							<td><input type="checkbox" class="checkWhite" id="<?= $affiliates['affiliateId']?>" name="affiliates[]" value="<?= $affiliates['affiliateId']?>" onclick="uncheckAll(this.id)"></td>

		<?php
					}
		?>

							<td><a href="<?= BASEURL . 'affiliate_management/userInformation/' . $affiliates['affiliateId']?>"><?= $affiliates['username'] ?></a></td>

							<?php if($this->session->userdata('name') == "checked" || !$this->session->userdata('name')) { ?>
								<td id="visible"><?= ($affiliates['lastname'] == '') && ($affiliates['firstname'] == '') ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $name ?></td>
							<?php } ?>

							<?php if($this->session->userdata('email') == "checked" || !$this->session->userdata('email')) { ?>
								<td id="visible"><?= $affiliates['email'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $affiliates['email'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('country') == "checked" || !$this->session->userdata('country')) { ?>
								<td id="visible"><?= $affiliates['country'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $affiliates['country'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('tag') == "checked" || !$this->session->userdata('tag')) { ?>
								<td id="visible"><?= $affiliates['tagName'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $affiliates['tagName'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on')) { ?>
								<td><?= $affiliates['createdOn'] == '' ? '<i class="help-block">'. lang('lang.norecyet') .'<i/>' : $affiliates['createdOn'] ?></td>
							<?php } ?>

							<?php if($this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col')) { ?>
								<td id="visible">
									<?php
										if($affiliates['status'] == 0)
											echo lang('aff.aj09');
										else if($affiliates['status'] == 1)
											echo lang('aff.aj10');
										else
											echo lang('aff.aj11');
								 	?>
								</td>
							<?php } ?>

							<td>
								<!-- <a href="#delete" data-toggle="tooltip" title="<?= lang('tool.am02'); ?>" onclick="deleteAffiliate(<?= $affiliates['affiliateId']?>, '<?= $affiliates['username'] ?>')"><span class="glyphicon glyphicon-trash"></span></a> -->

								<?php if($affiliates['status'] == 2) { ?>
									<a href="#unfreeze" data-toggle="tooltip" title="<?= lang('tool.am03'); ?>" onclick="unfreezeAffiliate(<?= $affiliates['affiliateId']?>, '<?= $affiliates['username'] ?>')"><span class="glyphicon glyphicon-lock" style="color:green"></span></a>
								<?php } else if($affiliates['status'] == 0) { ?>
									<a href="#freeze" data-toggle="tooltip" title="<?= lang('tool.am04'); ?>" onclick="freezeAffiliate(<?= $affiliates['affiliateId']?>, '<?= $affiliates['username'] ?>')"><span class="glyphicon glyphicon-lock"></span></a>
								<?php } ?>

								<?php if($affiliates['status'] == 1) { ?>
									<a href="<?= BASEURL . 'affiliate_management/userInformation/' . $affiliates['affiliateId']?>" data-toggle="tooltip" title="<?= lang('tool.am05'); ?>"><span class="glyphicon glyphicon-user"></span></a>
								<?php } else { ?>
									<a href="<?= BASEURL . 'affiliate_management/trafficStats/' . $affiliates['affiliateId']?>" data-toggle="tooltip" title="<?= lang('tool.am06'); ?>"><span class="glyphicon glyphicon-zoom-in"></span></a>

									<?php if($this->permissions->checkPermissions('affiliate_tag')) { ?>
										<a href="#tags" data-toggle="tooltip" title="<?= lang('tool.am07'); ?>" onclick="viewAffiliateWithCurrentPage(<?= $affiliates['affiliateId']?>, 'affiliateTag');"><span class="glyphicon glyphicon-tag"></span></a>
									<?php } ?>
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