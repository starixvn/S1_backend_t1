<script type="text/javascript">

    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

    function GetXmlHttpObject()
    {
      var xmlHttp=null;

      try
       {
       // Firefox, Opera 8.0+, Safari
       xmlHttp=new XMLHttpRequest();
       }
      catch (e)
       {
       // Internet Explorer
       try
        {
        xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
       catch (e)
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
       }
      return xmlHttp;
    }

    var response;
    function lang(str)
    {
      xmlHttp=GetXmlHttpObject();
      var url = window.location.protocol + "//" + window.location.host + "/" + "ajax_lang";
      url = url + "/index/" + str;
      xmlHttp.open("GET",url,false);
      xmlHttp.send(null);
         if (xmlHttp.readyState==4)
            {
                 response = xmlHttp.responseText;
            }
      return response;
    }
    function remove_first_char_if_exist(s, first){
        if(s && first){
            if(s.substr(0,1)==first){
                return s.substr(1,s.length);
            }
        }
        return s;
    }
    function site_url(uri){
        if(uri){
            //remove first /
            uri=remove_first_char_if_exist(uri,'/');
            return '<?php echo site_url(); ?>'+uri;
        }
        return uri;
    }
    var base_url = "<?php echo site_url('/'); ?>";
    var imgloader = "<?php echo site_url('/resources/images/ajax-loader.gif'); ?>";

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
    /**
     * Check exists console.error and console.error msg
     *
     * @param {mixed} msg
     */
    utils.saferrlog = function(msg){
        //check exists console.error
        if(variables.debugLog && typeof(console)!='undefined' && console.error){
            console.error(msg);
        }
    };

    /**
     * Simulate evil()
     *
     * Ref. to http://www.seozhijia.net/javascript/168.html
     * @param fn {string} fn The script. Recommended to used less. (hard to trace)
     */
    utils.evil = function(fn){
        var _fn = Function;  //一个变量指向Function，防止有些前端编译工具报错
        return new _fn("return " + fn)();
    }

    $(function(){
        $('#wrapper').css("margin-top",$('.navbar-fixed-top').height()-53);
        $( window ).resize(function() {
            $('#wrapper').css("margin-top",$('.navbar-fixed-top').height()-53);
        });
        if ($.fn.dataTable) {

            $.extend( $.fn.dataTable.defaults, {
                // "scrollX": true,
                "pageLength": <?php echo $this->utils->getConfig('default_datatable_page_length'); ?>,
                "language": {
                    "url": "<?php
                        $lang = 'english';
                        switch ($currentLang) {
                            case '1':
                                $lang = 'english';
                                break;
                            case '2':
                                $lang = 'chinese';
                                break;
                            case '5':
                                $lang = 'korean';
                                break;

                            default:
                                $lang = 'english';
                                break;
                        }
                        echo $this->utils->jsUrl('lang/' . $lang . '.json');
                        ?>"
                }
            } );

            $.fn.dataTable.ext.errMode = "<?php echo $this->utils->getConfig('datatable_error_mode');?>";
        }

    });

    $( function() {

        $('.panel-title > span.pull-right > a[data-toggle="collapse"]').each( function() {

            var target      = $(this).attr('href');
            var panelName   = target.substr(1);
            var collapsed   = $.cookie(panelName) == 'true' || $(this).hasClass('collapsed');

            if (collapsed) {
                $(target).collapse({toggle:true});
                $(this).addClass('collapsed');
            } else {
                $(target).collapse({toggle:false});
                $(this).removeClass('collapsed');
            }

            $(target).parent('.panel').removeClass('hidden');

        });

        $('.panel-title > span.pull-right > a[data-toggle="collapse"]').click( function() {

            var target      = $(this).attr('href');
            var panelName   = target.substr(1);
            var collapsed   = ( ! $(this).hasClass('collapsed'));

            $.cookie(panelName, collapsed);

        });

    });



    //DATEINPUT
    $( function() {
        $('.dateInput').each( function() {
            var curr$El = $(this);
            initDateInputComboExtendAttr(curr$El)
            // /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
            // // for setup attributes of daterangepicker by every elements.
            // var theParam = {}; // default
            // var extraAttr = curr$El.data('extra-attr'); // The ranges attr. need with callback for get.
            // if( typeof(extraAttr) !== 'undefined' ){
            //     switch( typeof(extraAttr) ) {
            //         case 'object': // json format apply
            //             theParam = $.extend(true, {}, theParam, extraAttr );
            //             break; // EOF case 'object':
            //         case 'string': // just string, recommend with function, ex: "getSpecAttr()".
            //             try{
            //                 var theParam = utils.evil(extraAttr);
            //             }catch(e){
            //                 utils.safelog('e on 184 :');
            //                 utils.saferrlog(e);
            //             }
            //             break; // EOF case 'string':
            //     } // EOF switch( typeof(extraAttr) )
            // } // EOF if( typeof(extraAttr) !== 'undefined' )
            //
            // initDateInput(curr$El, theParam);
        }); // EOF $('.dateInput').each( function() {...

        $('.panel-title > span.pull-right > a[data-toggle="collapse"]').each( function() {

            var target      = $(this).attr('href');
            var panelName   = target.substr(1);
            var collapsed   = $.cookie(panelName) == 'true';

            if (collapsed) {
                $(target).collapse({toggle:false});
                $(this).addClass('collapsed');
            } else {
                $(target).collapse({toggle:true});
                $(this).removeClass('collapsed');
            }

        }).click( function() {
            var target      = $(this).attr('href');
            var panelName   = target.substr(1);
            var collapsed   = ( ! $(this).hasClass('collapsed'));
            $.cookie(panelName, collapsed);
        });

    });

    /**
     * Wrapping Initialize Date Range Picker Plugin With Extra Attribute.
     *
     * Usage DOM:
     * <input class="dateInput" data-extra-attr="callbackArrayAttributes()">
     * Javascript function,
     * callbackArrayAttributes = function(){
     *       return [
     *           'linkedCalendars':false,
     *           'showDropdowns':false
     *       ];
     * }
     *
     * @param curr $Els The selements have class "dateInput".
     * @return void
     */
    function initDateInputComboExtendAttr(curr$Els){

        $.each(curr$Els, function(){
            if( typeof($(this).data('daterangepicker') ) === 'undefined' ){ // filter initialized daterangepicker
                var curr$El = $(this);
                var theParam = {}; // default
                var extraAttr = curr$El.data('extra-attr'); // The ranges attr. need with callback for get.
                if( typeof(extraAttr) !== 'undefined' ){
                    switch( typeof(extraAttr) ) {
                        case 'object': // json format apply
                            theParam = $.extend(true, {}, theParam, extraAttr );
                            break; // EOF case 'object':
                        case 'string': // just string, recommend with function, ex: "getSpecAttr()".
                            try{
                                var theParam = utils.evil(extraAttr);
                            }catch(e){
                                utils.safelog('e on 184 :');
                                utils.saferrlog(e);
                            }
                            break; // EOF case 'string':
                    } // EOF switch( typeof(extraAttr) )
                } // EOF if( typeof(extraAttr) !== 'undefined' )

                initDateInput(curr$El, theParam);
            }
        });
    }; // EOF initDateInputComboExtendAttr

    /**
     * Initialize Date Range Picker Plugin
     * Ref. to http://www.daterangepicker.com/#config
     * @param {element jquery(selector)} dateInput The element of jquery(selectStr).
     * @param {object} extendAttr The attributes for merge/rewrite in daterangepicker.
     */
    function initDateInput(dateInput, extendAttr) {
        var _self = this;
        var isRange = (dateInput.data('start') && dateInput.data('end'));
        var isTime = dateInput.data('time');
        var isFuture = dateInput.data('future');

        /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
        var isWithYear = 1; // default
        if( typeof(dateInput.data('with-year')) != 'undefined'){
            isWithYear = dateInput.data('with-year')
        }

        // START PREPARE ATTRIBUTES
        var attributes = {
            "showDropdowns": true,
            "alwaysShowCalendars": true,
            // "opens": "left",
            "applyClass": "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>",
            "cancelClass": "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>",
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
               <?php if ($this->utils->isEnabledFeature('enable_daterangepicker_last30days_item')): ?>
               '<?=lang('dt.last30days')?>': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
               <?php endif ?>
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

            if( isWithYear != 1){ /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
                attributes['locale']['format'] = 'MM-DD HH:mm:ss';
            }
        } else {
            attributes['locale']['format'] = 'YYYY-MM-DD';
            // attributes['autoApply'] = true;

            if( isWithYear != 1){ /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
                attributes['locale']['format'] = 'MM-DD';
            }
        }
        // END PREPARE ATTRIBUTES

        /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
        if( typeof(extendAttr) !== 'undefined' ){
            attributes = $.extend(true, {}, attributes, extendAttr);
        }

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
            else {
                var startEl = $(dateInput.data('start'));
                var start = dateInput.data('daterangepicker').startDate;
                startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));
            }
        }); // EOF dateInput.daterangepicker(attributes, function(start, end, label){...

        dateInput.on('cancel.daterangepicker', function(ev, picker) {
            dateInput.val('');
            if (isRange) {
                $(dateInput.data('start')).val('');
                $(dateInput.data('end')).val('');
            }
        });

        // -- check if restriction was made
        if(dateInput.data('restrict-max-range') && !dateInput.data('override-on-apply')){

            var $restricted_range = dateInput.data('restrict-max-range');

            if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                return false;

            dateInput.on('apply.daterangepicker, hide.daterangepicker', function(ev, picker) {
                var theTarget$El =$(ev.target);
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

                dateInputAssignValue(theTarget$El);

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

                    dateInputAssignValue(theTarget$El);
                }// EOF if((end_date - start_date) >= restriction)

            });// EOF dateInput.on('apply.daterangepicker, hide.daterangepicker', function(ev, picker) {...

        }else{
            dateInput.on('apply.daterangepicker, hide.daterangepicker', function(ev, picker) {
                var theTarget$El =$(ev.target);
                dateInputAssignToStartAndEnd(theTarget$El);
            });
        } // EOF if(dateInput.data('restrict-max-range') && !dateInput.data('override-on-apply')){...



        // SET DEFAULT VALUES BEFORE INITIALIZATION
        if (isRange) {

            var dateFormat = 'YYYY-MM-DD HH:mm:ss';

            if(isWithYear != 1){ /// Patch for OGP-13367 "All Player" search function need to allow search DOB without year
                dateFormat = dateInput.data('daterangepicker').locale.format;
            }
            var startEl = $(dateInput.data('start'));
            var start = startEl.val();
            start = start ? moment(start, dateFormat) : moment().startOf('day');

            var endEl = $(dateInput.data('end'));
            var end = endEl.val();
            end = end ? moment(end, dateFormat) : moment().endOf('day');

            if(start.isValid()){
                dateInput.data('daterangepicker').setStartDate(start);
            }

            if(end.isValid()){
                dateInput.data('daterangepicker').setEndDate(end);
            }

            if( dateInput.is(':visible') ){
                dateInputAssignToStartAndEnd( dateInput );
            }
        } // EOF if (isRange) {...

    } // EOF initDateInput()

    /**
     * The daterangepicker input Assign value by locale.format and locale.separator of plugin,daterangepicker.
     * @param {jquery(element)} dateInputEl The element of jquery selected after daterangepicker initialied.
     */
    function dateInputAssignValue(dateInput$El, fromStartAndEndEls){

        if( typeof(fromStartAndEndEls) === 'undefined'){
            fromStartAndEndEls = false; // default
        }
        /// Patch for OGP-13625 Games history can only open the field of the date once
        var isRange = (dateInput$El.data('start') && dateInput$El.data('end'));
        // get plugin,daterangepicker of the element, dateInput$El.
        var theDateInput = dateInput$El.data('daterangepicker');

        if(isRange){

            if(fromStartAndEndEls){
                // get input hidden field of start and end
                var startSelectorStr = dateInput$El.data('start');
                var endSelectorStr = dateInput$El.data('end');
                var start$El = $(startSelectorStr);
                var end$El = $(endSelectorStr);
                assignStartDate = start$El.val();
                assignEndDate = end$El.val();

                theDateInput.setStartDate(moment(assignStartDate).format(theDateInput.locale.format));
                theDateInput.setEndDate(moment(assignEndDate).format(theDateInput.locale.format));
            }


            var assignStartDate = theDateInput.startDate;
            var assignEndDate = theDateInput.endDate;

            var dateInputVal = '';
            dateInputVal += moment(assignStartDate).format(theDateInput.locale.format);
            dateInputVal += theDateInput.locale.separator;
            dateInputVal += moment(assignEndDate).format(theDateInput.locale.format);
            dateInput$El.val(dateInputVal);
        }

    }// EOF dateInputAssignValue

    /**
     * The daterangepicker input Assign to hidden inputs,"data-start" and "data-end" input.
     * @param {jquery(element)} dateInputEl The element of jquery selected after daterangepicker initialied.
     */
    function dateInputAssignToStartAndEnd(dateInput$El){

        // get plugin daterangepicker of the element, dateInput$El.
        var theDateInput = dateInput$El.data('daterangepicker');

        // get input hidden field of start and end
        var startSelectorStr = dateInput$El.data('start');

        // for data-start
        if( typeof(startSelectorStr) !== 'undefined' ){
            var start$El = $(startSelectorStr);
            if(start$El.length > 0){
                var startDateStr = moment(theDateInput.startDate).format(theDateInput.locale.format);
                start$El.val( startDateStr );
            }
        }

        // for data-end
        var endSelectorStr = dateInput$El.data('end');
        if( typeof(endSelectorStr) !== 'undefined' ){
            var end$El = $(endSelectorStr);
            if(end$El.length > 0){
                var endDateStr =moment(theDateInput.endDate).format(theDateInput.locale.format)
                end$El.val( endDateStr );
            }
        }
    } // EOF dateInputAssignToStartAndEnd

    var DATATABLES_COLUMNVISIBILITY = "<?php echo lang('Column visibility'); ?>";
    var SHOWPASSWORD = "<?php echo lang('sys.rp11'); ?>";
    var HIDEPASSWORD = "<?php echo lang('sys.rp11'); ?>";
    var DATATABLES_RESTOREVISIBILITY = "<?php echo lang('Restore Visibility'); ?>";

    // function changeLanguage() {
    //     var lang = $('#language').val();
    //     $.get('/affiliate/changeLanguage/' + lang, function() {
    //         location.reload();
    //     })
    // }

    function confirmDelete(){
        return confirm('<?php echo lang("confirm.delete"); ?>');
    }

    var Template = {
        changeSidebarStatus : function(requestId) {
            var sidebar_status = '';
            if ($('#sidebar_status').hasClass('active') || $('#sidebar_status').hasClass('')) {
                sidebar_status = 'inactive';
            } else {
                sidebar_status = 'active';
            }
            // console.log('sidebar_status: '+sidebar_status);
            $.ajax({
                'url' : base_url +'player_management/changeSidebarStatus/'+sidebar_status,
                'type' : 'POST',
                'success' : function(data){}
           },'json');
            return false;
        },
        removeCrumbSession : function(name) {
            var cnt = 0;

            cnt = $('#cnt').text() - 1;
            $('#cnt').text(cnt);

            if (cnt == 0) {
                $("#custom-well").hide();
            }
            // console.log('name: '+name);
            $.ajax({
                'url' : base_url +'user_management/deleteCrumb/'+name,
                'type' : 'POST',
                'success' : function(data){}
            },'json');
            return false;

        }
    };

