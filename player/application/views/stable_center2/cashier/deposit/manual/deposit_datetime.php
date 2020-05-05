<?php if ($this->utils->isEnabledFeature('enable_deposit_datetime')) :?>
    <?php
        $standard_js = [
            $this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js'),
            $this->utils->thirdpartyUrl('bootstrap-datetimepicker/bootstrap-datetimepicker.min.js'),
        ];

        $standard_css = [
            $this->utils->cssUrl('daterangepicker.css'),
        ];

        foreach ($standard_css as $css_url) {
            echo '<link href="' . $css_url . '" rel="stylesheet"/>';
        }

        foreach ($standard_js as $js_url) {
            echo '<script type="text/javascript" src="' . $js_url . '"></script>';
        }

        $mode_of_deposit = $this->config->item('mode_of_deposit');
    ?>

    <div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-datetime">
        <div class="form-group has-feedback">
            <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Deposit Date time')?></label>
                <?php if($this->system_feature->isEnabledFeature('enable_display_manual_deposit_datetime_step_hint')):?>
                    <span class="step_hint manual_deposit_datetime_step_hint"><?=lang('pay.manual_deposit.step_hint.datetime')?></span>
                <?php endif;?>
            </p>
            <div class="input-group col col-xs-5 col-sm-5 col-md-5 datetimepicker">
                <input type='text' class="form-control" id="deposit_datetime" name="deposit_datetime" data-required-error="<?=lang('Please enter the date and time of your deposit')?>" data-error="<?=lang('text.error')?>" required="required">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
            <div class="help-block with-errors"></div>
        </div>
        <?php if(!empty($mode_of_deposit)): ?>
        <div class="form-group has-feedback">
            <div class="input-group col col-xs-5 col-sm-5 col-md-5">
                <br/>
                <select class="form-control" id="mode_of_deposit" name="mode_of_deposit" data-required-error="<?=lang('Please choose your deposit mode')?>" data-error="<?=lang('text.error')?>" required="required">
                    <option value=""><?php echo lang('Select Mode of Deposit');?></option>
                    <?php foreach ($mode_of_deposit as $key => $value) : ?>
                        <option value="<?= $value ?>"><?=lang($value)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="help-block with-errors"></div>
        </div>
        <?php endif; ?>
        <hr />
    </div>
    <script type="text/javascript">
        $(function () {
            $('.datetimepicker').datetimepicker({
                defaultDate:new Date(),
                format:'YYYY-MM-DD HH:mm:ss'
            });
        });
    </script>
<?php endif; ?>

