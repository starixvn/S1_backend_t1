<form action="<?= site_url('payment_management/saveMinWithdrawSetting/') ?>" method="post" class="form-inline">
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('pay.minwithsetting'); ?>  -  <span style ="font-size: small"><?=lang('pay.minwithsetting.details');?></span></h4>
    </div>
    <div class="panel-body">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                <div class="form-group">
                    <label><?= lang('pay.minwithsetting'); ?></label>
                    <input class="form-control number_only" type="text" name="min_withdraw" value="<?= $min_withdraw[0]['value'] ?>">
                    <input class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?= lang('lang.save'); ?>">
                </div>
                </div>
            </div>
    </div>
</div>
</form>
<script type="text/javascript">

    $(document).ready(function () {
        //submenu
        $('#collapseSubmenu').addClass('in');
        $('#view_payment_settings').addClass('active');
        $('#minWithdrawSetting').addClass('active');

    }); //end document ready.

</script>