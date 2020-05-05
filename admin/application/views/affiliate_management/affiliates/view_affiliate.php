<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAffiliateList" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseAffiliateList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form class="form-horizontal" action="<?=site_url('affiliate_management/postSearchPage')?>" method="post" role="form" name="myForm">
				<div class="form-group">
					<div class="col-md-4">
						<label class="control-label"><?=lang('aff.ap04');?></label>
						<div class="input-group">
						<input type="text" class="form-control input-sm dateInput" data-start="#start_date" data-end="#end_date" data-time="true"/>
						<input type="hidden" name="start_date" id="start_date" value="<?=(isset($input['start_date']) ? $input['start_date'] : '')?>">
						<input type="hidden" name="end_date" id="end_date" value="<?=(isset($input['end_date']) ? $input['end_date'] : '')?>">
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_reg_date" id="search_reg_date" value="1" <?php if (isset($input['search_reg_date']) && $input['search_reg_date']) {echo 'checked="checked"';}
?>/>
                                </span>
						</div>
					</div>
					<div class="col-md-2">
						<label for="status" class="control-label"><?=lang('aff.al16');?></label>
						<select name="status" class="form-control input-sm">
							<option value=""><?=lang('aff.al17');?></option>
							<option value="active" <?php if (!empty($_POST['status']) && $_POST['status'] == 'active') {
	echo 'selected';
}
?>><?=lang('aff.al18');?></option>
							<option value="inactive" <?php if (!empty($_POST['status']) && $_POST['status'] == 'inactive') {
	echo 'selected';
}
?>><?=lang('aff.al20');?></option>
							<option value="deleted" <?php if (!empty($_POST['status']) && $_POST['status'] == 'deleted') {
	echo 'selected';
}
?>><?=lang('Only Deleted');?></option>
						</select>
					</div>
					<div class="col-md-2">
						<label for="username" class="control-label"><?=lang('aff.al10');?></label>
						<input type="text" name="username" id="username" class="form-control input-sm" value="<?php if (!empty($_POST['username'])) {
	echo $_POST['username'];
}
?>">
						<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="firstname" class="control-label"><?=lang('aff.al14');?></label>
						<input type="text" name="firstname" id="firstname" class="form-control input-sm" value="<?php if (!empty($_POST['firstname'])) {
	echo $_POST['firstname'];
}
?>">
						<?php echo form_error('firstname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="lastname" class="control-label"><?=lang('aff.al15');?></label>
						<input type="text" name="lastname" id="lastname" class="form-control input-sm" value="<?php if (!empty($_POST['lastname'])) {
	echo $_POST['lastname'];
}
?>">
						<?php echo form_error('lastname', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="email" class="control-label"><?=lang('aff.al11');?></label>
						<input type="email" class="form-control input-sm" name="email" id="email" value="<?php if (!empty($_POST['email'])) {
	echo $_POST['email'];
}
?>">
						<?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="parent" class="control-label"><?=lang('lang.parentAffiliate');?></label>
						<select class="form-control input-sm" name="parentId" id="parent">
							<option value=""></option>
							<?php foreach ($affiliates_list as $a) {
	?>
								<option value="<?=$a['affiliateId'];?>"  <?php if (!empty($_POST['parentId']) && $_POST['parentId'] == $a['affiliateId']) {
		echo 'selected';
	}
	?>><?=$a['username'];?></option>
							<?php }
?>
						</select>
					</div>
					<div class="col-md-2" style="padding-top:23px;text-align:left">
						<input type="reset" value="<?=lang('aff.al22');?>" class="btn btn-default btn-sm">
						<input type="submit" value="<?=lang('aff.al21');?>" id="search_main"class="btn btn-primary btn-sm">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<form action="<?=site_url('affiliate_management/actionType')?>" method="post" role="form">
	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="panel-heading custom-ph">
					<h4 class="panel-title custom-pt pull-left">
						<i class="icon-list"></i>
						<?=lang('aff.al33');?>
					</h4>

					<a href="<?php echo $this->utils->getSystemUrl('aff') . '/affiliate/register';?>" class="btn btn-default btn-sm pull-right" target="_blank">
						<i class="fa fa-plus-circle"></i> <?=lang('player.ui71');?>
					</a>
					<div class="clearfix"></div>
				</div>

				<div class="panel-body" id="affiliate_panel_body">
					<!-- <hr class="hr_between_table"/> -->
					<div class="table-responsive">
						<table class="table table-bordered table-hover dataTable" style="width: 100%;" id="affiliatesTable">
							<div class="col-md-6">
								<input type="hidden" name="action_type" id="action_type" value="">
								<input type="hidden" name="affiliates" id="affiliates" value="">
								<div class="input-group">
							      <select name="tags" id="tags" class="form-control">
							      	<option value="">-<?=lang('lang.select');?>-</option>
							      	<?php foreach ($tags as $tag) {?>
                                        <option value="<?=$tag['tagId']?>"><?=$tag['tagName']?></option>
                                    <?php }
