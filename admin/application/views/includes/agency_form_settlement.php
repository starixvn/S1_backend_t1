<?php
/**
 *   filename:   agency_form_settlement.php
 *   author:     Charlie Yuan
 *   e-mail:     youmax210139@gmail.com
 *   date:       2016-08-09
 * @brief:     field 'settlement' in agency form
 */
?>
    <div class="form-group">
        <div class="input-group">
            <div class="col-md-3">
                <input type="radio" name="settlement_period[]" value="Daily"
                    <?= in_array('Daily', $conditions['settlement_period']) ? 'checked' : '' ?>
                       onclick="setDisplayWeeklyStartDay()">
                <?= lang('Daily'); ?>
            </div>
            <div class="col-md-3">
                <input type="radio" name="settlement_period[]"
                       id="settlement_period_weekly" value="Weekly"
                    <?= in_array('Weekly', $conditions['settlement_period']) ? 'checked' : '' ?>
                       onclick="setDisplayWeeklyStartDay()">
                <?= lang('Weekly'); ?>
            </div>
            <div class="col-md-3">
                <input type="radio" name="settlement_period[]" value="Monthly"
                    <?= in_array('Monthly', $conditions['settlement_period']) ? 'checked' : '' ?>
                       onclick="setDisplayWeeklyStartDay()">
                <?= lang('Monthly'); ?>
            </div>
            <div class="col-md-3">
                <input type="radio" name="settlement_period[]" value="Manual"
                    <?= in_array('Manual', $conditions['settlement_period']) ? 'checked' : '' ?>
                       onclick="setDisplayWeeklyStartDay()">
                <?= lang('Manual'); ?>
            </div>
        </div>
    </div>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_form_settlement.php
