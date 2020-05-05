<?php
/**
 *   filename:   agency_form_currency.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-07-25
 *   @brief:     field 'Currency' in agency form
 */
?>
                    <!-- select currency  -->
                    <label class="control-label">
                        <font style="color:red;">*</font> 
                        <?=lang('Currency');?>
                    </label>
                    <select name="currency" id="currency" class="form-control input-sm"
                        title="<?=lang('Currency')?>">
                        <!-- OGP-8977 Currency should changed to All after reset -->
                        <option value="" <?=!isset($conditions['currency']) || empty($conditions['currency']) ? 'selected' : ''?> >
                        <?=lang('All')?>
                        </option>
                        <!--fullanme:Chinese Yuan country:China -->
                        <option value="CNY" <?=($conditions['currency'] == "CNY") ? 'selected' : ''?>>
                        <?=lang('CNY');?>
                        </option>
                        <!--fullanme:United States dollar country:United States -->
                        <option value="USD" <?=($conditions['currency'] == "USD") ? 'selected' : ''?> >
                        <?=lang('USD');?>
                        </option>
                        <!--fullanme:euro country:eurozone -->
                        <option value="EUR" <?=($conditions['currency'] == "EUR") ? 'selected' : ''?> >
                        <?=lang('EUR');?>
                        </option>
                        <!--fullanme:Great Britain Pound country:United Kingdom -->
                        <option value="GBP" <?=($conditions['currency'] == "GBP") ? 'selected' : ''?> >
                        <?=lang('GBP');?>
                        </option>
                        <!--fullanme:Malaysian dollar country:Malaysia -->
                        <option value="MYR" <?=($conditions['currency'] == "MYR") ? 'selected' : ''?> >
                        <?=lang('MYR');?>
                        </option>
                        <!--fullanme:Thai baht country:Thailand -->
                        <option value="THB" <?=($conditions['currency'] == "THB") ? 'selected' : ''?> >
                        <?=lang('THB');?>
                        </option>
                        <!--fullanme:Korean Republic Won country:South Korea -->
                        <option value="KRW" <?=($conditions['currency'] == "KRW") ? 'selected' : ''?> >
                        <?=lang('KRW');?>
                        </option>
                        <!--fullanme:Indonesian rupiah country:Indonesian-->
                        <option value="IDR" <?=($conditions['currency'] == "IDR") ? 'selected' : ''?> >
                        <?=lang('IDR');?>
                        </option>
                        <!--fullanme:Vietnamese dong country:Vietnam -->
                        <option value="VND" <?=($conditions['currency'] == "VND") ? 'selected' : ''?> >
                        <?=lang('VND');?>
                        </option>
                        <!--fullanme:Vietnamese dong country:Vietnam -->
                        <option value="JPY" <?=($conditions['currency'] == "JPY") ? 'selected' : ''?> >
                        <?=lang('JPY');?>
                        </option>
                    </select>
                    <span class="errors"><?php echo form_error('currency'); ?></span>
                    <span id="error-currency" class="errors"></span>
                    <!-- end of select currency  -->
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_form_currency.php
