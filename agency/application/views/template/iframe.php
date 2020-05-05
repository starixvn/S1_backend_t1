<?php

$currentLang = $this->session->userdata('agency_lang');
$debugbarRenderer = $this->utils->getDebugBarRender();

$favicon = $this->config->item('agency_fav_icon_folder');
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
      <link rel="shortcut icon" href="<?=isset($favicon) ? $this->utils->appendCmsVersionToUri($favicon)  : '/favicon.ico' ?>" type="image/x-icon" />

    <!-- TITLE -->
    <title><?php echo isset($title) ? $title :  lang('Agency Program'); ?></title>

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
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-notify/bootstrap-notify.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('clipboard/2.0.1/clipboard.js')?>"></script>

    <script type="text/javascript" src="<?=$this->utils->jsUrl('pub/pubutils.js')?>"></script>

    <!-- font awesome -->
    <!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="<?=$this->utils->cssUrl('font-awesome.min.css')?>">

    <!-- Custom styles for this template -->
    <link href="<?=$this->utils->cssUrl('daterangepicker.css')?>" rel="stylesheet">

    <!-- Theme switcher -->
    <?php $user_theme = !empty($this->session->userdata('agency_theme')) ? $this->session->userdata('agency_theme') : 'flatly'; ?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
    <link href="<?=$this->utils->thirdpartyUrl('animate/3.6.0/animate.min.css')?>" rel="stylesheet">
    <?php if($this->utils->isEnabledFeature('enable_player_center_style_support_on_agency')): ?>
    <link href="<?=$this->utils->getPlayerCmsUrl($this->utils->getActivePlayerCenterTheme())?>" rel="stylesheet">
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
    @media (min-width: 1400px){
        .container{
            width: 1280px;
        }
    }
    @media (min-width: 1600px){
        .container{
            width: 1480px;
        }
    }
    table.dataTable.table-condensed>thead>tr>th {
        padding-right: 8px;
        padding-left: 4px;
    }
    table.dataTable thead th, table.dataTable tbody th, table.dataTable tbody td{
        padding-right: 8px;
        padding-left: 15px;
    }
    table.dataTable, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate .pagination li a {
        font-size: 12px;
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

});

    </script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js')?>"></script>

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
                "alwaysShowCalendars": true,
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
                   '<?=lang('lang.today')?>': [moment().startOf('day'), moment().endOf('day')],
                   '<?=lang('dt.yesterday')?>': [moment().subtract(1,'days').startOf('day'), moment().subtract(1,'days').endOf('day')],
                   '<?=lang('cms.thisWeek')?>': [moment().startOf('isoWeek'), moment().endOf('day')],
                   '<?=lang('dt.lastweek')?>': [moment().subtract(1,'weeks').startOf('isoWeek'), moment().subtract(1,'weeks').endOf('isoWeek')],
                   '<?=lang('cms.thisMonth')?>': [moment().startOf('month'), moment().endOf('day')],
                   '<?=lang('dt.lastmonth')?>': [moment().subtract(1,'months').startOf('month'), moment().subtract(1,'months').endOf('month')],
                   '<?=lang('cms.thisYear')?>': [moment().startOf('year'), moment().endOf('day')],
                   // '<?=lang('dt.lastyear')?>': [moment().subtract(1,'years').startOf('year'), moment().subtract(1,'years').endOf('year')],
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
    <?php include VIEWPATH . '/others/js_vars.php'; ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>

  <body class="agency-center">

    <div id="main_content">
        <!-- MODAL -->
        <div class="modalbg"></div>

        <!-- ALERT -->
        <div class="container">
            <?php if ($this->session->userdata('result') == 'success') {?>
                <script type="text/javascript">
                    $('.modalbg').show();
                </script>
                <div class="alert alert-success alert-dismissible" id="alert-success" role="alert">
                    <div class="alert-title">
                        <b><?=lang('lang.message');?></b>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span><?=$this->session->userdata('message')?></span>
                </div>
                <?php $this->session->unset_userdata('result');?>
            <?php } elseif ($this->session->userdata('result') == 'danger') {?>
                <script type="text/javascript">
                    $('.modalbg').show();
                </script>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="alert-title">
                        <b><?=lang('lang.message');?></b>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <span><?=$this->session->userdata('message')?></span>
                </div>
                <?php $this->session->unset_userdata('result');?>
            <?php } elseif ($this->session->userdata('result') == 'warning') {?>
                <script type="text/javascript">
                    $('.modalbg').show();
                </script>
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <div class="alert-title">
                        <b><?=lang('lang.message');?></b>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <span><?=$this->session->userdata('message')?></span>
                </div>
                <?php $this->session->unset_userdata('result');?>
            <?php }
?>
        </div>

        <!-- TIMER -->
        <script type="text/javascript">
            $(".modalbg").delay(2500).addClass("in").fadeOut(5500);
            $(".alert").delay(2500).addClass("in").fadeOut(5500);
        </script>

        <!-- CONTENT -->
        <div class="container">
        <?=$main_content?>
        </div>
    </div>
    <script src="<?php echo $this->utils->jsUrl('polyfiller.js'); ?>"></script>
    <script type="text/javascript">
        function confirmDelete(){
            return confirm('<?php echo lang("confirm.delete"); ?>');
        }

        $(function(){
            // webshims.setOptions('forms-ext', {types: 'date time range datetime-local', replaceUI: true});
            webshims.polyfill('forms forms-ext');
        });

//should be array
var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

_pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);

    </script>

<?php echo $this->utils->getAnalyticCode('agency'); ?>

  </body>
</html>
