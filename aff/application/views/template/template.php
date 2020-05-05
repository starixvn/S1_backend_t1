<?php
$currentLang = $this->session->userdata('afflang');
//$debugbarRenderer = $this->utils->getDebugBarRender();
//

$company_title = $this->config->item('aff_page_title');

$page_title = (isset($company_title) ? $company_title.' ' : ''). lang('reg.affilate');

$favicon = $this->config->item('aff_fav_icon_folder');

if(empty($favicon)){
    $favicon = get_site_favicon();
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- add lang data table translation-->
    <script type="text/javascript">
        var DATATABLES_COLUMNVISIBILITY = "<?php echo lang('Column visibility'); ?>";
        var DATATABLES_RESTOREVISIBILITY = "<?php echo lang('Restore Visibility'); ?>";
    </script>
    <!-- end of data table translation-->
    <!-- META TAGS -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?=$description?>"/>
    <meta content="<?=$keywords?>" name="keywords" />
    <meta name="author" content="">
    <?php $currenTemplate = $this->config->item('affiliate_view_template'); ?>
    <?php if ($currenTemplate =='xcbet'){ ?>

    <link rel="shortcut icon" href="/<?=$currenTemplate?>/images/favicon.ico" type="image/x-icon" />

    <?php } else { ?>

    <link rel="icon" href="<?=isset($favicon) ? $this->utils->appendCmsVersionToUri($favicon)  : '/favicon.ico' ?>"/>

    <?php } ?>

    <!-- TITLE -->
    <title><?=$page_title?></title>

    <!-- JQUERY -->
    <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {?>
        <script type="text/javascript">
            // include jquery
            <?php echo $this->utils->getFileFromCache(APPPATH . '/../public/resources/js/jquery-1.11.1.min.js'); ?>
        </script>
    <?php } else {?>
        <script type="text/javascript">
            //include jquery
            <?php echo $this->utils->getFileFromCache(APPPATH . '/../public/resources/js/jquery-2.1.4.min.js'); ?>
        </script>
    <?php }
?>

 <!-- Bootstrap -->
    <script type="text/javascript" src="<?=$this->utils->jsUrl('bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->jsUrl('pub/pubutils.js')?>"></script>

<?php
 $segment =  $this->uri->segment(2);
?>
    <?php if($this->utils->isEnabledFeature('enable_aff_custom_css')) : ?>
        <link href="<?=$this->utils->getAnyCmsUrl('includes/css/custom-style-affiliate.css')?>" rel="stylesheet" type="text/css">
    <?php endif; ?>

  <?php if(($currenTemplate == 'webet') && ($segment == 'register')):?>

     <link href="/webet/dist/css/bootstrap.css" rel="stylesheet" type="text/css">
     <link href="/webet/css/styles.css" rel="stylesheet" type="text/css" />
     <!--  <link rel="stylesheet" href="/webet/css/datepicker.css" />
     <script src="/webet/dist/js/bootstrap.min.js"></script> -->

  <?php else: ?>

     <!-- font awesome -->
    <!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> -->
     <link rel="stylesheet" href="<?=$this->utils->cssUrl('font-awesome.min.css')?>">
     <link rel="stylesheet" href="<?=$this->utils->cssUrl('style.css')?>">

    <!-- Custom styles for this template -->
    <link href="<?=$this->utils->cssUrl('daterangepicker.css')?>" rel="stylesheet">

    <!-- Theme switcher -->
    <?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly';?>
    <?php
        if($_SERVER['SERVER_NAME']=='aff.vip-win007.com'){
           $user_theme = $this->session->userdata['affiliate_theme'] = "win007";
        }
    ?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">

  <?php endif; ?>

    <!-- CONTENT STYLE -->
    <?=$_styles?>
    <style type="text/css">
    .dt-bootstrap .panel-body .pull-right{
        margin-left: 2px;
    }
    .dateInput.inline{
        width:300px !important;
    }
    .input-group .form-control {
        z-index: 0!important;
    }
    </style>


    <!-- LANGUAGE -->
    <script type="text/javascript">
        function GetXmlHttpObject() {
            var xmlHttp=null;
            try {
                // Firefox, Opera 8.0+, Safari
                xmlHttp=new XMLHttpRequest();
            } catch (e) {
                // Internet Explorer
                try {
                    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
            }
            return xmlHttp;
        }

        var response;

        function lang(str) {
            xmlHttp=GetXmlHttpObject();
            var url = window.location.protocol + "//" + window.location.host + "/" + "ajax_lang";
            url = url + "/index/" + str;
            xmlHttp.open("GET",url,false);
            xmlHttp.send(null);

            if (xmlHttp.readyState==4) {
                response = xmlHttp.responseText;
            }
            return response;
        }

        var variables={
            debugLog: true
        };

        var utils={
            safelog:function(msg){
                //check exists console.log
                if(variables.debugLog && typeof(console)!='undefined' && console.log){
                    console.log(msg);
                }
            }
        };

        <?php
            switch ($currentLang) {
                case 2:
                    $setLanguage = "chinese";
                    break;
                case 5:
                    $setLanguage = "korean";
                    break;
                case 6:
                    $setLanguage = "thai";
                    break;
                default:
                    $setLanguage = "english";
                    break;
            }
        ?>

$(function(){
    if ($.fn.dataTable) {
        $.extend( $.fn.dataTable.defaults, {
            "language": {
                "url": "<?php echo $this->utils->jsUrl('lang/' . $setLanguage . '.json'); ?>"
            }
        } );
    }

    var loc = window.location.href; // returns the full URL
    if(loc == '<?=$this->utils->getSystemUrl('aff')?>' + '/affiliate/register') {
        $('body').addClass('register');
    }else{
        $('body').removeClass('register');
        $('body').addClass('body-content');
    };
});

    </script>
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-notify/bootstrap-notify.min.js')?>"></script>

    <!-- DATEINPUT -->
     <script type="text/javascript">

        $( function() {
            $('.dateInput').each( function() {
                initDateInput($(this));
            });
        });

        function initDateInput(dateInput) {

            var isRange = (dateInput.data('start') && dateInput.data('end'));
            var isTime = dateInput.data('time');
            var isFuture = dateInput.data('future');

            // START PREPARE ATTRIBUTES
            var attributes = {
                "showDropdowns": true,
                "opens": "left",
                "applyClass": "btn-primary",
                "locale": {
                    "separator": " <?=strtolower(lang('player.81'))?> ",
                    "applyLabel": "<?=lang('lang.apply')?>",
                    "cancelLabel": "<?=lang('lang.clear')?>",
                    "fromLabel": "<?=lang('player.80')?>",
                    "toLabel": "<?=lang('player.81')?>",
                    "customRangeLabel": "<?=lang('lang.custom')?>",
                    "daysOfWeek": <?=lang('daysOfWeek')?>,
                    "monthNames": <?=lang('monthNames')?>,
                    "firstDay": 0
                },
            };

            if ( ! isFuture) {
                attributes['maxDate'] = moment().endOf('day');
            }

            if (isRange) {
                attributes['linkedCalendars'] = false;
                attributes['ranges'] = {
                   '<?=lang('dt.yesterday')?>': [moment().subtract(1,'days').startOf('day'), moment().subtract(1,'days').endOf('day')],
                   '<?=lang('dt.lastweek')?>': [moment().subtract(1,'weeks').startOf('isoWeek'), moment().subtract(1,'weeks').endOf('isoWeek')],
                   '<?=lang('dt.lastmonth')?>': [moment().subtract(1,'months').startOf('month'), moment().subtract(1,'months').endOf('month')],
                   '<?=lang('dt.lastyear')?>': [moment().subtract(1,'years').startOf('year'), moment().subtract(1,'years').endOf('year')],
                   '<?=lang('lang.today')?>': [moment().startOf('day'), moment().endOf('day')],
                   '<?=lang('cms.thisWeek')?>': [moment().startOf('isoWeek'), moment().endOf('day')],
                   '<?=lang('cms.thisMonth')?>': [moment().startOf('month'), moment().endOf('day')],
                   '<?=lang('cms.thisYear')?>': [moment().startOf('year'), moment().endOf('day')]
                };
            } else {
                attributes['singleDatePicker'] = true;
            }

            if (isTime) {
                attributes['locale']['format'] = 'YYYY-MM-DD HH:mm:ss';
                attributes['timePicker'] = true;
                attributes['timePicker24Hour'] = true;
                attributes['timePickerSeconds'] = true;
            } else {
                attributes['locale']['format'] = 'YYYY-MM-DD';
                // attributes['autoApply'] = true;
            }
            // END PREPARE ATTRIBUTES

            // INITIALIZE DATEINPUT
            dateInput.daterangepicker(attributes, function(start, end, label) {
                // CALLBACK: SET VALUES FOR DATE RANGE
                if (isRange) {

                    var startEl = $(dateInput.data('start'));
                    var start = dateInput.data('daterangepicker').startDate;
                    var endEl = $(dateInput.data('end'));
                    var end = dateInput.data('daterangepicker').endDate;

                    startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                }

            });

            dateInput.on('cancel.daterangepicker', function(ev, picker) {
                dateInput.val('');
                if (isRange) {
                    $(dateInput.data('start')).val('');
                    $(dateInput.data('end')).val('');
                }
            });

                // -- check if restriction was made
            if(dateInput.data('restrict-max-range')){

                var $restricted_range = dateInput.data('restrict-max-range');

                if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                    return false;

                dateInput.on('apply.daterangepicker, hide.daterangepicker', function(ev, picker) {

                    var a_day = 86400000; // -- one day
                    var restriction = a_day * $restricted_range;
                    var start_date = new Date(picker.startDate._d);
                    var end_date = new Date(picker.endDate._d);

                    // -- if start date was empty, add a default one
                    if($.trim($(dateInput.data('start')).val()) == ''){
                        var startEl = $(dateInput.data('start'));
                            start = startEl.val();
                            start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');
                            startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));

                        dateInput.data('daterangepicker').setStartDate(start);
                    }

                    // -- if end date was empty, add a default one
                    if($.trim($(dateInput.data('end')).val()) == ''){
                        var endEl = $(dateInput.data('end'));
                            end = endEl.val();
                            end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');
                            endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                        dateInput.data('daterangepicker').setEndDate(end);
                    }

                    dateInput.val($(dateInput.data('start')).val() + ' to ' + $(dateInput.data('end')).val());


                    if((end_date - start_date) >= restriction){ // -- get timestamp result

                        if(dateInput.data('restrict-range-label') && $.trim(dateInput.data('restrict-range-label')) !== "")
                            alert(dateInput.data('restrict-range-label'));
                        else{
                            var day_label = 'day';

                            if($restricted_range > 1) day_label = 'days'

                            alert('Please choose a date range not greater than '+ $restricted_range +' '+ day_label);
                        }

                        //  -- reset value
                        //  -- if validation fails, do not change anything, retain the last correct values
                        $(dateInput.data('start')).val('');
                        $(dateInput.data('end')).val('');

                        var startEl = $(dateInput.data('start'));
                            start = picker.oldStartDate;//startEl.val();
                            start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');

                        var endEl = $(dateInput.data('end'));
                            end = picker.oldEndDate;//endEl.val();
                            end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');

                        dateInput.data('daterangepicker').setStartDate(start);
                        dateInput.data('daterangepicker').setEndDate(end);

                        startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                        endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                        dateInput.val(startEl.val() + ' to ' + endEl.val());
                    }

                });

            }

            // SET DEFAULT VALUES BEFORE INITIALIZATION
            if (isRange) {

                var startEl = $(dateInput.data('start'));
                    start = startEl.val();
                    start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');

                var endEl = $(dateInput.data('end'));
                    end = endEl.val();
                    end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');

                dateInput.data('daterangepicker').setStartDate(start);
                dateInput.data('daterangepicker').setEndDate(end);

                startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

            }
        }

    </script>

    <?=$_scripts?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?=$this->utils->startEvent('Load aff custom header'); ?>
    <?=$this->utils->getAffiliateCustomHeader()?>
    <?=$this->utils->endEvent('Load aff custom header'); ?>

    <?=$this->utils->getTrackingScriptWithDoamin('aff', 'gtm', 'header');?>
    <?=$this->utils->getTrackingScriptWithDoamin('aff', 'ga');?>
