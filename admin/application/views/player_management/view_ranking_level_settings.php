<!-- start add new ranking setting -->
<div id="addRankingLevelSetting">
    <label for="ranlingLevel">ADD RANKING SETTINGS</label>
    <div class="well" style="overflow: auto">
        <!-- start sort dw list -->
        <form action="<?= BASEURL . 'player_management/addRankingLevelSetting' ?>" method="post" role="form">
            <div class="row">
                <div class="col-md-1">
                    <h6><label for="rankingLevelGroup">Ranking Group: </label></h6>
                </div>
                <div class="col-md-2">
                  <select class="form-control input-sm" name="rankingLevelGroup">
                    <option value="Normal" >Normal</option>
                    <option value="VIP" >VIP</option>
                  </select>
                </div>

                <div class="col-md-1">
                    <h6><label for="rankingLevel">Level: </label></h6>
                </div>
                <div class="col-md-2">
                  <select class="form-control input-sm" name="rankingLevel">
                    <option value="1" >1</option>
                    <option value="2" >2</option>
                    <option value="3" >3</option>
                    <option value="4" >4</option>
                    <option value="5" >5</option>
                    <option value="6" >6</option>
                    <option value="7" >7</option>
                    <option value="8" >8</option>
                    <option value="9" >9</option>
                    <option value="10" >10</option>
                  </select>
                </div>

                <div class="col-md-1">
                    <h6><label for="minDepositRequirement">Required Deposit: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="minDepositRequirement" class="form-control input-sm">
                    <?php echo form_error('minDepositRequirement', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-2">

                    <?php if(!empty($currency)) { ?>
                        <input type="text" class="form-control input-sm" name="currency" id="currency" value="<?= $currency['currencyCode']?>" readonly>
                    <?php } else { ?>
                        <input type="text" class="form-control input-sm" name="currency" id="currency" maxlength="3" placeholder="Currency code">
                    <?php } ?>
                        <?php echo form_error('currency', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
<!--                  <select class="form-control" name="currency">
                    <option value="USD" >USD</option>
                    <option value="RMB" >RMB</option>
                    <option value="MYR" >MYR</option>
                    <option value="GDP" >GDP</option>
                  </select> -->
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <h6><label for="requiredPoint">Required Point: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="requiredPoint" class="form-control input-sm">
                    <?php echo form_error('requiredPoint', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
                <div class="col-md-2">
                    <h6><label for="dailyMaxWithdrawal">Daily Max Withdrawal: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="dailyMaxWithdrawal" class="form-control input-sm">
                    <?php echo form_error('dailyMaxWithdrawal', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <h6><label for="maxDepositAmount">Max Deposit Amount: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="maxDepositAmount" class="form-control input-sm">
                    <?php echo form_error('maxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
                <div class="col-md-2">
                    <h6><label for="minDepositAmount">Min Deposit Amount: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="minDepositAmount" class="form-control input-sm">
                    <?php echo form_error('minDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <input type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="btn btn-info review-btn" data-toggle="modal" />
                </div>
            </div>
        </form>
    </div>
</div>
<!-- end add new ranking setting -->

<!-- start edit new ranking setting -->
<div id="editRankingLevelSetting">
    <label for="ranlingLevel">EDIT RANKING</label>
    <div class="well" style="overflow: auto">
        <!-- start sort dw list -->
        <form action="<?= BASEURL . 'player_management/editRankingLevelSetting' ?>" method="post" role="form">
            <div class="row">
                <div class="col-md-1">
                    <h6><label for="">Ranking Group: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="hidden" name="editRankingLevelId" class="form-control input-sm" id="editRankingLevelId">
                    <input type="text" name="editRankingLevelGroup" class="form-control input-sm" id="editRankingLevelGroup" readonly>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for="">Ranking Level: </label></h6>
                </div>

                <div class="col-md-2">
                    <input type="text" name="editRankingLevel" class="form-control input-sm" id="editRankingLevel" readonly>
                    <br/>
                </div>

                <div class="col-md-1">
                    <h6><label for="">Min Required Deposit: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="editMinRequiredDeposit" class="form-control input-sm" id="editMinRequiredDeposit">
                    <?php echo form_error('editMinRequiredDeposit', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control input-sm" name="editCurrency" id="editCurrency" readonly>

<!--                      <select class="form-control" name="editCurrency" id="editCurrency">
                        <option value="USD" >USD</option>
                        <option value="RMB" >RMB</option>
                        <option value="MYR" >MYR</option>
                        <option value="GDP" >GDP</option>
                      </select> -->
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <h6><label for="editPointRequirement">Required Point: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="editPointRequirement" id="editPointRequirement" class="form-control input-sm">
                    <?php echo form_error('editPointRequirement', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
                <div class="col-md-2">
                    <h6><label for="editDailyMaxWithdrawal">Daily Max Withdrawal: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="editDailyMaxWithdrawal" id="editDailyMaxWithdrawal"  class="form-control input-sm">
                    <?php echo form_error('editDailyMaxWithdrawal', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <h6><label for="editMaxDepositAmount">Max Deposit Amount: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="editMaxDepositAmount" id="editMaxDepositAmount" class="form-control input-sm">
                    <?php echo form_error('editMaxDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
                <div class="col-md-2">
                    <h6><label for="editMinDepositAmount">Min Deposit Amount: </label></h6>
                </div>

                <div class="col-md-3">
                    <input type="text" name="editMinDepositAmount" id="editMinDepositAmount" class="form-control input-sm">
                    <?php echo form_error('editMinDepositAmount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <input type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="btn btn-info review-btn" data-toggle="modal" />
                </div>
            </div>
        </form>
    </div>
</div>
<!-- end edit new ranking setting -->

<div class="row">
    <!-- start request list -->
    <div class="col-md-12" id="toggleView">
        <div class="col-md-5"></div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="col-md-8 pull-left">
                    <h4 class="panel-title "><i class="glyphicon glyphicon-list-alt"></i> Ranking Level Settings</h4>
                </div>
                <div class="pull-right">
                    <span class="glyphicon glyphicon-plus-sign addRankingLevelSettingBtn" data-toggle="tooltip" title="<?= lang('tool.pm05'); ?>" onclick="PlayerManagementProcess.getRankingList()" >
                    </span>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- start data table -->
            <div class="panel panel-body" id="ranking_panel_body">
                <div id="paymentList" class="table-responsive">
                    <table class="table table-striped table-hover" id="myTable">
                        <thead>
                            <tr>
                                <th>Ranking Group</th>
                                <th>Ranking Level</th>
                                <th>Deposit Requirement</th>
                                <th>Point Requirement</th>
                                <th>Max Deposit</th>
                                <th>Min Deposit</th>
                                <th>Daily Max Withdrawal</th>
                                <th>Currency</th>
                                <th>Set By</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                if(!empty($rankingLevelData)) {
                                    foreach($rankingLevelData as $rankingLevelData) {
                            ?>
                                            <tr>
                                                <td><?= $rankingLevelData['rankingLevelGroup'] == '' ? '<i class="help-block">No Record Yet<i/>' : $rankingLevelData['rankingLevelGroup'] ?></td>
                                                <td><?= $rankingLevelData['rankingLevel'] == '' ? '<i class="help-block">No Record Yet<i/>' : $rankingLevelData['rankingLevel'] ?></td>
                                                <td><?= $rankingLevelData['minDepositRequirement'] == '' ? '<i class="help-block">No Minimum Deposit<i/>' : $rankingLevelData['minDepositRequirement'] ?></td>
                                                <td><?= $rankingLevelData['pointRequirement'] == '' ? '<i class="help-block">No Point Requirement<i/>' : $rankingLevelData['pointRequirement'] ?></td>
                                                <td><?= $rankingLevelData['maxDepositAmount'] == '' ? '<i class="help-block">No Daily Max Withdrawal<i/>' : $rankingLevelData['maxDepositAmount'] ?></td>
                                                <td><?= $rankingLevelData['minDepositAmount'] == '' ? '<i class="help-block">No Daily Max Withdrawal<i/>' : $rankingLevelData['minDepositAmount'] ?></td>
                                                <td><?= $rankingLevelData['dailyMaxWithdrawal'] == '' ? '<i class="help-block">No Daily Max Withdrawal<i/>' : $rankingLevelData['dailyMaxWithdrawal'] ?></td>
                                                <td><?= $rankingLevelData['currency'] == '' ? '<i class="help-block">No Record Yet<i/>' : $rankingLevelData['currency'] ?></td>
                                                <td><?= $rankingLevelData['setByName'] == '' ? '<i class="help-block">No Record Yet<i/>' : $rankingLevelData['setByName'] ?></td>

                                                <td>
                                                        <span class="glyphicon glyphicon-edit editRankingLevelSettingBtn" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" onclick="PlayerManagementProcess.getRankingLevelSettingsDetail(<?= $rankingLevelData['rankingLevelSettingId'] ?>)" data-placement="top" data-placement="top">
                                                        </span>

                                                    <a class="deleteRankingBtn" href="<?= BASEURL . 'player_management/deleteRankingLevelSetting/'.$rankingLevelData['rankingLevelSettingId'] ?>">
                                                        <span data-toggle="tooltip" title="<?= lang('lang.delete'); ?>" class="glyphicon glyphicon-trash deleteRankingBtn" data-placement="top">
                                                        </span>
                                                    </a>

                                                </td>
                                            </tr>
                            <?php
                                    }
                                }
                                else{ ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center"><?= lang('lang.norec'); ?>
                                        </td>
                                    </tr>
                            <?php   }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
            <!-- end data table -->

            <div class="panel-footer">
                <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>

            </div>
        </div>
    </div>
    <!-- end request list -->

</div>