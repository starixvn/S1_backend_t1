<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('cms.marketingSettings');?> </h4>
        <!-- <a href="<?=BASEURL . 'marketing_management/vipGroupSettingList'?>" class="btn btn-primary btn-sm pull-right" id="add_news"><span class="glyphicon glyphicon-remove"></span></a> -->
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <?php if ($this->permissions->checkPermissions('cashback_setting')) {?>
                    <tr>
                        <td style="width:25%"><b><a href="<?=BASEURL . 'marketing_management/cashbackPayoutSetting'?>"><?=lang('cms.cashbackSettings');?></a></b>
                        </td>
                        <td><?=lang('cms.cashbackSettingsDesc');?>
                        </td>
                    </tr>
                    <?php }
?>
                     <?php if ($this->permissions->checkPermissions('friend_referral_setting')) {?>
                    <tr>
                        <td><b><a href="<?=BASEURL . 'marketing_management/friend_referral_settings'?>"><?=lang('cms.friendReferralSettings');?></a></b>
                        </td>
                        <td><?=lang('cms.friendReferralSettingsDesc');?>
                        </td>
                    </tr>
                    <?php }
?>
                    <?php if ($this->permissions->checkPermissions('promo_category_setting')) {?>
                    <tr>
                        <td><b><a href="<?=BASEURL . 'marketing_management/promoTypeManager'?>"><?=lang('cms.promoCategorySettings');?></a></b>
                        </td>
                        <td><?=lang('cms.promoCategorySettingsDesc');?>
                        </td>
                    </tr>
                    <?php }
?>
                </table>
            </div>
        </div>
    </div>
</div>
