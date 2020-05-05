<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt">
                    <i class="icon-banknote"></i> <?=lang('system.word72');?>

                    <a href="javascript: void(0);" role="button" class="btn  pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-primary' : 'btn-info btn-sm panel-button'?>"  onclick="UserManagementProcess.getCurrencyDetails(0)"><?=$this->utils->getConfig('use_new_sbe_color') ? '<span class="glyphicon glyphicon-plus-sign"></span> Add New Currency' : '<i class="fa fa-plus"></i>'?></a>
                </h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body" id="list_panel_body">
                <div class="well" style="overflow:auto;" id="currency-panel">
                    <div class="panel-body" id="add_panel_body">
                        <form class="form-horizontal" action="<?= BASEURL . 'user_management/actionCurrency' ?>" method="post" role="form">
                            <input type="hidden" name="currencyId" class="form-control" id="currencyId">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="currencyName" class="control-label"><?= lang('system.word70'); ?> </label>
                                    <input type="text" name="currencyName" id="currencyName" class="form-control input-sm">
                                    <?php echo form_error('currencyName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="currencyCode" class="control-label"><?= lang('system.word69'); ?> </label>
                                    <input type="text" name="currencyCode" id="currencyCode" maxlength="3" class="form-control input-sm">
                                    <?php echo form_error('currencyCode', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="currencySymbol" class="control-label"><?= lang('Currency Symbol'); ?> </label>
                                    <input type="text" name="currencySymbol" id="currencySymbol" class="form-control input-sm">
                                    <?php echo form_error('currencySymbol', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="currencyShortName" class="control-label"><?= lang('system.word70_s'); ?> </label>
                                    <input type="text" name="currencyShortName" id="currencyShortName" maxlength="10" class="form-control input-sm">
                                    <?php echo form_error('currencyShortName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-1" style="text-align:center;padding-top:23px;">
                                    <input type="submit" value="<?= lang('system.word71'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <?php if($active_currency) { ?>
                                    <center><?= lang('system.word73'); ?> <label for=""><?= $active_currency['currencyCode'] . " - " . $active_currency['currencyName']?></label></center>
                                <?php } else { ?>
                                    <center><?= lang('system.word74'); ?></center>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <form action="<?= BASEURL . 'user_management/deleteSelectedCurrency'?>" method="post" role="form">
                            <div id="currency_table">

                                <table class="table table-striped table-hover table-condensed" id="my_table">
                                    <thead>
                                        <tr>
                                            <th><?= lang('system.word82'); ?></th>
                                            <th><?= lang('system.word81'); ?></th>
                                            <th><?= lang('Currency Symbol'); ?></th>
                                            <th><?= lang('system.word82_s'); ?></th>
                                            <th><?= lang('system.word83'); ?></th>
                                            <th><?= lang('system.word84'); ?></th>
                                            <th><?= lang('system.word85'); ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $countActive = 0; ?>
                                        <?php if(!empty($currency)) { ?>
                                            <?php foreach($currency as $row) { ?>
                                                <tr>
                                                    <td><?= $row['currencyName'] ?></td>
                                                    <td><?= $row['currencyCode'] ?></td>
                                                    <td><?= $row['currencySymbol'] ?></td>
                                                    <td><?= $row['currencyShortName'] ?></td>
                                                    <td><?= $row['updatedOn'] ?></td>
                                                    <td>
                                                        <?php if($row['status'] == $const_currency_active): ?>
                                                            <?php $countActive += 1;?>
                                                            <span class="text-success"><?=lang('system.word87')?></span>
                                                        <?php else: ?>
                                                            <span class="text-danger"><?=lang('system.word86')?></span>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <?php if(!$is_disabled_action) { ?>
                                                            <a href="#editCurrency"><span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('system.word89'); ?>"  data-placement="top" onclick="UserManagementProcess.getCurrencyDetails(<?= $row['currencyId'] ?>)"></span></a>
                                                            <a href="<?= BASEURL . 'user_management/changeCurrencyStatus/' . $row['currencyId']?>"><span class="glyphicon glyphicon-flag" data-toggle="tooltip" title="<?= $row['status'] == $const_currency_active ? lang('system.word92') : lang('system.word88')?>"  data-placement="top"></span></a>
                                                        <?php } else { ?>
                                                            <span class="text-danger"><?=lang('Not available.')?></span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="6" style="text-align:center"><?= lang('system.word91'); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php if($countActive < 1) { ?>
                                    <div class="alert alert-danger" role="alert"><?=lang('currency.required');?></div>
                                <?php } ?>
                                <br/>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>