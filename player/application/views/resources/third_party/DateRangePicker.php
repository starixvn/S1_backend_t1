<link href="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.css')?>" rel="stylesheet">
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js') ?>"></script>
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js') ?>"></script>

<script type="text/javascript">
    var daterangepicker_default_attrs = {
        "showDropdowns": true,
        "alwaysShowCalendars": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "timePickerSeconds": true,
        // "opens": "left",
        "applyClass": "btn-primary",
        "locale": {
            "separator": " <?=lang('player.12')?> ",
            "applyLabel": " <?=lang('lang.apply')?> ",
            "cancelLabel": " <?=lang('lang.cancel')?> ",
            "daysOfWeek": ["<?=lang('Sun')?>","<?=lang('Mon')?>","<?=lang('Tue')?>","<?=lang('Wed')?>","<?=lang('Thu')?>","<?=lang('Fri')?>","<?=lang('Sat')?>"],
            "monthNames": ["<?=lang('January')?>","<?=lang('February')?>","<?=lang('March')?>","<?=lang('April')?>","<?=lang('May')?>","<?=lang('June')?>","<?=lang('July')?>","<?=lang('August')?>","<?=lang('September')?>","<?=lang('October')?>","<?=lang('November')?>","<?=lang('December')?>"],
            "firstDay": 0,
            "format": "YYYY-MM-DD HH:mm:ss",
            "customRangeLabel": "<?=lang('lang.custom')?>"
        },
        "ranges": {
            '<?=lang('dt.yesterday')?>': [moment().subtract(1,'days').startOf('day'), moment().subtract(1,'days').endOf('day')],
            '<?=lang('dt.lastweek')?>': [moment().subtract(1,'weeks').startOf('isoWeek'), moment().subtract(1,'weeks').endOf('isoWeek')],
            '<?=lang('lang.today')?>': [moment().startOf('day'), moment().endOf('day')],
            '<?=lang('cms.thisWeek')?>': [moment().startOf('isoWeek'), moment().endOf('day')],
            '<?=lang('cms.thisMonth')?>': [moment().startOf('month'), moment().endOf('day')]
        }
    };
</script>