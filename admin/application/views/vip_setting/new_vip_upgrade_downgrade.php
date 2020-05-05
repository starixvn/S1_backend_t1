<style>
    fieldset { position: relative; }
    @-moz-document url-prefix() {
        .legend2 { top: -3.8ex; }
    }
    .level_upgrade { font-weight: bold; }
    .glyphicon { cursor: pointer;}
</style>
<div class="form-group" style="margin-left:5px;margin-right:5px;">
    <fieldset style="padding:20px">
        <legend><h4><?=lang('player.levelUpgrade');?></h4></legend>
        <div class="row">
            <div class="col-xs-5">
                <fieldset style="padding:20px;height: 215px;">
                    <legend>
                        <h5><strong><?=lang('Level Up Down Setting');?></strong></h5>
                    </legend>

                    <div class="row upgrade">
                        <div class="col-xs-12 level-upgrade"  data-id="<?=Vipsetting_Management::UPGRADE_SETTING['upgrade_only'];?>" data-upgrade="1" style="margin-bottom:10px;" >
                            <div class="col-xs-1 control-label check-period">
                                <i class="fa fa-square-o fa-lg" aria-hidden="true"></i>
                            </div>
                            <div class="col-xs-3 control-label upgrade-label"><?=lang('Upgrade');?></div>
                            <div class="col-xs-6 ">
                                <select class="form-control" id="upgradeOnly" name="upgradeOnly"></select>
                            </div>
                            <div class="col-xs-2 control-label" id="upgradeSettingBtn"><span class="glyphicon glyphicon-open"></span></div>
                        </div>

                        <div class="col-xs-12 level-upgrade"  data-id="<?=Vipsetting_Management::UPGRADE_SETTING['upgrade_and_downgrade'];?>" data-upgrade="2"  style="margin-bottom:10px;" >
                            <div class="col-xs-1 control-label check-period"><i class="fa fa-square-o fa-lg" aria-hidden="true"></i> </div>
                            <div class="col-xs-3 control-label upgrade-label"><?=lang('Downgrade');?></div>
                            <div class="col-xs-6">
                                <select class="form-control" id="upgradeDowngrade" name="upgradeDowngrade"></select>
                            </div>
                            <div class="col-xs-2 control-label" id="downgradeSettingBtn"><span class="glyphicon glyphicon-save"></span></div>
                        </div>
                        <input type="hidden" name="upgradeSetting" id="upgradeSetting" value="<?php echo isset($data['upgrade_setting']) ? $data['upgrade_setting'] : "" ?>">

                        <br>
                        <div class="col-xs-12 hide">
                            <p><span style="font-style:italic;font-size:12px;color:#919191;" id="up_down_notes"><?=lang('Upgrade Only Notes');?></span></p>
                        </div>
                    </div>
            </div>

            <div class="col-xs-7">
                <fieldset style="padding:20px;">
                    <legend>
                        <h5><strong><?=lang('Period Up Down');?></strong></h5>
                    </legend>
                    <div class="row schedule">
                        <div class="col-xs-12 period-sched" data-id="<?=Vipsetting_Management::UPGRADE_SCHEDULE['daily'];?>" data-sched="1">
                            <div class="col-xs-1 d-inline control-label check-period"><i class="fa fa-square-o fa-lg" aria-hidden="true"></i> </div>
                            <div class="col-xs-2 d-inline control-label period-label"><?=lang('lang.daily');?></div>
                            <div class="col-xs-5 d-inline">
                                <input type="text" value="00:00:00 - 23:59:59" class="form-control" id="" name="" readonly style="background-color:#ffffff;">
                            </div>
                            <div class="col-xs-5 d-inline hide"><input type="text" value="00:00:00 - 23:59:59" class="form-control" id="" name="daily" ></div>
                        </div>

                        <div class="col-xs-12 period-sched" data-id="<?=Vipsetting_Management::UPGRADE_SCHEDULE['weekly'];?>" data-sched="2" style="padding-bottom: 8px;">
                            <div class="col-xs-1 d-inline control-label check-period"> <i class="fa fa-square-o fa-lg" aria-hidden="true"></i></i></div>
                            <div class="col-xs-2 d-inline control-label period-label"><?=lang('lang.weekly');?></div>
                            <div class="col-xs-9 d-inline">
                                <label class="radio-inline"> <input type="radio" name="weekly" value="1"><?=lang('Monday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="2"><?=lang('Tuesday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="3"><?=lang('Wednesday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="4"><?=lang('Thursday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="5"><?=lang('Friday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="6"><?=lang('Saturday');?></label>
                                <label class="radio-inline"> <input type="radio" name="weekly" value="7"><?=lang('Sunday');?></label>
                            </div>
                        </div>

                        <div class="col-xs-12 period-sched" data-id="<?=Vipsetting_Management::UPGRADE_SCHEDULE['monthly'];?>" data-sched="3" style="padding-bottom: 8px;">
                            <div class="col-xs-1 d-inline control-label check-period"><i class="fa fa-square-o fa-lg" aria-hidden="true"></i> </div>
                            <div class="col-xs-2 d-inline control-label period-label"><?=lang('lang.monthly');?></div>
                            <div class="col-xs-5 d-inline">
                                <select class="form-control" id="monthly" name="monthly">
                                    <?php for ($i = 1; $i <= 31; $i++) {?>
                                        <option value="<?php echo $i; ?>"><?=$i?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 period-sched" data-id="<?=Vipsetting_Management::UPGRADE_SCHEDULE['yearly'];?>" data-sched="4">
                            <div class="col-xs-1 d-inline control-label check-period"><i class="fa fa-square-o fa-lg" aria-hidden="true"></i> </div>
                            <div class="col-xs-2 d-inline control-label period-label"><?=lang('lang.yearly');?></div>
                            <div class="col-xs-5 d-inline">
                                <select class="form-control" id="yearly" name="yearly">
                                    <option value="1"><?=lang('January')?></option>
                                    <option value="2"><?=lang('February')?></option>
                                    <option value="3"><?=lang('March')?></option>
                                    <option value="4"><?=lang('April')?></option>
                                    <option value="5"><?=lang('May')?></option>
                                    <option value="6"><?=lang('June')?></option>
                                    <option value="7"><?=lang('July')?></option>
                                    <option value="8"><?=lang('August')?></option>
                                    <option value="9"><?=lang('September')?></option>
                                    <option value="10"><?=lang('October')?></option>
                                    <option value="11"><?=lang('December')?></option>
                                    <option value="12"><?=lang('October')?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="upgradeSched" id="upgradeSched" value="">
                </fieldset>
            </div>


            <div class="col-xs-5">
                <fieldset style="padding:20px;height: 130px;">
                    <legend>
                        <h5><strong><?=lang('Upgrade Bonus');?></strong></h5>
                    </legend>
                    <div class="row ">
                        <div class="col-xs-7" style="margin-left:10px;margin-bottom:10px;" >
                            <label for="promoManager"><?=lang('Select Promo');?></label>
                            <div id="promoManager" >
                                <select class="form-control" name="promo_cms_id" id="promoCmsId">
                                    <option value="">-----<?php echo lang('N/A'); ?>-----</option>
                                    <?php if (!empty($promoCms)) {
                                        foreach ($promoCms as $v): ?>
                                            <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>
                                        <?php endforeach;
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </fieldset>
</div>

<script>

    var baseUrl = '<?php echo base_url(); ?>';
    var vipUpgradeId = '<?php echo isset($data['vip_upgrade_id']) ? $data['vip_upgrade_id'] : "" ?>';
    var upgradeSetting = '<?php echo isset($data['upgrade_setting']) ? $data['upgrade_setting'] : "" ?>';
    var upgradeSched = '<?php echo !empty($data['period_up_down_2']) ? json_encode($data['period_up_down_2']) : "{}" ?>';

    $(document).ready(function(){

        $('#upgradeSettingBtn').on('click', function(){
            $('#headerDescription').html("<b>Upgrade Setting</b>");
            $('#levelUpModal').modal('show');
        });

        $('#downgradeSettingBtn').on('click', function(){
            $('#headerDescription').html("<b>Downgrade Setting</b>");
            $('#levelUpModal').modal('show');
        });

        var $schedule = $('.schedule').find('i');
        var $upgrade = $('.upgrade').find('i');
        var $label = $('.upgrade').find('.upgrade-label');
        var $periodLabel = $('.schedule').find('.period-label');

        loadUpDownGradeSetting();

        // on load level upgrade setting
        if(vipUpgradeId && upgradeSetting) {
            var onLoadUpgrade =  $("[data-upgrade='" + upgradeSetting + "']");
            levelUpgrade($upgrade, $label, onLoadUpgrade.find('i'), onLoadUpgrade.find('.upgrade-label'));
        }

        displayNotes(upgradeSetting);

        // on load period schedule
        if(upgradeSched !== null) {
            var schedule = $.parseJSON(upgradeSched);
            var num = 0;
            if(schedule.daily) {
                num = 1;
                $('#daily').val(schedule.daily);
            } else if(schedule.weekly) {
                num = 2;
                $("input[name=weekly][value=" + schedule.weekly + "]").prop('checked', true);
            } else if(schedule.monthly) {
                num = 3;
                $("#monthly option[value='"+schedule.monthly+"']").attr('selected', 'selected');
            } else if(schedule.yearly) {
                num = 4;
                $("#yearly option[value='"+schedule.yearly+"']").attr('selected', 'selected');
            }
            var onLoadSched =  $("[data-sched='" + num + "']");
            $('#upgradeSched').val(num);
            levelUpgrade($schedule, $periodLabel, onLoadSched.find('i'), onLoadSched.find('.upgrade-label'));
        }

        $('#addSettingsBtn').on('click', function(){
            $('#levelUpModal').modal('show');
        });

        $('.period-sched').on('click', function(){
            var checkPeriod = $(this).find('i');
            var data = $(this).attr('data-id');
            var label = $(this).find('.period-label');

            levelUpgrade($schedule,$periodLabel,checkPeriod,label);
            $('#upgradeSched').val(data);
        });

        $('.level-upgrade').on('click', function() {
            var checkUpgrade = $(this).find('i');
            var data = $(this).attr('data-id');
            var label = $(this).find('.upgrade-label');

            //displayNotes(data);

            levelUpgrade($upgrade,$label,checkUpgrade,label);
            $('#upgradeSetting').val(data);
        });
    });

    function displayNotes(settingId) {
        if(settingId == 1) {
            $('#up_down_notes').html('<?=lang("Upgrade Only Notes");?>');
        } else if (settingId == 2) {
            $('#up_down_notes').html('<?=lang("Upgrade Downgrade Notes");?>');
        }
    }

    function loadUpDownGradeSetting() {
        var optionUp = '', optionUpDown = '';
        var upgrade = $('#upgradeOnly');
        var upgradeDowngrade = $('#upgradeDowngrade');
        var hasUp = false;
        var hasUpdown = false;
        var $selectEmpty = '<option value="">-----<?php echo lang('N/A'); ?>-----</option>';
        $.post( base_url + 'vipsetting_management/upDownTemplateList', function(data){
            if(data) {
                optionUp += $selectEmpty;
                optionUpDown += $selectEmpty;
                for(var i in data) {
                    var selected = '';
                    if(data[i].level_upgrade == 1) {
                        if(vipUpgradeId == data[i].upgrade_id) {
                            selected = 'selected';
                        }
                        optionUp += '<option value="'+data[i].upgrade_id+'" '+selected+'> '+data[i].setting_name+'</option>';
                        hasUp = true;
                    } else {
                        if(vipUpgradeId == data[i].upgrade_id) {
                            selected = 'selected';
                        }
                        optionUpDown += '<option value="'+data[i].upgrade_id+'"  '+selected+'> '+data[i].setting_name+'</option>';
                        hasUpdown = true;
                    }
                }
            }
            if(hasUp) {
                upgrade.html(optionUp);
            } else {
                upgrade.html($selectEmpty)
            }
            if(hasUpdown){
                upgradeDowngrade.html(optionUpDown);
            } else {
                upgradeDowngrade.html($selectEmpty);
            }
        },"json");
    }

    function levelUpgrade($upgrade, $label, checkUpgrade, thisLabel) {
        $upgrade.removeClass('fa-check-square').addClass('fa-square-o');
        $label.removeClass('level_upgrade');
        if(checkUpgrade.hasClass('fa-check-square')) {
            checkUpgrade.removeClass('fa-check-square').addClass('fa-square-o');
        } else {
            checkUpgrade.removeClass('fa-square-o').addClass('fa-check-square');
            thisLabel.addClass('level_upgrade');
        }
    }
</script>