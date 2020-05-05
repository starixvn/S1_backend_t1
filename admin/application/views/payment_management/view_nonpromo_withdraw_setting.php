<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="glyphicon glyphicon-list-alt"></i> <?= lang('pay.18'); ?>  -  <span style ="font-size: small"><?=lang('pay.15');?></span>
        </h4>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <form action="/payment_management/saveNonPromoWithdrawSetting" method="post" role="form">
                    <div class="form-group">
                        <label class="col-md-2 control-label" style="padding-top:7px;font-size:13px;">
                            <?= lang('pay.deposit'); ?>
                        </label>
                        <label class="col-md-2 control-label" style="padding-top:4px;font-size:16px;text-align:center;">
                            x
                        </label>
                        <div class="col-md-3">
                            <input class="form-control number_only" maxlength="2" type="text" name="times" value="<?= $times[0]['value'] ?>">
                        </div>

                        <div class="col-md-3">
                            <input class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?= lang('lang.save'); ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#collapseSubmenu').addClass('in');
        $('#nonPromoWithdrawSetting').addClass('active');
    });
</script>