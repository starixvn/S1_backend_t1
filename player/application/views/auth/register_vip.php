<style type="text/css">
.tooltip-inner {
    /* If max-width does not work, try using width instead */
    width: 350px;
}
</style>

<br/>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">Registration for VIP Account</h4> <a href="<?= BASEURL . 'auth/register' ?>" class="pull-right btn btn-danger btn-sm">Register Normal Account</a>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="add_player_panel_body">

                <ol class="breadcrumb">
                    <li class="active"><b>STEP 1: CREATE ACCOUNT</b></li>
                    <li class="active">STEP 2: MAKE A DEPOSIT</li>
                    <li class="active">STEP 3: START PLAYING</li>
                </ol>

                <span class="help-block" style="color:#ff6666;">Fields with (*) means required.</span>

                <form method="post" action="<?= BASEURL . 'auth/postRegisterPlayer'?>" id="my_form" autocomplete="off" roel="form" class="form-inline" name="form">
                    <input type="hidden" value="<?= (set_value('tracking_code') == null) ? $tracking_code:set_value('tracking_code') ?>" name="tracking_code" id="tracking_code"/>
                    <input type="hidden" value="vip" name="level">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-1">
                                    <label for="username"><i style="color:#ff6666;">*</i> Username: </label> <br/>

                                    <input type="text" name="username" id="username" class="form-control" data-toggle="tooltip" title="Your username must contain 5 to 12 letters and/or numbers. No spaces are allowed." value="<?php echo set_value('username') ?>" placeholder="Username">
                                        <?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div id="passwordField">
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-1">
                                        <label for="password"><i style="color:#ff6666;">*</i> Password: </label> <br/>

                                        <input type="password" name="password" id="password" class="form-control"  data-toggle="tooltip" title="Create your password using between 6 to 12 characters." placeholder="Password">
                                            <?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                    </div>
                                </div>

                                <br/>

                                <div class="row">
                                    <div class="col-md-8 col-md-offset-1">
                                        <label for="cpassword"><i style="color:#ff6666;">*</i> Confirm Password: </label> <br/>

                                        <input type="password" name="cpassword" id="cpassword" class="form-control" data-toggle="tooltip" title="Please enter your password again for verification." placeholder="Retype password">
                                            <?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
                                    </div>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8 col-md-offset-1">
                                    <label for="gender"><i style="color:#ff6666;">*</i> Gender: </label>

                                    <?php
                                            $male = "";
                                            if(isset($_POST['gender']) && $_POST['gender'] == 'Male') {
                                                $male  = 'checked';
                                            }

                                            $female = "";
                                            if(isset($_POST['gender']) && $_POST['gender'] == 'Female') {
                                                $female  = 'checked';
                                            }
                                    ?>

                                    <input type="radio" name="gender" value="Male" <?= $male ?>> Male
                                    <input type="radio" name="gender" value="Female" <?= $female ?>> Female
                                        <?php echo form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-12 col-md-offset-1">
                                    <label for="birthday"><i style="color:#ff6666;">*</i> Birthdate: </label> <br/>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="birth_month" id="birth_month" class="form-control">
                                                <option value="" selected>Month</option>
                                                <option value="01" <?php echo set_select('birth_month', '01'); ?> onlick="getDays(1)">January</option>
                                                <option value="02" <?php echo set_select('birth_month', '02'); ?> onlick="getDays(2)">February</option>
                                                <option value="03" <?php echo set_select('birth_month', '03'); ?> onlick="getDays(3)">March</option>
                                                <option value="04" <?php echo set_select('birth_month', '04'); ?> onlick="getDays(4)">April</option>
                                                <option value="05" <?php echo set_select('birth_month', '05'); ?> onlick="getDays(5)">May</option>
                                                <option value="06" <?php echo set_select('birth_month', '06'); ?> onlick="getDays(6)">June</option>
                                                <option value="07" <?php echo set_select('birth_month', '07'); ?> onlick="getDays(7)">July</option>
                                                <option value="08" <?php echo set_select('birth_month', '08'); ?> onlick="getDays(8)">August</option>
                                                <option value="09" <?php echo set_select('birth_month', '09'); ?> onlick="getDays(9)">September</option>
                                                <option value="10" <?php echo set_select('birth_month', '10'); ?> onlick="getDays(10)">October</option>
                                                <option value="11" <?php echo set_select('birth_month', '11'); ?> onlick="getDays(11)">November</option>
                                                <option value="12" <?php echo set_select('birth_month', '12'); ?> onlick="getDays(12)">December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="birth_day" id="birth_day" class="form-control" value="<?php echo set_value('birth_day'); ?>">
                                                <option value="" selected>Day</option>

                                            <?php for($count = 1; $count <= 31; $count++) { ?>
                                                <?php   if($count < 10) {           ?>

                                                            <option value="0<?= $count ?>" <?php echo set_select('birth_day', '0' . $count); ?> >0<?= $count ?></option>

                                                <?php   }   else   {                ?>

                                                            <option value="<?= $count ?>" <?php echo set_select('birth_day', $count); ?> ><?= $count ?></option>

                                                <?php   }                           ?>

                                            <?php } ?>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="birth_year" id="birth_year" class="form-control" value="<?php echo set_value('birth_year'); ?>">
                                                <option value="" selected>Year</option>

                                                <?php for($count = (date('Y')-18); $count >= 1950; $count--) { ?>
                                                            <option value="<?= $count ?>" <?php echo set_select('birth_year', $count); ?>><?= $count ?></option>
                                                <?php } ?>

                                            </select>
                                            <?php echo form_error('birth_month', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="email"><i style="color:#ff6666;">*</i> Email Address: </label> <br/>

                                    <input type="email" name="email" id="email" class="form-control"  data-toggle="tooltip" title="Make sure you enter your valid email address." value="<?php echo set_value('email') ?>" placeholder="Email">
                                        <?php echo form_error('email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="retyped_email"><i style="color:#ff6666;">*</i> Re-type Email Address: </label> <br/>

                                    <input type="email" name="retyped_email" id="retyped_email" class="form-control" data-toggle="tooltip" title="Please enter your email again for verification." value="<?php echo set_value('retyped_email') ?>" placeholder="Retype email">
                                        <?php echo form_error('retyped_email', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lretype_email"></span>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="first_name"><i style="color:#ff6666;">*</i> First Name: </label> <br/>

                                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo set_value('first_name') ?>" placeholder="First name">
                                        <?php echo form_error('first_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="last_name"><i style="color:#ff6666;">*</i> Last Name: </label> <br/>

                                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo set_value('last_name') ?>" placeholder="Last name">
                                        <?php echo form_error('last_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="currency"><i style="color:#ff6666;">*</i> Currency: </label>

                                    <?php if(!empty($currency)) { ?>
                                        <input type="text" name="currency" id="currency" class="form-control" value="<?= $currency['currencyCode'] ?>" placeholder="Currency" readonly>
                                    <?php } else { ?>
                                        <input type="text" name="currency" id="currency" class="form-control" placeholder="Currency">
                                    <?php } ?>

<!--                                         <select name="currency" id="currency" class="form-control">
                                        <option value="" selected>Select Currency</option>
                                        <option value="CNY" <?php echo set_select('currency', 'CNY'); ?>>CNY</option>
                                        <option value="USD" <?php echo set_select('currency', 'USD'); ?>>USD</option>
                                        <option value="PHP" <?php echo set_select('currency', 'PHP'); ?>>PHP</option>
                                    </select> -->
                                        <?php echo form_error('currency', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <!-- <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="address"> Address: </label>

                                    <input type="text" name="address" id="address" class="form-control" value="<?php echo set_value('address'); ?>" placeholder="Address">
                                        <?php echo form_error('address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="city"> City: </label>

                                    <input type="text" name="city" id="city" class="form-control" value="<?php echo set_value('city'); ?>" placeholder="City">
                                        <?php echo form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="address"> Region: </label>

                                    <input type="text" name="region" id="region" class="form-control" value="<?php echo set_value('region'); ?>" placeholder="Region">
                                        <?php echo form_error('region', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="country"> Country: </label>
                                        <select name="country" id="country" class="form-control" data-toggle="tooltip" title="Select what country where you're currently living">
                                            <option value="">Select Country</option>
                                            <?php foreach (unserialize(COUNTRY_LIST) as $key) {  ?>
                                                    <option value="<?= $key?>" <?php echo set_select('country', $key); ?> ><?= $key?></option>
                                            <?php } ?>
                                        </select>

                                        <?php echo form_error('country', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="city"> Zipcode: </label>

                                    <input type="text" name="zipcode" id="zipcode" class="form-control number_only" value="<?php echo set_value('zipcode'); ?>" onkeypress="return isNumberKey(event);" placeholder="Zipcode">
                                        <?php echo form_error('zipcode', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div> -->
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="language"><i style="color:#ff6666;">*</i> Language: </label>

                                    <select name="language" id="language" class="form-control" value="<?php echo set_value('language'); ?>">
                                        <option value="">Select language</option>
                                        <option value="Chinese" <?php echo set_select('language', 'Chinese'); ?>>Chinese</option>
                                        <option value="English" <?php echo set_select('language', 'English'); ?>>English</option>
                                    </select>
                                        <?php echo form_error('language', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="contact_number"><i style="color:#ff6666;">*</i> Contact Number: </label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <div class="input-group">
                                                <input type="text" name="contact_number" id="contact_number" class="form-control number_only" data-toggle="tooltip" title="Please enter your valid contact number. Only accepts numeric characters" value="<?php echo set_value('contact_number'); ?>" onkeypress="return isNumberKey(event);" placeholder="Contact number">
                                            </div>
                                        </div>
                                    </div>
                                        <?php echo form_error('contact_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="security_question"><i style="color:#ff6666;">*</i> Security question: </label>

                                    <select name="security_question" id="security_question" class="form-control" data-toggle="tooltip" title="You must select one of the options.">
                                            <option value="" selected>Select question</option>
                                            <option value="City of Birth" <?php echo set_select('security_question', 'City of Birth'); ?>>City of Birth</option>
                                            <option value="Favorite Sports Team" <?php echo set_select('security_question', 'Favorite Sports Team'); ?>>Favorite Sports Team</option>
                                            <option value="First School" <?php echo set_select('security_question', 'First School'); ?>>First School</option>
                                            <option value="Mother's Maiden Name" <?php echo set_select('security_question', "Mother's Maiden Name"); ?>>Mother's Maiden Name</option>
                                            <option value="Pet's Name" <?php echo set_select('security_question', "Pet's Name"); ?>>Pet's Name</option>
                                    </select>
                                        <?php echo form_error('security_question', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="security_answer"><i style="color:#ff6666;">*</i> Security answer: </label>

                                    <input type="password" name="security_answer" id="security_answer" class="form-control"  data-toggle="tooltip" title="This field must be between 1-50 characters and may include English letters and/or numbers only."  value="<?php echo set_value('security_answer'); ?>" placeholder="Security answer">
                                        <?php echo form_error('security_answer', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-8">
                                    <label for="referral_code"> Referral code: </label>

                                    <input type="text" name="referral_code" id="referral_code" class="form-control"  data-toggle="tooltip" title="Input your referrer's code."  value="<?php echo set_value('referral_code'); ?>" placeholder="Referral code">
                                        <?php echo form_error('referral_code', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>

                            <br/>

                            <!-- <div class="row">
                                <div class="col-md-8">
                                    <label for="coupon"> Coupon Code (Affiliate): </label>

                                    <input type="text" name="coupon" id="coupon" class="form-control"  value="<?php if($this->session->userdata('promoCode')) { echo $this->session->userdata('promoCode'); } else { echo set_value('coupon'); } ?>" placeholder="Coupon">
                                        <?php echo form_error('coupon', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-2">
                                            <input type="checkbox" name="terms" id="terms" onclick="checkAccept(this)"> * I am at least 18 years of age and I accept the <a href="#terms">Terms & Conditions</a>
                                            <?php echo form_error('terms', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                        </div>

                                        <div class="col-md-1">
                                            <input type="submit" value="Create Account" id="accept" class="btn btn-warning btn-lg" disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>