function resizeSidebar() {
    $('#sidebar-wrapper').css('height', 'calc(101vh - '+$('#bs-navbar-collapse-1').height()+'px)');

    // setTimeout( function() {
    //     if($('#sidebar-wrapper').height() < $('#wrapper').height())
    //          $('#sidebar-wrapper').css('height', $('#wrapper').height() + 30);
    // }, 1000);
}

var stopLoader=false;

function loadProgressBar(id,progWrapperId,isAjax){
  var value = 10;
  $('#'+progWrapperId).show();

  if(isAjax){

   var interval = setInterval(function() {
    value += 1;
    $("#"+id).show()

    if(value <= 100){
    $("#"+id)
    .css("width", value + "%")
    .attr("aria-valuenow", value)
    .text(value + "%");
    }

    if (value == 100 && stopLoader){
      clearInterval(interval);
      setTimeout(function(){
          $("#"+id).css("width", 0 + "%").attr("aria-valuenow", 0).text(0 + "%").hide();
          $('#'+progWrapperId).hide();
      },300);
    }
    }, 1);

    }else{

       var interval = setInterval(function() {
        value += 1;
        $("#"+id).show()
        $("#"+id)
        .css("width", value + "%")
        .attr("aria-valuenow", value)
        .text(value + "%");

        if (value >= 100){
          clearInterval(interval);
          setTimeout(function(){
              $("#"+id).css("width", 0 + "%").attr("aria-valuenow", 0).text(0 + "%").hide();
              $('#'+progWrapperId).hide();
          },300);
        }
        }, 1);

    }

}


