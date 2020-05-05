function GetXmlHttpObject() {
	var xmlHttp = null;

	try {
		xmlHttp = new XMLHttpRequest();
	} catch (e) {
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	return xmlHttp;
}

var variables={
	debugLog: true
};

var utils={
	safelog:function(msg){
		// check exists console.log
		if(variables.debugLog && typeof(console)!='undefined' && console.log){
			console.log(msg);
		}
	}
};

function initDateInput(dateInput) {

	var isRange = (dateInput.data('start') && dateInput.data('end'));
	var isTime = dateInput.data('time');

	// START PREPARE ATTRIBUTES
	var attributes = {
		"showDropdowns": true,
        "alwaysShowCalendars": true,
		"opens": "left",
		"applyClass": "btn-primary",
		"locale": public_lang['datetime_picker']
	};

	if (isRange) {
		attributes['linkedCalendars'] = false;
		attributes['ranges'] = datetime_ranges;
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

function confirmDelete() {
	return confirm(public_lang['confirm.delete']);
}

// DATEINPUT
$( function() {
	$('.dateInput').each( function() {
		initDateInput($(this));
	});

	$('#lang_select').change(function(){
		var lang = $(this).val();

		$.ajax({
			'url' 		: base_url +'async/set_language/'+lang,
			'type' 		: 'GET',
			'dataType' 	: "json",
			'success' 	: function(data){
				if (data.status == 'success') {
					location.reload();
				}
			}
		});
	});

	if ($(".alert").length != 0) {
		$(".alert").delay(300).addClass("in");
		setTimeout(function(){
			$(".alert").fadeOut(2000);
		},5000);
	}

	//init csrf
    if(typeof csrf_token !== 'undefined' && csrf_token['hash']){
        //add meta to head
        $('head').append('<meta name="csrf-token" content="'+csrf_token['hash']+'">');

        //add to ajax
        $.ajaxSetup({
            headers: {
                'XCSRFTOKEN': csrf_token['hash']
            }
        });

        //wait all form ready
        $( document ).ready(function() {
            add_csrf_to_all_form();
        });
    }

	$.ajaxSetup({
		xhrFields: {
		  	withCredentials: true
		}
	});

});


function add_csrf_to_all_form(){
    //search name first
    $('form').each(function(idx, frm){
        // utils.safelog(item);
        if($(frm).find('[name='+csrf_token['name']+']')){
            //ignore it
            // utils.safelog('found csrf token');
        }else{
            $(frm).append("<input name='"+csrf_token['name']+"' type='hidden' value='"+csrf_token['hash']+"'>");
        }
    });
}


$( document ).ready(function() {

    $.each($('.navbar').find('li'), function() {
    	//console.log(window.location.pathname);
        $(this).toggleClass('active',$(this).find('a').attr('href') == window.location.pathname);
    });

});



