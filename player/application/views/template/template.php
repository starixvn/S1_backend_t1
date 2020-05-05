<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
	ob_start("ob_gzhandler");
} else {
	ob_start();
}

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
			<!-- add lang data table translation-->
			<script type="text/javascript">
					var DATATABLES_COLUMNVISIBILITY = "<?php echo lang('Column visibility'); ?>";
					var DATATABLES_RESTOREVISIBILITY = "<?php echo lang('Restore Visibility'); ?>";
			</script>
			<!-- end of data table translation-->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="<?=$description?>"/>
        <meta content="<?=$keywords?>" name="keywords" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <title><?=$title?></title>
        <link rel="shortcut icon" href="<?=IMAGEPATH?>favicon.png" type="image/x-icon" />

        <link rel="stylesheet" type="text/css" href="<?=CSSPATH?>bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=CSSPATH . 'template/' . $skin?>">

        <?php if (!$this->authentication->isLoggedIn()) {?>
                <link rel="stylesheet" type="text/css" href="<?=CSSPATH . 'template/nav-bar-template1.css'?>">
        <?php } else {?>
                <link rel="stylesheet" type="text/css" href="<?=CSSPATH . 'template/nav-bar-template2.css'?>">
        <?php }
?>
        <?=$_styles?>

        <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {?>
                <script type="text/javascript" src="<?=JSPATH?>jquery-1.11.1.js"></script>
        <?php } else {?>
                <script type="text/javascript" src="<?=JSPATH?>jquery-2.1.1.js"></script>
        <?php }
?>

        <script type="text/javascript" src="<?=JSPATH?>bootstrap.js"></script>
        <script type="text/javascript">
           $(function () {
                $('.dropdown-menu input').click(function (event) {
                    event.stopPropagation();
                });
            });
        </script>


        <?=$_scripts?>

    </head>

    <body>
        <!-- Navigation -->

        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                    <?php //var_dump($mainwallet);exit();
if (!$this->authentication->isLoggedIn()) {
	echo form_open(BASEURL . 'auth/login');
	?>
                            <div class="col-md-12 pull-right">
                                <div class="row login-sec">
                                    <div class="col-md-2 logo-sec">
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/logo.png';?>"></a>
                                    </div>
                                    <div class="col-md-5">
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/en.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/cn.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/cn2.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/rwb.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/kr.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/ybg.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/star.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/rb.png';?>"></a>
                                    </div>
                                    <div class="col-md-6 pull-right">
                                            <table style="margin-left:25px">
                                                <tr>
                                                    <td>
                                                        <input type="text" placeholder="Username" class="form-control login-input" name="login" id="inputError" />
                                                    </td>
                                                    <td class="signup-pw-spacer">
                                                        <input type="password" placeholder="Password" class="form-control login-input" name="password" id="Password1" />
                                                    </td>
                                                    <td class="signup-spacer">
                                                        <input type="submit" value="LOG IN" class="submit-btn" />
                                                    </td>
                                                    <td class="signup-spacer">
                                                        <a href="<?=BASEURL . 'auth/register/'?>" class="signup-btn">SIGN UP</a>
                                                    </td>
                                                </tr>
                                            </table>
                                    </div>
                            </div>
                    <?php } else {?>
                            <div class="col-md-12 pull-right">
                                <div class="row login-sec">
                                    <div class="col-md-2 logo-sec">
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/logo.png';?>"></a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/en.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/cn.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/cn2.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/rwb.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/kr.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/ybg.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/star.png';?>"></a>
                                        <a href="<?=BASEURL . 'online'?>"><img src="<?=IMAGEPATH . '/home/flags/rb.png';?>"></a>
                                    </div>
                                    <div class="col-md-4 pull-right player-header-info">
                                        <table >
                                            <tr>
                                                <td><span class=''>Username: </span><span class='welcome_player'><?=$username;?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class=''>Main Wallet: </span><span class='welcome_player'><?=$mainwallet;?></span>
                                                </td>


                                            </tr>
                                            <tr>

                                                <td colspan="3">
                                                <!-- <a class="signup-btn" href="<?=BASEURL . 'smartcashier/makeDeposit/' . $player_id?>">Member Center</a> -->
                                                <a class="btn btn-xs btn-danger" href="<?=BASEURL . 'smartcashier/viewCashier/'?>">Member Center</a>
                                                <a href="<?=BASEURL . 'smartcashier/makeDeposit/1'?>" class="btn btn-xs btn-danger">Deposit</a>
                                                <a href="<?=BASEURL . 'smartcashier/viewWithdraw'?>" class="btn btn-xs btn-danger">Withdrawal</a>
                                                <!-- <a class="signup-btn" href="<?=BASEURL . 'player_controller/playerSettings'?>">Information</a> -->
                                                <a href="<?=BASEURL . 'auth/logout'?>" class="btn btn-xs btn-danger">Logout</a>
                                                </td>

                                            </tr>
                                        </table>
                                    </div>
                                </div>
                        </div>

                    <?php }
