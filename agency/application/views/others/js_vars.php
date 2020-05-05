<script type="text/javascript">
    var LANG_ALERT_INFO = "<?=lang('alert-info');?>";
    var LANG_ALERT_DANGER = "<?=lang('alert-danger')?>";
    var LANG_COPY_SUCCESS = "<?=lang('Successfully copied to clipboard');?>";
    var LANG_RANDOM_GERNERATE = "<?=lang('aff.ai38');?>";
    var LANG_AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER = "<?=lang('Player')?>";
    var LANG_AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT = "<?=lang('Agent')?>";

    var LANG_LABEL_SAVE = "<?=lang('Save')?>";
    var LANG_LABEL_CLOSE = "<?=lang('Close')?>";
    var LANG_LABEL_REMOVE = "<?=lang('Remove')?>";
    var LANG_LABEL_COPY = "<?=lang('Copy')?>";
    var LANG_LABEL_AGENT_SOURCE_CODE = "<?=lang('Agent Source Code')?>";
    var LANG_LABEL_AGENT_ADDITIONAL_DOMAIN = "<?=lang('Agent Additional Domain')?>";

    var SYSTEM_FEATURE_AGENT_TRACKING_CODE_NUMBERS_ONLY = <?=$this->utils->isEnabledFeature('agent_tracking_code_numbers_only') ? 'true' : 'false'?>;

    var AGENCY_TRACKING_CODE_RANDOM_GENERATE_LANGTH = 12;
    var AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER = <?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>;
    var AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT = <?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT?>;
</script>