//  function initBarLoader(id){

//   $('#'+id).on( 'preInit.dt', function () {

//       var progress  = '<div class="progress" id="progress-wrapper-'+id+'" style="display:none;">';
//       progress += '<div class="progress-bar  progress-bar-success progress-bar-striped active" id="progress-'+id+'" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height">';
//       progress += '0%';
//       progress += '</div></div>';

//       $(this).parent().find('.progress-container').html(progress);


//       if($(this).dataTable().api()){
//         $(this).on( 'preXhr.dt', function () {

//             loadProgressBar('progress-'+id, 'progress-wrapper-'+id,true)
//         });

//         $(this).on( 'xhr.dt', function () {
//             stopLoader = true;
//         });


//     }
//             // for non-ajax table
//             $(this).on( 'draw.dt', function () {

//                 loadProgressBar('progress-'+id, 'progress-wrapper-'+id,false)
//             });


//         });

// }


var ringSound = new Audio();
var msg_count = 0,
    local_deposit = 0,
    thrdpartydeposit = 0,
    promo = 0,
    withdraw = 0,
    thrdpartySettled = 0,
    onloadEventHandler = 0,
    newPlayer = 0,
    newGame  = 0,
    agentWithdrawRequest = 0,
    affiliateWithdrawRequest = 0,
    selfExclusionRequest = 0,
    new_player_attachment_count = 0,
    siteUrl = '<?=site_url()?>',
    checkNotificationTimer = null
