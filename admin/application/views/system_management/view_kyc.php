<style type="text/css">
    .chart-X {
       color: red;
    }

    .chart-Y {
       color: green;
    }

    .chart-notif{
        position: absolute;
        background: green;
        border: none;
        border-radius: 2px;
        font-size: 8px;
        color: #fff;
        padding: 2px 5px;
        text-transform: uppercase;
        top: 8px;
        right: 35px;
    }

    .table > thead > tr > th{
        border: 0;
    }

    .line-kyc{
        width: 100%;
        display: block;
        height: 1px;
        position: relative;
        background: #06B9FC;
        top: 16px;
    }

    .line-kyc-left::after{
        content: '';
        position: absolute;
        width: 1px;
        height: 20px;
        background: #06B9FC;
        left: 0;
        top: -9px;
    }
    .line-kyc-right::after{
        content: '';
        position: absolute;
        width: 1px;
        height: 20px;
        background: #06B9FC;
        right: 0;
        top: -9px;
    }
    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td{
        border-top: 0 solid #ddd;
    }
    .table-striped > tbody > tr:nth-of-type(2n+1) {
        background-color: #f7f7f7;
    }
    .risk-title{
        position: absolute;
        display: block;
        -ms-transform: 0;
        -webkit-transform: 0;
        transform: 0;
        top: -26px;
        left: 100%;
        width: 770%;
        height: 26px;
        background: red;
        font-size: 10px;
        color: #fff;
        text-transform: uppercase;
        padding: 5px;
    }
</style>