?>
							      </select>
							      <span class="input-group-btn">
							        <button type="submit" id="btn-tag" class="btn btn-success btn-action"><i class="fa fa-tag"></i> <?=lang('lang.tagSelected');?></button>
							      </span>
							    </div><!-- /input-group -->
							</div>
							<br><br>
							<thead>
								<tr>
									<th style="padding:8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
									<th><?=lang('aff.aj08');?></th>
									<th><?=lang('aff.aj01');?></th>
									<th><?=lang('aff.aj02');?></th>
									<th><?=lang('aff.aj03');?></th>
									<th><?=lang('aff.aj04');?></th>
									<th><?=lang('aff.al49');?></th>
									<th><?=lang('aff.aj05');?></th>
									<th><?=lang('aff.aj06');?></th>
									<th><?=lang('aff.al48');?></th>
									<th><?=lang('aff.aj07');?></th>
								</tr>
							</thead>

							<tbody>
								<?php
if (!empty($affiliates)) {

	foreach ($affiliates as $aff) {
		$name = $aff['lastname'] . ", " . $aff['firstname'];
		$available_balance = (isset($aff['approved']) && isset($aff['deduct_amt'])) ? @$aff['approved']-@$aff['deduct_amt'] : 0;

		$clsName = '';
		if ($aff['status'] == 1) {
			$clsName = 'warning';
		} else if ($aff['status'] == 2) {
			$clsName = 'danger';
		}
		?>
											<tr class="<?php echo $clsName; ?>">
												<td style="padding:8px"><input type="checkbox" class="checkWhite" id="<?=$aff['affiliateId']?>" name="affiliate[]" value="<?=$aff['affiliateId']?>" onclick="uncheckAll(this.id)"></td>

												<td>
													<?php if ($aff['status'] == 1) {?>
														<a href="<?=site_url('affiliate_management/userInformation/' . $aff['affiliateId'])?>" data-toggle="tooltip" title="<?=lang('tool.am05');?>"><span class="fa fa-user"></span></a>
													<?php } else {
			?>
														<?php if ($this->permissions->checkPermissions('affiliate_tag')) {?>
															<a href="#tags" data-toggle="tooltip" title="<?=lang('tool.am07');?>" onclick="viewAffiliateWithCurrentPage(<?=$aff['affiliateId']?>, 'affiliateTag');"><span class="fa fa-tag"></span></a>
														<?php }
			?>
													<?php }
		?>
													<!-- <a href="#delete" data-toggle="tooltip" title="<?=lang('tool.am02');?>" onclick="deleteAffiliate(<?=$aff['affiliateId']?>, '<?=$aff['username']?>')"><span class="fa fa-trash"></span></a> -->
												</td>
												<td><a href="<?=site_url('affiliate_management/userInformation/' . $aff['affiliateId'])?>"><?=$aff['username']?></a></td>

												<td id="visible"><?=($aff['lastname'] == '') && ($aff['firstname'] == '') ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $name?></td>
												<td id="visible"><?=$aff['email'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $this->permissions->checkPermissions('affiliate_contact_info') == true ? $aff['email'] : preg_replace('#.#', '*', $aff['email'])?></td>
												<td id="visible"><?=$aff['country'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $aff['country']?></td>
												<td id="visible">
													<?php if (isset($aff['parent'])) {?>
														<?=$aff['parent'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $aff['parent']?>
													<?php } else {?>
														<i class="help-block"><?=lang('lang.norecyet');?><i/>
													<?php }
		?>
												</td>

												<td id="visible"><?=$aff['tagName'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $aff['tagName']?></td>
												<td><?=$aff['createdOn'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $aff['createdOn']?></td>
												<td><?=$available_balance == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $available_balance?></td>

												<td id="visible">
													<?php
if ($aff['status'] == 0) {
			echo lang('aff.aj09');
		} else if ($aff['status'] == 1) {
			echo lang('aff.aj10');
		} else {
			echo lang('Deleted');
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
				<div class="panel-footer"></div>
			</div>
		</div>

		<div class="col-md-5" id="affiliate_details" style="display: none;"></div>
	</div>
</form>
<!--end of MODAL for edit column-->

<script type="text/javascript">
    $(document).ready(function() {
	    var dataTable = $('#affiliatesTable').DataTable({
	    	dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
	         autoWidth: false,
	        stateSave: true,
	        //dom: "<'panel-body'<'pull-right'B><'pull-right'f>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
	        buttons: [
	            {
	                extend: 'colvis',
	                postfixButtons: [ 'colvisRestore' ]
	            },
	            'excel'
	        ],
	        "order": [[ 8, 'desc' ]],
            "deferLoading": 57,
            "columnDefs": [ { "targets": 0, "orderable": false } ],
	    });

	    $('#btn-freeze').on('click', function(){
	    	$('#action_type').val('locked');

	    	var affiliates = getAffiliatesId();
	    	$('#affiliates').val(affiliates);

	    	if(affiliates == '') {
	    		return false;
	    	}
	    });

	    $('#btn-tag').on('click', function(){
	    	$('#action_type').val('tag');

	    	var affiliates = getAffiliatesId();
	    	$('#affiliates').val(affiliates);

	    	if(affiliates == '' || $('#tags').val() == '') {
	    		return false;
	    	}
	    });

	    function getAffiliatesId() {
	    	affiliateIDs = Array();
	    	$('input[name^="affiliate"]:checked').each(function() {
			    affiliateIDs.push($(this).val());
			});

			return affiliateIDs.join(", ");
	    }

    } );
</script>