;


$(function() {

    runAutoCheckNotification(0);

});

function runAutoCheckNotification(timer){
    if(!!checkNotificationTimer){
        clearTimeout(checkNotificationTimer);
    }
    timer = (typeof timer !== "undefined") ? timer : <?=($this->config->item('transaction_request_notification_timeout') ? : 30) * 1000?>;

    checkNotificationTimer = setTimeout(function(){
        transactionRequestNotification(function(){
            runAutoCheckNotification();
        });
    }, timer);
}

function transactionRequestNotification(callback) {

  $.getJSON('/api/transaction_request_notification', function(data) {

    var ring = '/resources/third_party/lobibox/sounds/sound3.ogg',
        body = '',
        total_request = data.total_request;

    <?php if ($this->utils->isEnabledFeature('notification_promo')): ?>
        if ( onloadEventHandler > 0 && data.promo > promo ) {
            $('.promo-count, .promo-count-dropdown').addClass('blink');
            $('.promo-count, .promo-count-dropdown').html(data.promo);
             if( data.notification[1] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[1];
            ringSound.src = ring;
            ringSound.play();
            body += "<?=lang('lang.promo')?>: " + data.promo + "\n";
        } else {
            $('.promo-count, .promo-count-dropdown').removeClass('blink');
            $('.promo-count, .promo-count-dropdown').html(data.promo);
        }
    <?php endif?>

    <?php if ($this->utils->isEnabledFeature('notification_messages')): ?>
        if ( onloadEventHandler > 0 && data.messages > msg_count ) {
            $('.message-count, .message-count-dropdown').addClass('blink');
            $('.message-count, .message-count-dropdown').html(data.messages);
            if( data.notification[2] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[2];
            ringSound.src = ring;
            ringSound.play();
            body += "<?=lang('cs.messages')?>: " + data.messages + "\n";
        } else {
            $('.message-count, .message-count-dropdown').removeClass('blink');
            $('.message-count, .message-count-dropdown').html(data.messages);
        }
    <?php endif?>

    if ( onloadEventHandler > 0 && data.local_deposit > local_deposit ) {
        $('.deposit-count-offline, .deposit-count-offline-dropdown').addClass('blink');
        $('.deposit-count-offline, .deposit-count-offline-dropdown').html(data.local_deposit);
         if( data.notification[3] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[3];
        ringSound.src = ring;
        ringSound.play();
        body += "<?=lang('Local Deposit')?>: " + data.local_deposit + "\n";
    } else {
        $('.deposit-count-offline, .deposit-count-offline-dropdown').removeClass('blink');
        $('.deposit-count-offline, .deposit-count-offline-dropdown').html(data.local_deposit);
    }

    if ( onloadEventHandler > 0 && data.thrdpartydeposit > thrdpartydeposit ) {
        $('.deposit-count-thrdparty, .deposit-count-thrdparty-dropdown').addClass('blink');
        $('.deposit-count-thrdparty, .deposit-count-thrdparty-dropdown').html(data.thrdpartydeposit);
        if( data.notification[4] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[4];
        ringSound.src = ring;
        ringSound.play();
        body += "<?=lang('3rdParty Deposit')?>: " + data.thrdpartydeposit + "\n";
    } else {
        $('.deposit-count-thrdparty, .deposit-count-thrdparty-dropdown').removeClass('blink');
        $('.deposit-count-thrdparty, .deposit-count-thrdparty-dropdown').html(data.thrdpartydeposit);
    }

    if ( onloadEventHandler > 0 && data.withdrawal_request > withdraw ) {
        $('.withdraw-count, .withdraw-count-dropdown').addClass('blink');
        $('.withdraw-count, .withdraw-count-dropdown').html(data.withdrawal_request);
        if( data.notification[5] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[5];
        ringSound.src = ring;
        ringSound.play();
        body += "<?=lang('pay.withreqst')?>: " + data.withdrawal_request;
    } else {
        $('.withdraw-count, .withdraw-count-dropdown').removeClass('blink');
        $('.withdraw-count, .withdraw-count-dropdown').html(data.withdrawal_request);
    }

    <?php if ($this->utils->isEnabledFeature('notification_new_player')): ?>
        if ( onloadEventHandler > 0 && data.new_player > newPlayer ) {
            $('.new-player-count, .new-player-count-dropdown').addClass('blink');
            $('.new-player-count, .new-player-count-dropdown').html(data.new_player);
            if( data.notification[6] != undefined ) ring = siteUrl + 'upload/notifications/' + data.notification[6];
            ringSound.src = ring;
            ringSound.play();
            body += "<?= lang('report.sum05') ?>: " + data.new_player;
        } else {
            $('.new-player-count, .new-player-count-dropdown').removeClass('blink');
            $('.new-player-count, .new-player-count-dropdown').html(data.new_player);
        }
    <?php endif?>

    <?php if ($this->utils->isEnabledFeature('show_new_games_on_top_bar')): ?>
        if ( onloadEventHandler > 0 && data.new_games > newGame ) {
            $('.newgame-count-dropdown').addClass('blink');
            $('.newgame-count-dropdown').html(data.new_games);
            ringSound.src = ring;
            body += "<?=lang('New Games')?>: " + data.new_games;
        } else {
            $('.newgame-count-dropdown').removeClass('blink');
            $('.newgame-count-dropdown').html(data.new_games);
        }
    <?php endif?>

    <?php if($this->utils->isEnabledFeature('responsible_gaming')): ?>
        if ( onloadEventHandler > 0 && data.self_exclusion_request > selfExclusionRequest ) {
          $('.selfExclusionRequest-count-dropdown').addClass('blink');
          $('.selfExclusionRequest-count-dropdown').html(data.self_exclusion_request);
          ringSound.src = ring;
          body += "<?=lang('Self Exclusion')?>: " + data.self_exclusion_request;
        } else {
          $('.selfExclusionRequest-count-dropdown').removeClass('blink');
          $('.selfExclusionRequest-count-dropdown').html(data.self_exclusion_request);
        }
    <?php endif;?>

    <?php /* if($this->utils->isEnabledFeature('attached_file_list'))
        if ( onloadEventHandler > 0 && data.new_player_attachment_count > new_player_attachment_count ) {
          $('.new_player_attachment-count-dropdown').addClass('blink');
          $('.new_player_attachment-count-dropdown').html(data.new_player_attachment_count);
          ringSound.src = ring;
          body += "<?=lang('Player Attachments')?>: " + data.new_player_attachment_count;
        } else {
          $('.new_player_attachment-count-dropdown').removeClass('blink');
          $('.new_player_attachment-count-dropdown').html(data.new_player_attachment_count);
        }
    */ ?>

    <?php if($this->utils->isEnabledFeature('notify_agent_withdraw')): ?>
        if ( onloadEventHandler > 0 && data.agent_withdraw_request > agentWithdrawRequest ) {
          $('.agentWithdrawRequest-count-dropdown').addClass('blink');
          $('.agentWithdrawRequest-count-dropdown').html(data.agent_withdraw_request);
          ringSound.src = ring;
          body += "<?=lang('Agency Withdraw')?>: " + data.agent_withdraw_request;
        } else {
          $('.agentWithdrawRequest-count-dropdown').removeClass('blink');
          $('.agentWithdrawRequest-count-dropdown').html(data.agent_withdraw_request);
        }
    <?php endif;?>

    <?php if($this->utils->isEnabledFeature('notify_affiliate_withdraw')): ?>
        if ( onloadEventHandler > 0 && data.affiliate_withdraw_request > affiliateWithdrawRequest ) {
          $('.affiliateWithdrawRequest-count-dropdown').addClass('blink');
          $('.affiliateWithdrawRequest-count-dropdown').html(data.affiliate_withdraw_request);
          ringSound.src = ring;
          body += "<?=lang('Affiliate Withdraw')?>: " + data.affiliate_withdraw_request;
        } else {
          $('.affiliateWithdrawRequest-count-dropdown').removeClass('blink');
          $('.affiliateWithdrawRequest-count-dropdown').html(data.affiliate_withdraw_request);
        }
    <?php endif;?>

    var thrdPartyCount = $('.approved_thirdparty_count');

    if( onloadEventHandler > 0 && total_request > parseInt(thrdPartyCount.html()) ){
        thrdPartyCount.addClass('blink');
    }else{
        thrdPartyCount.removeClass('blink');
    }
    thrdPartyCount.html(total_request);

    if( onloadEventHandler > 0 && body != '' ){

        ringSound.play();

        var n = new Notification("<?=lang('Notifications')?>", {
                body: body,
                tag: 'transaction_request_notification',
                <?php if (isset($logo_icon)) {  ?>
                    icon: "<?=$this->utils->imageUrl($logo_icon)?>",
                <?php } else {  ?>
                    icon: "<?=$this->utils->getDefaultLogoUrl()?>",
                <?php } ?>
              }).onclick = function(event) {
                window.focus();
              }

    }

    promo = data.promo;
    msg_count = data.messages;
    local_deposit = data.local_deposit;
    thrdpartydeposit = data.thrdpartydeposit;
    withdraw = data.withdrawal_request;
    thrdpartySettled = data.thrdpartySettled;
    cashback_request = data.cashback_request;
    newPlayer = data.new_player;
    selfExclusionRequest = data.self_exclusion_request;
    new_player_attachment_count = data.new_player_attachment_count;
    agentWithdrawRequest = data.new_plaagent_withdraw_requestyer;
    affiliateWithdrawRequest = data.affiliate_withdraw_request;

    //OGP-15305 Hide order count of the side menu: SBE_Payment
    // var total_deposit_request = parseInt(local_deposit) + parseInt(thrdpartydeposit);
    // $('.deposit_count_sidebar').html(total_deposit_request);

    onloadEventHandler = 1;

    if(typeof callback === "function") callback();
  });
}


    function isChrome(){
        var result=false;
        var ua=window.navigator.userAgent;
        if(ua!=''){
            var arr=ua.split(' ');
            for (var i = 0; i < arr.length; i++) {
                if(arr[i].indexOf('Chrome') !== -1){
                    //found Chrome
                    var tmpArr=arr[i].split('/');
                    if(tmpArr.length>=2){
                        var verArr=tmpArr[1].split('.');
                        if(verArr.length>1){
                            result=parseInt(verArr[0], 10)>=69;
                        }
                    }
                }
            }
        }

        return result;
    }

<?php
$langKeyList=['Changing Currency', 'Change Currency Failed'];
?>
(function(){
    <?php foreach ($langKeyList as $key) { ?>
    _pubutils.lang['<?=$key?>']="<?=lang($key)?>";
    <?php }?>
})();

</script>
