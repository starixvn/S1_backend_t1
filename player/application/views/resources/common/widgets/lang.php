<script type="text/javascript">
(function(window){
    var LANG = <?=json_encode($lang)?>;
    
    function lang(lang_id){
        return (LANG.hasOwnProperty(lang_id)) ? LANG[lang_id] : lang_id;
    }
    
    if(!window.hasOwnProperty('lang')){
        window.lang = lang;
    }
})(window);
</script>