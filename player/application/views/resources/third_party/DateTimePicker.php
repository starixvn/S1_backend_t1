<?php
$DateTimePicker_func = function(){
    $currentLanguage = $this->language_function->getCurrentLanguage();

    $options = [
        'language' => 'zh-CN'
    ];
    switch($currentLanguage){
        case Language_function::INT_LANG_CHINESE:
            $options['language'] = 'zh-CN';
        break;
        case Language_function::INT_LANG_ENGLISH:
            $options['language'] = 'en';
        break;
        default:
            $options['language'] = 'en';
        break;
    }

    return $options;
};

$DateTimePicker = $DateTimePicker_func();
?>
<link rel="stylesheet" href="<?=$this->utils->getSystemUrl("player")?>/resources/third_party/DateTimePicker/0.1.38/dist/DateTimePicker.min.css" />
<script type="text/javascript" src="<?=$this->utils->getSystemUrl("player")?>/resources/third_party/DateTimePicker/0.1.38/dist/DateTimePicker.min.js"></script>
<script type="text/javascript" src="<?=$this->utils->getSystemUrl("player")?>/resources/third_party/DateTimePicker/0.1.38/dist/i18n/DateTimePicker-i18n-<?=$DateTimePicker['language']?>.js"></script>
<script type="text/javascript">
(function(){
    $.DateTimePicker.defaults.language = "<?=$DateTimePicker['language']?>";
})();
$(document).ready(function(){
    function replace_options_from_data(options, element){
        for(var name in options){
            var copy = element.data(name.toLowerCase());
            if(copy !== undefined){
                options[name] = copy;
            }
        }
        
        return options;
    }
    
    var DateTimePickerBoxOptions = {
		mode: "date",
		defaultDate: null,
	
		dateSeparator: "-",
		timeSeparator: ":",
		timeMeridiemSeparator: " ",
		dateTimeSeparator: " ",
		monthYearSeparator: " ",
	
        dateFormat: "yyyy-MM-dd",
        timeFormat: "hh:mm AA",
        dateTimeFormat: "yyyy-MM-dd hh:mm:ss AA",
	
		maxDate: null,
		minDate:  null,
	
		maxTime: null,
		minTime: null,
	
		maxDateTime: null,
		minDateTime: null,
	
		shortDayNames: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		fullDayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        shortMonthNames: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
        fullMonthNames: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
//		shortMonthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
//		fullMonthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		labels: null, /*{"year": "Year", "month": "Month", "day": "Day", "hour": "Hour", "minutes": "Minutes", "seconds": "Seconds", "meridiem": "Meridiem"}*/

		minuteInterval: 1,
		roundOffMinutes: true,

		secondsInterval: 1,
		roundOffSeconds: true,
	
		showHeader: false,
		titleContentDate: "Set Date",
		titleContentTime: "Set Time",
		titleContentDateTime: "Set Date & Time",
	
		buttonsToDisplay: ["HeaderCloseButton", "SetButton", "ClearButton"],
		setButtonContent: "<?=lang('lang.apply')?>",
		clearButtonContent: "<?=lang('lang.clear')?>",
    	incrementButtonContent: "+",
    	decrementButtonContent: "-",
		setValueInTextboxOnEveryClick: false,
		readonlyInputs: true,
	
		animationDuration: 400,

		touchHoldInterval: 300, // in Milliseconds
		captureTouchHold: false, // capture Touch Hold Event

		mouseHoldInterval: 50, // in Milliseconds
		captureMouseHold: false, // capture Mouse Hold Event
	
		isPopup: false,
		parentElement: "body",

		isInline: false,
		inputElement: null,

		language: "<?=$DateTimePicker['language']?>",
	
		init: null, // init(oDateTimePicker)
		addEventHandlers: null,  // addEventHandlers(oDateTimePicker)
		beforeShow: null,  // beforeShow(oInputElement)
		afterShow: null,  // afterShow(oInputElement)
		beforeHide: null,  // beforeHide(oInputElement)
		afterHide: null,  // afterHide(oInputElement)
		buttonClicked: null,  // buttonClicked(sButtonType, oInputElement) where sButtonType = "SET"|"CLEAR"|"CANCEL"|"TAB"
		settingValueOfElement: null, // settingValueOfElement(sValue, dDateTime, oInputElement)
		formatHumanDate: null,  // formatHumanDate(oDateTime, sMode, sFormat)
	
		parseDateTimeString: null, // parseDateTimeString(sDateTime, sMode, sFormat, oInputField)
		formatDateTimeString: null // formatDateTimeString(oDateTime, sMode, sFormat, oInputField)
    };
    
    $('.datetimepicker-date').each(function(){
        $(this).attr('data-field', 'date');

        var DateTimePickerBoxContainer = $('<div data-name="DateTimePickerBoxContainer">');
        var DateTimePickerBox = DateTimePickerBoxContainer.DateTimePicker(replace_options_from_data($.extend({
            parentElement: $(this).parent()
        }, DateTimePickerBoxOptions), $(this)));
        $('body').append(DateTimePickerBoxContainer);
    });
    $('.datetimepicker-time').each(function(){
        $(this).attr('data-field', 'time');

        var DateTimePickerBoxContainer = $('<div>');
        var DateTimePickerBox = DateTimePickerBoxContainer.DateTimePicker(replace_options_from_data($.extend({
            parentElement: $(this).parent()
        }, DateTimePickerBoxOptions), $(this)));
        $('body').append(DateTimePickerBoxContainer);
    });
    $('.datetimepicker-datetime').each(function(){
        $(this).attr('data-field', 'datetime');

        var DateTimePickerBoxContainer = $('<div>');
        var DateTimePickerBox = DateTimePickerBoxContainer.DateTimePicker(replace_options_from_data($.extend({
            parentElement: $(this).parent()
        }, DateTimePickerBoxOptions), $(this)));
        $('body').append(DateTimePickerBoxContainer);
    });

//    var DateTimePickerBoxContainer = $('<div>');
//    var DateTimePickerBox = DateTimePickerBoxContainer.DateTimePicker($.extend({
//    }, DateTimePickerBoxOptions));
//    $('body').append(DateTimePickerBoxContainer);
});
</script>