echo form_close();?>
                </div>
                <div class="col-md-12 mainnav-sec text-center">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                    <!-- <div class="col-md-10">
                        <ul class="nav navbar-nav pull-right"> -->

                            <li <?=($activenav == 'home') ? 'class="active"' : ''?> >
                                <a href="<?=BASEURL . 'online'?>">Home</a>
                            </li>
                            <?php if ($this->authentication->isLoggedIn()) {?>
                                <!--<li <?=($activenav == 'wallet') ? 'class="active"' : ''?> >
                                    <a class="nav-link" href="<?=BASEURL . 'cashier/makeDeposit/' . $player_id?>">Wallet</a>
                                </li>
                                 <li <?=($activenav == 'games') ? 'class="active"' : ''?> >
                                    <a class="nav-link" href="<?=BASEURL . 'player_controller/playGames/'?>">Game</a>
                                </li> -->
                            <?php }
?>
                            <li <?=($activenav == 'casino') ? 'class="active"' : ''?> >
                                <a href="<?=BASEURL . 'online/casino'?>">Casino</a>
                            </li>
                            <li <?=($activenav == 'sports') ? 'class="active"' : ''?> >
                                <a href="<?=BASEURL . 'online/sports'?>">Sports</a>
                            </li>
                            <li <?=($activenav == 'poker') ? 'class="active"' : ''?> >
                                <a href="<?=BASEURL . 'online/poker'?>">Poker</a>
                            </li>
                            <li <?=($activenav == 'keno') ? 'class="active"' : ''?> >
                                <a href="<?=BASEURL . 'online/keno'?>">Keno</a>
                            </li>
                            <?php if ($this->authentication->isLoggedIn()) {?>
                                 <li <?=($activenav == 'promotions') ? 'class="active"' : ''?> >
                                    <a href="<?=BASEURL . 'online/featured_promotions'?>">Promotions</a>
                                </li>
                            <?php }
?>
                            <li <?=($activenav == 'affiliate') ? 'class="active"' : ''?> >
                                <a href="http://aff.hll999.com" >Affiliate</a>
                            </li>
                            <li <?=($activenav == 'payment') ? 'class="active"' : ''?> >
                                <a href='<?=BASEURL . 'online/contactus/'?>'>Contact Us</a>
                            </li>

                            <?php if (!$this->authentication->isLoggedIn()) {?>
                                <!-- <li <?=($activenav == 'games') ? 'class="active"' : ''?> >
                                    <a href="<?=BASEURL . 'online/playPTGames/1'?>">Games</a>
                                </li> -->
                            <?php }
?>
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                    <br/><br/>
                </div>

            <!-- /.container -->
        </nav>
        <!-- end Navigation -->

        <!-- Page Content -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
if ($this->session->userdata('result') == 'success') {
	?>
                            <div class="alert alert-success alert-dismissible" id="alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <?=$this->session->userdata('message')?>
                                <span id="message"></span>
                            </div>
                    <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');

} elseif ($this->session->userdata('result') == 'danger') {
	?>
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <?=$this->session->userdata('message')?>
                            </div>
                    <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');
} elseif ($this->session->userdata('result') == 'warning') {
	?>
                            <div class="alert alert-warning alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <?=$this->session->userdata('message')?>
                            </div>
                    <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');
}
?>
                    <div id="main_content">
                        <br/>
                        <?=$main_content?>
                    </div>
                    <div id="footer_content">
                        <?=$footer_content?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container -->

        <div class="temp_footer">
            <!-- Footer -->
            <!-- <footer>
                <div class="row">
                    <div class="temp_footer">
                        <center><p>Copyright &copy; FH International Consulting Co. Inc <?=date('Y');?></p></center>
                    </div>
                </div>
            </footer> -->

        </div>
<script src="<?php echo $this->utils->jsUrl('polyfiller.js'); ?>"></script>
<script type="text/javascript">
    $(function(){

        webshims.setOptions('forms-ext', {types: 'date time range datetime-local', replaceUI: true});
        webshims.polyfill('forms forms-ext');
    });
</script>
    </body>
</html>
