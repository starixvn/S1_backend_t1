$( function() {
    $('.dateInput').each( function() {
        initDateInput($(this));
    });
});

function initDateInput(dateInput) {

    var isRange = (dateInput.data('start') && dateInput.data('end'));
    var isTime = dateInput.data('time');

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

    if (isRange) {
        attributes['linkedCalendars'] = false;
        attributes['ranges'] = {
           '<?=lang('lang.today')?>': [moment().startOf('day'), moment().endOf('day')],
           '<?=lang('cms.thisWeek')?>': [moment().startOf('week'), moment().endOf('day')],
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