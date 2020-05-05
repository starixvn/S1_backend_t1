<?php if($action == 'locked') { ?>
    <div class="row">
        <div class="col-md-2">
            <h6><label for="locked_period">Locked period:</label></h6>
        </div>

        <div class="col-md-4">
            <select class="form-control input-sm" name="locked_period" onchange="specifyLocked(this);">
                <option value="">Select</option>
                <option value="0">One day</option>
                <option value="1">One week</option>
                <option value="2">One month</option>
                <option value="3">One year</option>
                <option value="specify">Specify</option>
            </select>
                <?php echo form_error('locked_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
        </div>
    </div>

    <div class="row">
        <div id="hide_date">
            <div class="col-md-2">
                <h6><label for="start_date_locked">Start Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="start_date_locked" id="start_date_locked" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('start_date_locked', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
            </div>

            <div class="col-md-2">
                <h6><label for="end_date_locked">End Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="end_date_locked" id="end_date_locked" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('end_date_locked', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span><br/>
            </div>
        </div>
    </div>
<?php } elseif($action == 'hold') { ?>
    <div class="row">
        <div class="col-md-2">
            <h6><label for="hold_period">Hold period:</label></h6>
        </div>

        <div class="col-md-4">
            <select class="form-control input-sm" name="hold_period" onchange="specifyLocked(this);">
                <option value="">Select</option>
                <option value="0">One day</option>
                <option value="1">One week</option>
                <option value="2">One month</option>
                <option value="3">One year</option>
                <option value="specify">Specify</option>
            </select>
                <?php echo form_error('hold_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
        </div>
    </div>

    <div class="row">
        <div id="hide_date">
            <div class="col-md-2">
                <h6><label for="start_date_hold">Start Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="start_date_hold" id="start_date_hold" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('start_date_hold', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
            </div>

            <div class="col-md-2">
                <h6><label for="end_date_hold">End Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="end_date_hold" id="end_date_hold" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('end_date_hold', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span><br/>
            </div>
        </div>
    </div>
<?php } elseif($action == 'frozen') { ?>
    <div class="row">
        <div class="col-md-2">
            <h6><label for="frozen_period">Hold period:</label></h6>
        </div>

        <div class="col-md-4">
            <select class="form-control input-sm" name="frozen_period" onchange="specifyLocked(this);">
                <option value="">Select</option>
                <option value="0">One day</option>
                <option value="1">One week</option>
                <option value="2">One month</option>
                <option value="3">One year</option>
                <option value="specify">Specify</option>
            </select>
                <?php echo form_error('frozen_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
        </div>
    </div>

    <div class="row">
        <div id="hide_date">
            <div class="col-md-2">
                <h6><label for="start_date_frozen">Start Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="start_date_frozen" id="start_date_frozen" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('start_date_frozen', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
            </div>

            <div class="col-md-2">
                <h6><label for="end_date_frozen">End Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="end_date_frozen" id="end_date_frozen" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('end_date_frozen', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span><br/>
            </div>
        </div>
    </div>
<?php } elseif($action == 'blocked') { ?>

    <div class="row">
        <div class="col-md-2">
            <h6><label for="block_period">Game:</label></h6>
        </div>

        <div class="col-md-4">
            <select class="form-control input-sm" name="game">
                <option value="">Select</option>
                    <?php foreach($games as $row) { ?>
                        <option value="<?= $row['gameId'] ?>"><?= $row['game'] ?></option>
                    <?php } ?>
            </select>
                <?php echo form_error('game', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <h6><label for="block_period">Block period:</label></h6>
        </div>

        <div class="col-md-4">
            <select class="form-control input-sm" name="block_period" onchange="specifyBlocked(this);">
                <option value="">Select</option>
                <option value="0">One day</option>
                <option value="1">One week</option>
                <option value="2">One month</option>
                <option value="3">One year</option>
                <option value="specify">Specify</option>
            </select>
                <?php echo form_error('block_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
        </div>
    </div>

    <div class="row">
        <div id="hide_date">
            <div class="col-md-2">
                <h6><label for="start_date_block">Start Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="start_date_block" id="start_date_block" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('start_date_block', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
            </div>

            <div class="col-md-2">
                <h6><label for="end_date_block">End Date </label></h6>
            </div>

            <div class="col-md-4">
                <input type="date" name="end_date_block" id="end_date_block" class="form-control input-sm" disabled="disabled">
                    <?php echo form_error('end_date_block', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span><br/>
            </div>
        </div>
    </div>
<?php } else { ?>
    <center><h4 style="">Select Type of Action first</h4></center>
<?php } ?>