<?php //echo $this->utils->printDebugBarHead($debugbarRenderer); ?>
  </head>

  <body>

    <?=$nav_right?>

    <div id="main_content">
        <!-- MODAL -->
        <div class="modalbg"></div>

        <!-- ALERT -->
        <div class="container">
<?php

$alert_message = $this->session->userdata('message');
$alert_type = $this->session->userdata('result');
if (!empty($alert_message)) {
	?>

<script type="text/javascript">

$.notify({
    // options
    message: <?php echo json_encode($alert_message); ?>
},{
    // settings
    type: '<?php echo $alert_type; ?>'
});

</script>
<?php

	$this->session->unset_userdata('result');
	$this->session->unset_userdata('message');

}
?>
        </div>

        <!-- TIMER -->
        <script type="text/javascript">
            $(".modalbg").delay(2500).addClass("in").fadeOut(5500);
            $(".alert").delay(2500).addClass("in").fadeOut(5500);
        </script>

        <!-- CONTENT -->
        <?=$main_content?>


        <?php if($this->config->item('affiliate_view_template') != 'webet'):?>
       <!-- Footer -->
        <div class="container">
            <hr>
            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <center><p>Copyright &copy; <?php echo $this->utils->getConfig('copyright_company_name') ? $this->utils->getConfig('copyright_company_name') : lang('reg.affilate'); ?> <?=date('Y')?> <?php echo PRODUCTION_VERSION; ?></p></center>
                    </div>
                </div>
            </footer>
        </div>
        <?php endif; ?>




    </div>
    <script src="<?php echo $this->utils->jsUrl('polyfiller.js'); ?>"></script>
    <script type="text/javascript">

        function confirmDelete(){
            return confirm('<?php echo lang("confirm.delete"); ?>');
        }

        $(function(){
            webshims.setOptions('forms-ext', {types: 'date time range datetime-local', replaceUI: true});
            webshims.polyfill('forms forms-ext');
        });

//should be array
var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

_pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);

    </script>

<?php echo $this->utils->getAnalyticCode('aff'); ?>

<!-- customize aff css -->
<style type="text/css">
<?php echo isset($aff_css) ? $aff_css : '';?>
</style>

  <?=$this->utils->startEvent('Load aff custom footer'); ?>
  <?=$this->utils->getAffiliateCustomFooter()?>
  <?=$this->utils->endEvent('Load aff custom footer'); ?>
  <?=$this->utils->getTrackingScriptWithDoamin('aff', 'gtm', 'footer');?>
  </body>
</html>