<div class="panel panel-primary panel_main">
    <div class="panel-heading custom-ph" id="">
        <div class="panel-heading custom-ph" id="">
            <h3 class="panel-title custom-pt"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
                <?php if($this->utils->getConfig('use_new_sbe_color')){?>
                    <span class="pull-right" style="margin-top:-1px">
                        <a data-toggle="collapse" href="#main_panel" class="btn btn-primary btn-xs" aria-expanded="true"></a>
                    </span>
                <?php }else{?>
                    <a href="#main_panel" data-toggle="collapse" class="btn btn-primary pull-right panel-button btn-sm" style="margin-right: 0; margin-top: -5px;"><i class="fa fa-caret-down"></i></a>
                <?php }?>
                <a href="javascript:void(0);" class="btn btn-primary pull-right panel-button <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" onclick="return KycSettings.viewAddPanel();" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="margin-right: 4px; margin-top: 0px;"' : 'style="margin-right: 0; margin-top: -5px;"'?>>
                    <i class="glyphicon glyphicon-ok-sign" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="color:white;"' : ''?> data-placement="top"  data-toggle='tooltip'></i>&nbsp;<?=lang('lang.add');?>
                </a>
            </h3>
        </div>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">
		<div class="panel-body">
        	<div class="well" style="overflow:auto;" id="kyc-panel" hidden>
                <div class="panel-body" id="add_panel_body">
                    <form class="form-horizontal" action="<?= BASEURL . 'player_management/addUpdateKYCManagement' ?>" method="post" role="form" id="form_kyc_settings">
                        <input type="hidden" name="id" class="form-control" id="id">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="rate_code" class="control-label"><?= lang('Rate Code'); ?> </label>
                                <input type="text" name="rate_code" id="rate_code" class="form-control input-sm" required>
                                <?php echo form_error('rate_code', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="kyc_lvl" class="control-label"><?= lang('KYC Level'); ?> </label>
                                <input type="text" name="kyc_lvl" id="kyc_lvl" class="form-control input-sm" required>
                                <?php echo form_error('kyc_lvl', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        	<div class="col-md-12">
                                <label for="description_english" class="control-label"><?= lang('English Description'); ?> </label>
                                <input type="text" name="description_english" id="description_english" class="form-control input-sm" required>
                                <?php echo form_error('description_english', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        	<div class="col-md-12">
                                <label for="description_chinese" class="control-label"><?= lang('Chinese Description'); ?> </label>
                                <input type="text" name="description_chinese" id="description_chinese" class="form-control input-sm">
                                <?php echo form_error('description_chinese', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        	<div class="col-md-12">
                                <label for="description_indonesian" class="control-label"><?= lang('Indonesian Description'); ?> </label>
                                <input type="text" name="description_indonesian" id="description_indonesian" class="form-control input-sm">
                                <?php echo form_error('description_indonesian', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        	<div class="col-md-12">
                                <label for="description_vietnamese" class="control-label"><?= lang('Vietnamese Description'); ?> </label>
                                <input type="text" name="description_vietnamese" id="description_vietnamese" class="form-control input-sm">
                                <?php echo form_error('description_vietnamese', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        </div>
                        <?php if(!empty($target_function)): ?>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="rate_code" class="control-label"><?= lang('Target Function'); ?> </label>
                                    <select class="form-control input-sm" name="target_function" id="target_function">
                                        <option value=""><?= lang("Select Target Function") ?></option>
                                        <?php foreach ($target_function as $key => $value) : ?>
                                            <option value="<?= $key ?>"><?= lang($value['lang_format'])?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
	                        <div class="col-md-12" style="text-align:left;padding-top:23px;">
                                <input type="button" value="<?= lang('lang.cancel'); ?>" onclick="return KycSettings.getCancel();" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>">
	                            <input type="button" value="<?= lang('lang.submit'); ?>" onclick="return KycSettings.submitEntry();" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>">
	                        </div>
                    	</div>
                    </form>
                </div>
            </div>
            <div id="kyc_list" class="tab-pane fade in">
           		<div class="row">
                    <div class="col-md-12">
                        <div id="currency_table">
                            <table class="table table-striped table-hover table-condensed" id="my_table">
                                <thead>
                                    <tr>
                                        <!-- <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th> -->
                                        <th><?= lang('Rate Code'); ?></th>
                                        <th><?= lang('Description'); ?></th>
                                        <th><?= lang('KYC Level'); ?></th>
                                        <th><?= lang('system.word85'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php if(!empty($kyc_list)) :  ?>
                                    	<?php foreach ($kyc_list as $key => $value) : ?>
	                                   	<tr>
	                                        <td><?= $value['rate_code'] ?></td>
	                                        <td><?= $value['description'] ?></td>
	                                        <td><?= $value['kyc_lvl'] ?></td>
	                                        <td>
	                                            <a href="javascript:void(0);"><span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('sys.em6'); ?>"  data-placement="top" onclick="KycSettings.getKycDetails(<?= $value['id'] ?>)"></span></a>
                                                <a href="javascript:void(0);"><span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?= lang('sys.gd21'); ?>"  data-placement="top" onclick="KycSettings.removeDetails(<?= $value['id'] ?>)"></span></a>
	                                        </td>
	                                    </tr>
	                                	<?php endforeach; ?>
                                	<?php else: ?>
                                       <tr>
                                            <td colspan="6" style="text-align:center"><?= lang('system.word91'); ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <br/>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

<!-- KYC / Riskscore Table -->
<div class="panel panel-primary panel_main">
    <div class="panel-heading custom-ph" id="">
        <h3 class="panel-title custom-pt"><i class="fa fa-cogs"></i> &nbsp;<?= lang("Risk Score / KYC Chart Management") ?>
        </h3>
    </div>

    <div id="main_panel" class="panel-collapse collapse in ">
        <div class="panel-body">
            <span>Legend: <span style="color: green;">Y</span> = Allowed Withdrawal | <span style="color: red;">X</span> = Withdrawal not allowed</span>
            <div id="kyc_list" class="tab-pane fade in">
                <div class="row">
                    <div class="col-md-12">
                        <div id="currency_table">
                            <table class="table table-striped table-hover table-condensed" id="my_table">
                                <thead>
                                    <tr>
                                        <th style="background: #fff; width: 75px;" colspan="2"></th>
                                        <th style="text-align: center;" colspan="<?=count($kyc_list)?>">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <span class="line-kyc line-kyc-left"></span>
                                                </div>
                                                <div class="col-md-2">
                                                    <h4 style="margin:5px 0;"><?= lang('KYC Level'); ?></h4>
                                                </div>
                                                <div class="col-md-5">
                                                    <span class="line-kyc line-kyc-right"></span>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr style="background: #06B9FC;">
                                        <th style="background: #fff; width: 75px;" colspan="2"></th>
                                        <?php if(!empty($kyc_list)) :  ?>
                                            <?php foreach ($kyc_list as $key => $value) : ?>
                                                <th style="text-align: center; width: 75px" ><?= $value['rate_code'] ?></th>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($renderChart)) :  ?>
                                        <?php $ctr = true; ?>
                                        <?php foreach ($renderChart as $chartkey => $chartValue) : ?>
                                            <tr>
                                                <?php if($ctr) : ?>
                                                    <td style="width: 5px ; background: #fff; font-weight: bold; position: relative;" rowspan="4">
                                                        <span class="risk-title"><?= lang('risk score'); ?></span>
                                                    </td>
                                                    <?php $ctr = false; ?>
                                                <?php endif; ?>
                                                <td class="table-striped" style="width: 75px;"><?= $chartkey ?></td>
                                                <?php if(!empty($chartValue)) :  ?>
                                                    <?php foreach ($chartValue as $key => $value) : ?>
                                                        <td style="text-align: center; width: 75px; position: relative;">
                                                            <select id="chartTag_<?=$value['id'] ?>" data-risklvl="<?=$value['risk_level']?>" data-kyclvl="<?=$value['kyc_level']?>" class="chartTag <?php echo ($value['tag'] == 'X') ? 'chart-X' : 'chart-Y'; ?>">
                                                                <option value="X" class="chart-X" <?=$value['tag'] == 'X' ? ' selected="selected"' : '';?>>X</option>
                                                                <option value="Y" class="chart-Y" <?=$value['tag'] == 'Y' ? ' selected="selected"' : '';?>>Y</option>
                                                            </select>
                                                            <span class="chart-notif" hidden> <?= lang('Saved') ?></span>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
        $('#kyc_setting').addClass('active');

        KycSettings.msgSubmitConfirmation = "<?= lang('sys.ga.conf.add.msg') ?>";
        KycSettings.msgDeleteConfirmation = "<?= lang('confirm.delete') ?>";
	});
</script>