<?php
    $user_theme = !empty($this->session->userdata('admin_theme')) ? $this->session->userdata('admin_theme') : $this->config->item('sbe_default_theme');
    $currentLang = $this->session->userdata('login_lan');
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="renderer" content="webkit" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <link href="<?= $this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css') ?>" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= $this->utils->cssUrl('font-awesome.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= $this->utils->cssUrl('daterangepicker.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= $this->utils->cssUrl('datatables.min.css') ?>">
        <style>
            /*OVERRIDE BOOTSTRAP*/
            .btn-primary,
            .btn-primary:focus,
            .btn-primary:active:focus {
                color: #ffffff;
                background-color: #66bc11;
                border-color: #66bc11;
            }

            .btn-primary:hover {
                color: #ffffff;
                background-color: #6f944a;
                border-color: #66bc11;
            }

            .daterangepicker .ranges li.active,
            .daterangepicker .ranges li:hover {
                background: #66bc11;
                border: 1px solid #66bc11;
                ;
                color: #fff;
            }

            .daterangepicker .ranges li {
                font-size: 13px;
                background: #f5f5f5;
                border: 1px solid #f5f5f5;
                color: #43ac6a;
                padding: 3px 12px;
                margin-bottom: 8px;
                border-radius: 5px;
                cursor: pointer;
            }

            .pagination>.active>a,
            .pagination>.active>span,
            .pagination>.active>a:hover,
            .pagination>.active>span:hover,
            .pagination>.active>a:focus,
            .pagination>.active>span:focus {
                z-index: 3;
                color: #ffffff;
                background-color: #66bc11;
                border-color: transparent;
                cursor: default;
            }

            .daterangepicker td.active,
            .daterangepicker td.active:hover {
                background-color: #66bc11;
                border-color: #3071a9;
                color: #fff;
            }

            th {
                color: #4E9E07;
                text-transform: uppercase;
                font-weight: normal;
            }

            .text-success {
                color: #66bc11;
                font-size: 12px;
            }

            th {
                background: #000000;
                color: white;
            }

            select {
                color: #000000;
            }
        </style>
        <script type="text/javascript">
            var DATATABLES_COLUMNVISIBILITY = "<?php echo lang('Column visibility'); ?>";
            var SHOWPASSWORD = "<?php echo lang('sys.rp11'); ?>";
            var HIDEPASSWORD = "<?php echo lang('sys.rp11'); ?>";
            var DATATABLES_RESTOREVISIBILITY = "<?php echo lang('Restore Visibility'); ?>";
        </script>

        <?= $_styles ?>

        <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) { ?>
            <script type="text/javascript">
                <?=$this->utils->getFileFromCache(APPPATH . '../public/resources/js/jquery-1.11.1.min.js');?>
            </script>
        <?php } else { ?>
            <script type="text/javascript">
                <?=$this->utils->getFileFromCache(APPPATH . '../public/resources/js/jquery-2.1.4.min.js');?>
            </script>
        <?php } ?>

        <script type="text/javascript">
            function GetXmlHttpObject() {
                var xmlHttp = null;

                try {
                    // Firefox, Opera 8.0+, Safari
                    xmlHttp = new XMLHttpRequest();
                } catch (e) {
                    // Internet Explorer
                    try {
                        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                }
                return xmlHttp;
            }

            var response;

            function lang(str) {
                xmlHttp = GetXmlHttpObject();
                var url = window.location.protocol + "//" + window.location.host + "/" + "ajax_lang";
                url = url + "/index/" + str;
                xmlHttp.open("GET", url, false);
                xmlHttp.send(null);
                if (xmlHttp.readyState == 4) {
                    response = xmlHttp.responseText;
                }
                return response;
            }

            function remove_first_char_if_exist(s, first) {
                if (s && first) {
                    if (s.substr(0, 1) == first) {
                        return s.substr(1, s.length);
                    }
                }
                return s;
            }

            function site_url(uri) {
                if (uri) {
                    //remove first /
                    uri = remove_first_char_if_exist(uri, '/');
                    return '<?php echo site_url(); ?>' + uri;
                }
                return uri;
            }
            var base_url = "<?php echo site_url(); ?>";

            var variables = {
                debugLog: true
            };

            var utils = {
                safelog: function(msg) {
                    //check exists console.log
                    if (variables.debugLog && typeof(console) != 'undefined' && console.log) {
                        console.log(msg);
                    }
                }
            };

            $(function() {
                if ($.fn.dataTable) {
                    $.extend($.fn.dataTable.defaults, {
                        "language": {
                            "url": "<?php echo $this->utils->jsUrl('lang/' . ($currentLang == '2' ? 'chinese' : 'english') . '.json'); ?>"
                        }
                    });
                }
            });
        </script>
        <script type="text/javascript" src="<?= $this->utils->jsUrl('datatables.min.js') ?>"></script>
        <script type="text/javascript" src="<?= $this->utils->jsUrl('bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?= $this->utils->jsUrl('jquery.cookie.min.js') ?>"></script>
        <script type="text/javascript" src="<?= $this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js') ?>"></script>
        <script type="text/javascript" src="<?= $this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js') ?>"></script>
        <script type="text/javascript">
            $(function() {
                $('.panel-title > span.pull-right > a[data-toggle="collapse"]').each(function() {
                    var target = $(this).attr('href');
                    var panelName = target.substr(1);
                    var collapsed = $.cookie(panelName) == 'true';

                    if (collapsed) {
                        $(target).collapse({
                            toggle: true
                        });
                        $(this).addClass('collapsed');
                    } else {
                        $(target).collapse({
                            toggle: false
                        });
                        $(this).removeClass('collapsed');
                    }

                    $(target).parent('.panel').removeClass('hidden');
                });

                $('.panel-title > span.pull-right > a[data-toggle="collapse"]').click(function() {
                    var target = $(this).attr('href');
                    var panelName = target.substr(1);
                    var collapsed = (!$(this).hasClass('collapsed'));

                    $.cookie(panelName, collapsed);
                });
            });
        </script>

        <!-- DATEINPUT -->
        <script type="text/javascript">
            $(function() {
                $('.dateInput').each(function() {
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
                        "separator": " <?= strtolower(lang('player.81')) ?> ",
                        "applyLabel": "<?= lang('lang.apply') ?>",
                        "cancelLabel": "<?= lang('lang.clear') ?>",
                        "fromLabel": "<?= lang('player.80') ?>",
                        "toLabel": "<?= lang('player.81') ?>",
                        "customRangeLabel": "<?= lang('lang.custom') ?>",
                        "daysOfWeek": <?= lang('daysOfWeek') ?>,
                        "monthNames": <?= lang('monthNames') ?>,
                        "firstDay": 0
                    },
                };

                if (!isFuture) {
                    attributes['maxDate'] = moment().endOf('day');
                }

                if (isRange) {
                    attributes['linkedCalendars'] = false;
                    attributes['ranges'] = {
                        '<?= lang('dt.yesterday') ?>': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                        '<?= lang('dt.lastweek') ?>': [moment().subtract(1, 'weeks').startOf('week'), moment().subtract(1, 'weeks').endOf('week')],
                        '<?= lang('dt.lastmonth') ?>': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1, 'months').endOf('month')],
                        '<?= lang('dt.lastyear') ?>': [moment().subtract(1, 'years').startOf('year'), moment().subtract(1, 'years').endOf('year')],
                        '<?= lang('lang.today') ?>': [moment().startOf('day'), moment().endOf('day')],
                        '<?= lang('cms.thisWeek') ?>': [moment().startOf('week'), moment().endOf('day')],
                        '<?= lang('cms.thisMonth') ?>': [moment().startOf('month'), moment().endOf('day')],
                        '<?= lang('cms.thisYear') ?>': [moment().startOf('year'), moment().endOf('day')]
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

        <?= $_scripts ?>
    </head>

    <body data-theme="<?= $user_theme ?>">
        <div id="main_content">
            <?= $main_content ?>
        </div>
        <script type="text/javascript" src="<?= $this->utils->jsUrl('polyfiller.js') ?>"></script>
        <script type="text/javascript">
            <?php
            $this->load->library('authentication');
            $currSessionId = $this->session->userdata('session_id');
            if (empty($currSessionId)) {
                $currSessionId = 0;
            }
            $sessionUserId = $this->authentication->getUserId();
            if ($sessionUserId == null) {
                $sessionUserId = 0;
            }

            $refresh_session_url = $this->utils->site_url_with_host('/admin/' . $sessionUserId . '/' . $currSessionId . '/refresh_session.gif');
            $ping_time = $this->config->item('ping_time');
            $ping = $this->utils->getPing($ping_time, $refresh_session_url);
            echo $ping;
            ?>
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                var stopLoader = false;

                $(".dataTable").each(function(index) {
                    $(this).on('preInit.dt', function() {
                        var id = $(this).attr('id');

                        var progress = '<div class="progress" id="progress-wrapper-' + id + index + '" style="display:none;height:20px;width:100px;font-size:10px;padding-bottom:2px;">';
                        progress += '<div class="progress-bar  progress-bar-info progress-bar-striped active" id="progress-' + id + index + '" role="progressbar"  aria-valuemax="100" style="width: 0%;height">';
                        progress += '</div></div>';

                        $(this).parent().find('.progress-container').html(progress);


                        if ($(this).dataTable().api()) {
                            $(this).on('preXhr.dt', function() {

                                loadProgressBar('progress-' + id + index, 'progress-wrapper-' + id + index, true)
                            });

                            $(this).on('xhr.dt', function() {
                                stopLoader = true;
                            });


                        }
                        // for non-ajax table
                        $(this).on('draw.dt', function() {
                            loadProgressBar('progress-' + id + index, 'progress-wrapper-' + id + index, false)
                        });
                    });

                    $(document).ajaxError(function(event, xhr, options, exc) {

                        stopLoader = true;

                    });
                });

                function loadProgressBar(id, progWrapperId, isAjax) {
                    var value = 10;
                    $('#' + progWrapperId).show();

                    if (isAjax) {
                        var interval = setInterval(function() {
                            value += 1;
                            $("#" + id).show()
                            if (value <= 100) {
                                $("#" + id).css("width", value + "%")
                            }

                            if (value == 100 && stopLoader) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $("#" + id).css("width", 0 + "%").hide();
                                    $('#' + progWrapperId).hide();
                                }, 300);
                            }
                        }, 1);
                    } else {
                        var interval = setInterval(function() {
                            value += 1;
                            $("#" + id).show()
                            $("#" + id).css("width", value + "%")

                            if (value >= 100) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $("#" + id).css("width", 0 + "%").hide();
                                    $('#' + progWrapperId).hide();
                                }, 300);
                            }
                        }, 1);
                    }
                }
            });

            //global function
            var ATTACH_DATATABLE_BAR_LOADER = (function() {
                var stopLoader = false;
                function initBarLoader(id) {
                    $('#' + id).on('preInit.dt', function() {
                        var progress = '<div class="progress" id="progress-wrapper-' + id + '" style="display:none;">';
                        progress += '<div class="progress-bar  progress-bar-info progress-bar-striped active" id="progress-' + id + '" role="progressbar" aria-valuenow="0" aria-valuemin="" aria-valuemax="100" style="width: 0%;height">';
                        progress += '0%';
                        progress += '</div></div>';

                        $(this).parent().find('.progress-container').html(progress);

                        if ($(this).dataTable().api()) {
                            $(this).on('preXhr.dt', function() {

                                loadProgressBar('progress-' + id, 'progress-wrapper-' + id, true)
                            });

                            $(this).on('xhr.dt', function() {
                                stopLoader = true;
                            });
                        }
                        // for non-ajax table
                        $(this).on('draw.dt', function() {

                            loadProgressBar('progress-' + id, 'progress-wrapper-' + id, false)
                        });
                    });
                }

                function loadProgressBar(id, progWrapperId, isAjax) {
                    var value = 10;
                    $('#' + progWrapperId).show();

                    if (isAjax) {

                        var interval = setInterval(function() {
                            value += 1;
                            $("#" + id).show()

                            if (value <= 100) {
                                $("#" + id)
                                    .attr("aria-valuenow", value)
                                    .text(value + "%");
                            }

                            if (value == 100 && stopLoader) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $("#" + id).css("width", 0 + "%").attr("aria-valuenow", 0).text(0 + "%").hide();
                                    $('#' + progWrapperId).hide();
                                }, 300);
                            }
                        }, 1);

                    } else {

                        var interval = setInterval(function() {
                            value += 1;
                            $("#" + id).show()
                            $("#" + id)
                                .css("width", value + "%")
                                .attr("aria-valuenow", value);

                            if (value >= 100) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $("#" + id).css("width", 0 + "%").attr("aria-valuenow", 0).text(0 + "%").hide();
                                    $('#' + progWrapperId).hide();
                                }, 300);
                            }
                        }, 1);
                    }
                }

                $(document).ajaxError(function(event, xhr, options, exc) {
                    stopLoader = true;
                });

                return {
                    init: function(tableId) {
                        initBarLoader(tableId);
                    }
                }
            })();
        </script>
    </body>
</html>