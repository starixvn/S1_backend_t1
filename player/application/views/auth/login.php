<?php
$login = array(
	'name' => 'login',
	'id' => 'login',
	'value' => set_value('login'),
	'maxlength' => 80,
	'size' => 30,
);
$password = array(
	'name' => 'password',
	'id' => 'password',
	'size' => 30,
);
?>

<link rel="stylesheet" type="text/css" href="<?=CSSPATH?>bootstrap.css">

<script type="text/javascript" src="<?=JSPATH?>jquery-2.1.1.js"></script>
<script type="text/javascript" src="<?=JSPATH?>bootstrap.js"></script>

<div class="row">
    <div id="container" class="col-md-3 col-md-offset-4">
        <div class="panel panel-warning">
            <div class="panel-heading">
                Player Login
            </div>
            <div class="panel-body">
                <?=$this->uri->uri_string()?>
                <?php echo form_open(BASEURL . $this->uri->uri_string());?>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-user"></span>
                        </div>
                            <?php echo form_input($login, "", "class='form-control' placeholder='Username'", "");?>
                    </div>
                    <?php echo form_error($login['name'], '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-lock"></span>
                        </div>
                            <?php echo form_password($password, "", "class='form-control' placeholder='Password'", "");?>
                    </div>
                    <?php echo form_error($password['name'], '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                </div>

                <span class="help-block" style="color:#ff6666;"><?=isset($errors['login']) ? $errors['login'] : ''?></span>
                <span class="help-block" style="color:#ff6666;"><?=isset($errors['password']) ? $errors['password'] : ''?></span>

                <center>
                    <div class="form-group form-inline">
                        <div id="container" class="col-md-3 col-md-offset-1">
                            <?php echo form_submit('submit', 'Login', "class='btn btn-default'", "");?>
                        </div>
                        <div id="container" class="col-md-3 col-md-offset-2">
                            <a href="<?=BASEURL . 'auth/register'?>" class="btn btn-default">Register</a>
                        </div>
                    </div>
                </center>

                <?php echo form_close();?>
            </div>
            <div class="panel-footer">

            </div>
        </div>
    </div>
</div>