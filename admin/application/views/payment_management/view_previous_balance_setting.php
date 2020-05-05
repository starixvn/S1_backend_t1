<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="glyphicon glyphicon-list-alt"></i> <?= lang('pay.17'); ?>  -  <span style ="font-size: small"><?=lang('pay.14');?></span>
        </h4>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form action="/payment_management/savePreviousBalanceSetting" method="post" role="form">
                    <div class="form-group">
                        <label class="col-md-5 control-label" style="padding-top:7px;font-size:13px;">
                            <?= lang('pay.16'); ?> :
                        </label>
                        <div class="col-md-3">
                            <input class="form-control number_only" maxlength="8" type="text" name="previousBalanceSetAmount" value="<?= $previousBalanceSetting[0]['value'] ?>"/>
                        </div>
                        <div class="col-md-3">
                            <input class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" type="submit" value="<?= lang('lang.save'); ?>">
                        </div>
                        <br><br><br>
                        <label class="control-label"><?= lang('con.pym25'); ?></label>
                        <br>
                        <?php foreach ($clear_withdraw_cond_by_subwallet as $id => $value) {?>
                            <?php echo $value['label'];?>
                            <input class="form-control" type="number" name="clear_withdraw_condition_<?=$value['id']?>" value="<?=$value['value']?>" />
                        <?php }?>
                    </div>
                    <br><br><br>
                    <div class="well col-xs-12">
                        <?=lang("TIP: if set to 5, when a member’s account reaches an amount of 5 or less, any previous withdraw conditions will be cleared when they make a new deposit.");?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#collapseSubmenu').addClass('in');
        $('#previousBalanceSetting').addClass('active');
    });
</script>