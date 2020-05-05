<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <?=lang('mark.deppromoman');?>
            <a href="#" class="btn btn-default pull-right" id="add_depositpromo">
                <span id="addDepositPromoGlyhicon" class="glyphicon glyphicon-plus-sign"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add depositpromo account -->
        <div class="row add_depositpromo_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;padding:15px;">
                    <form class="form-horizontal" action="<?=BASEURL . 'depositpromo_management/addDepositPromo'?>" method="post" role="form">
                        <div class="form-group" style="margin-bottom:0px">
                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoname');?>: </label>
                                <input type="hidden" name="depositPromoId" class="form-control" id="depositPromoId" >
                                <input type="text" name="promoName" class="form-control input-sm" required placeholder='enter promo name' autofocus>
                                <?php echo form_error('promoName', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>

                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoperiodstart');?>: </label>
                                <input type="date" name="promoPeriodStart" id="promoPeriodStart" class="form-control input-sm" required>
                                <?php echo form_error('promoPeriodStart', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>

                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoperiodend');?>: </label>
                                <input type="date" name="promoPeriodEnd" id="promoPeriodEnd" class="form-control input-sm" required>
                                <?php echo form_error('promoPeriodEnd', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0px">
                            <div class="col-md-12">
                                <label class="control-label"><?=lang('cms.playerlev');?>:</label>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select" data-placeholder="' . lang("pay.selectlevs") . '" data-untoggle="checkbox" required')?>
                                <span class="help-block pull-left" style="font-size:12px;color:#919191;"><i><?=lang('cms.aldeppromo');?></i></span>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" data-toggle="checkbox" data-target="#playerLevels option"<?=isset($form['playerLevels']) && sizeof($form['playerLevels']) == sizeof($levels) ? 'checked' : ''?>> <?=lang('lang.selectall');?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="requiredDepositAmount" class="control-label"><?=lang('cms.reqdepamt');?>: </label>
                                <input type="number" maxlength="12" min='0' name="requiredDepositAmount" id="requiredDepositAmount" class="form-control input-sm" id="requiredDepositAmount" required placeholder='<?=lang('cms.enterdepamt');?>'>
                                <?php echo form_error('requiredDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-3">
                                <label for="bonusAmount" class="control-label"><?=lang('cms.bonus');?>: </label>
                                <input type="number" maxlength="12" min='0' name="bonusAmount" id="bonusAmount" class="form-control input-sm" id="bonusAmount" required placeholder='<?=lang('cms.enterfixbonus');?>'>
                                <?php echo form_error('bonusAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-3 col-lg-4">
                                <label for="first_deposited" class="control-label"> <?=lang('cms.selecttype');?></label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="bonusAmountRuleType" id="bonusAmountRuleType" value="1" onclick="checkAmountRuleType('hide')" class="bonusAmountRuleType" checked> <?=lang('cms.fixamt');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="bonusAmountRuleType" id="bonusAmountRuleType" value="0" onclick="checkAmountRuleType('show')" class="bonusAmountRuleType" > <?=lang('cms.percentage');?> (%)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 maxBonusAmount-sec">
                                <label for="maxBonusAmount" class="control-label"><?=lang('cms.maxbonusamt');?>: </label>
                                <input type="number" maxlength="12" min='0' name="maxBonusAmount" id="maxBonusAmount" class="form-control input-sm" id="maxBonusAmount" placeholder='<?=lang('cms.entermaxbonusamt');?>'>
                                <?php echo form_error('maxBonusAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0px">
                            <div class="col-md-3 col-lg-3">
                                <label class="control-label"><?=lang('cms.withrule');?>: </label>
                                <table>
                                    <tr>
                                        <td style="width:35%"><?=lang('cms.totalbets');?> <strong><i>></i></strong>&nbsp;
                                        </td>
                                        <td style="width:65%">
                                            <input type="number" min='0' maxlength="12" name="totalBetsAmount" id="totalBetsAmount" class="form-control input-sm" id="totalBetsAmount" required placeholder='<?=lang('cms.enteramt');?>'>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <label class="control-label"><?=lang('cms.bonusexp');?>: </label>
                                <table>
                                    <tr>
                                        <td class="to_right"><?=lang('cms.expon');?> &nbsp;
                                        </td>
                                        <td>
                                            <input type="number" min='0' maxlength="12" name="expirationDayCnt" id="expirationDayCnt" class="form-control input-sm" id="expirationDayCnt" required placeholder='<?=lang('cms.enternumday');?>'>
                                        </td>
                                        <td>&nbsp;<?=lang('cms.dayafterdep');?>.
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr class="hr_between_table">
                        <center>
                            <input type="submit" value="<?=lang('lang.add');?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal"/>
                            <span class="btn btn-sm btn-default add_depositpromo-cancel-btn" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                        </center>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit Deposit Promo -->
        <div class="row edit_depositpromo_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;padding:15px;">
                    <form class="form-horizontal" action="<?=BASEURL . 'depositpromo_management/addDepositPromo'?>" method="post" role="form">
                        <div class="form-group" style="margin-bottom:0px">
                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoname');?>: </label>
                                <input type="hidden" name="depositPromoId" class="form-control" id="editDepositPromoId" >
                                <input type="text" name="promoName" id="editDepositPromoName" class="form-control input-sm" required placeholder='enter promo name' autofocus>
                                <?php echo form_error('promoName', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoperiodstart');?>: </label>
                                <input type="date" name="promoPeriodStart" id="editDepositPromoPeriodStart" class="form-control input-sm" required>
                                <?php echo form_error('promoPeriodStart', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label"><?=lang('cms.promoperiodend');?>: </label>
                                <input type="date" name="promoPeriodEnd" id="editDepositPromoPeriodEnd" class="form-control input-sm" required>
                                <?php echo form_error('promoPeriodEnd', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0px">
                            <div class="col-md-12">
                                <label class="control-label"><?=lang('cms.curpromoplayerlev');?>:</label>
                                <br/>
                                <i><span class='currentDepositPromoPlayerLevelLimit' style="font-size:13px;color:#5F5F5F;"></span></i>
                                <?php echo form_multiselect('playerLevels[]', $levels, $form['playerLevels'], 'id="playerLevels" class="form-control chosen-select" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#toggle-checkbox-2"')?>
                                <span class="help-block pull-left" style="font-size:12px;color:#919191;"><i><?=lang('cms.aldeppromo');?></i></span>
                                <div class="checkbox pull-right" style="margin-top: 5px">
                                    <label><input type="checkbox" data-toggle="checkbox" data-target="#playerLevels option"<?=isset($form['playerLevels']) && sizeof($form['playerLevels']) == sizeof($levels) ? 'checked' : ''?>> <?=lang('lang.selectall');?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="requiredDepositAmount" class="control-label"><?=lang('cms.reqdepamt');?>: </label>
                                <input type="number" maxlength="12" min='0' id="editRequiredDepositAmount" name="requiredDepositAmount" class="form-control input-sm" id="requiredDepositAmount" required placeholder='<?=lang("cms.enterdepamt");?>'>
                                <?php echo form_error('requiredDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-3">
                                <label for="bonusAmount" class="control-label"><?=lang('cms.bonus');?>: </label>
                                <input type="number" maxlength="12" min='0' name="bonusAmount" class="form-control input-sm" id="editBonusAmount" required placeholder='<?=lang("enterfixbonus")?>'>
                                <?php echo form_error('bonusAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <div class="col-md-3 col-lg-4">
                                <label for="first_deposited" class="control-label"> <?=lang('cms.selecttype');?></label>
                                <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 4px 10px;">
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="bonusAmountRuleType" id="editBonusAmountRuleType1" value="1" class="bonusAmountRuleType" onclick="checkAmountRuleType('hide')"> <?=lang('cms.fixamt');?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="bonusAmountRuleType" id="editBonusAmountRuleType2" value="0" class="bonusAmountRuleType" onclick="checkAmountRuleType('show')"> <?=lang('cms.percentage');?> (%)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 maxBonusAmount-sec">
                                <label for="maxBonusAmount" class="control-label"><?=lang('cms.maxbonusamt');?>: </label>
                                <input type="number" maxlength="12" min='0' name="maxBonusAmount" id="editMaxBonusAmount" class="form-control input-sm" id="maxBonusAmount" placeholder='<?=lang('cms.entermaxbonusamt');?>'>
                                <?php echo form_error('maxBonusAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div>
                            <!-- <div class="col-md-4">
                                <h6><label for="maxDepositAmount">Max Deposit Amount: </label></h6>
                                <input type="number" maxlength="12" min='0' name="maxDepositAmount" id="editMaxDepositAmount" class="form-control input-sm" id="maxDepositAmount" required placeholder='enter max deposit amount'>
                                <?php echo form_error('maxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                            </div> -->
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 col-lg-3">
                                <label class="control-label"><?=lang('cms.withrule');?>: </label>
                                <table>
                                    <tr>
                                        <td style="width:35%"><?=lang('cms.totalbets');?> <strong><i>></i></strong>&nbsp;
                                        </td>
                                        <td style="width:65%">
                                            <input type="number" min='0' maxlength="12" name="totalBetsAmount" id="editTotalBetsAmount" class="form-control input-sm" id="totalBetsAmount" required placeholder='<?=lang('cms.enteramt');?>'>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <label class="control-label"><?=lang('cms.bonusexp');?>: </label>
                                <table>
                                    <tr>
                                        <td class="to_right"><?=lang('cms.expon');?> &nbsp;
                                        </td>
                                        <td>
                                            <input type="number" min='0' maxlength="12" name="expirationDayCnt" id="editExpirationDayCnt" class="form-control input-sm" id="expirationDayCnt" required placeholder='<?=lang('cms.enternumday');?>'>
                                        </td>
                                        <td>&nbsp;<?=lang('cms.dayafterdep');?>.
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr class="hr_between_table">
                        <center>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm btn-info eview-btn custom-btn-size" data-toggle="modal"/>
                            <span class="btn btn-sm btn-default edit_depositpromo-cancel-btn custom-btn-size" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                        </center>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
               <!-- <form class="navbar-search pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                            Sort By <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                            <li><a onclick="sortDepositPromo('promoName')">Bank name</a></li>
                            <li><a onclick="sortDepositPromo('branchName')">Branch name</a></li>
                            <li><a onclick="sortDepositPromo('updatedOn')">Created On</a></li>
                        </ul>
                    </div>
                    <input type="text" class="search-query" placeholder="Search" name="search" id="search">
                    <input type="button" class="btn btn-sm" value="Go" onclick="searchDepositPromo();">
                </form> -->

                <form action="<?=BASEURL . 'depositpromo_management/deleteSelectedDepositPromo'?>" method="post" role="form">
                    <div id="depositpromo_table" class="table-responsive">
                        <table class="table table-striped table-hover" id="myTable" style="width:100%;">
                            <div class="btn-action">
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                    <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                                </button>&nbsp;
                                <?php if ($export_report_permission) {?>
                                    <a href="<?=BASEURL . 'depositpromo_management/exportToExcel'?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?=lang('lang.exporttitle');?>" data-placement="top">
                                        <i class="glyphicon glyphicon-share"></i>
                                    </a>
                                <?php }
?>
                            </div>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th class="tableHeaderFont"><?=lang('cms.promo');?> #</th>
                                    <th class="tableHeaderFont"><?=lang('cms.promoname');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.promocode');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.promostart');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.promoend');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.reqdepamt');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.bonusval');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.bonusruletype');?></th>
                                    <!-- <th>Max Deposit Amount</th> -->
                                    <th class="tableHeaderFont"><?=lang('cms.maxbonusamt');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.reqtotalbet');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.bonusexpdaycnt');?></th>
                                    <th style="width:20%; font-size:11px;"><?=lang('cms.playerlev');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.createdon');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.createdby');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.updatedon');?></th>
                                    <th class="tableHeaderFont"><?=lang('cms.updatedby');?></th>
                                    <th class="tableHeaderFont"><?=lang('lang.status');?></th>
                                    <th class="tableHeaderFont"><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
if (!empty($depositpromo)) {
	$cnt = 1;
	foreach ($depositpromo as $row) {
		?>
                                                <tr>
                                                    <td></td>
                                                    <td class='tableContent'>
                                                        <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                            <input type="checkbox" class="checkWhite" id="<?=$row['depositpromoId']?>" name="depositpromo[]" value="<?=$row['depositpromoId']?>" onclick="uncheckAll(this.id)"/>
                                                        <?php //d}  ?>
                                                    </td>
                                                    <!-- <td><?=$row['accountOrder'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['accountOrder']?></td> -->
                                                    <td class='tableContent'><?=$cnt?>
                                                    <td class='tableContent'><?=$row['promoName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['promoName']?></td>
                                                    <td class='tableContent'><?=$row['promoCode'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['promoCode']?></td>
                                                    <td class='tableContent'><?=$row['promoPeriodStart'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['promoPeriodStart']?></td>
                                                    <td class='tableContent'><?=$row['promoPeriodEnd'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['promoPeriodEnd']?></td>
                                                    <td class='tableContent'><?=$row['requiredDepositAmount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['requiredDepositAmount']?></td>
                                                    <td class='tableContent'><?=$row['bonusAmount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['bonusAmount']?></td>
                                                    <td class='tableContent'><?=$row['bonusAmountRuleType'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['bonusAmountRuleType'] == '0' ? '%' : 'fix amount'?></td>
                                                    <!-- <td><?=$row['maxDepositAmount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['maxDepositAmount']?></td> -->
                                                    <td class='tableContent'><?=$row['maxBonusAmount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['bonusAmountRuleType'] == '0' ? $row['maxBonusAmount'] : 'No Max Bonus'?></td>
                                                    <td class='tableContent'><?=$row['totalBetRequirement'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['totalBetRequirement']?></td>
                                                    <td class='tableContent'><?=$row['expirationDayCnt'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['expirationDayCnt']?></td>
                                                    <td class='tableContent'>
                                                        <ul>
                                                        <?php
foreach ($row['depositPromoPlayerLevelLimit'] as $key) {
			//echo $key['groupName'].''.$key['vipLevel'].'('.$key['vipLevelName'].')';
			echo "<li class='tableContent'>" . $key['groupName'] . '' . $key['vipLevel'] . "</li>";
		}
		?>
                                                    </td>
                                                    <td class='tableContent'><?=$row['createdOn'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['createdOn']?></td>
                                                    <td class='tableContent'><?=$row['createdBy'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['createdBy']?></td>
                                                    <td class='tableContent'><?=$row['updatedOn'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['updatedOn']?></td>
                                                    <td class='tableContent'><?=$row['updatedBy'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $row['updatedBy']?></td>
                                                    <td class='tableContent'><?=$row['status']?></td>

                                                    <td class='tableContent'>
                                                        <div class="actionDepositPromoGroup">
                                                                <?php if ($row['status'] == 'active') {?>
                                                                    <a href="<?=BASEURL . 'depositpromo_management/activateDepositPromo/' . $row['depositpromoId'] . '/' . 'inactive'?>">
                                                                    <span data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                    </span>
                                                                </a>
                                                                <?php } else {?>
                                                                    <a href="<?=BASEURL . 'depositpromo_management/activateDepositPromo/' . $row['depositpromoId'] . '/' . 'active'?>">
                                                                    <span data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                                                    </span>
                                                                    </a>
                                                                <?php }
		?>

                                                                <span style="cursor:pointer;" class="glyphicon glyphicon-edit editDepositPromoBtn" data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="DepositPromoManagementProcess.getDepositPromoDetails(<?=$row['depositpromoId']?>)" data-placement="top">
                                                                </span>
                                                                <?php //if($row['otcPaymentMethodId'] != 1){ ?>
                                                                    <a href="<?=BASEURL . 'depositpromo_management/deleteDepositPromoItem/' . $row['depositpromoId']?>">
                                                                        <span data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                        </span>
                                                                    </a>
                                                                <?php //}  ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                <?php
$cnt++;
	}
}
?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div><div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable( {
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
                $('.btn-action').prependTo($('.top'));
            }
        } );
    } );
</script>

