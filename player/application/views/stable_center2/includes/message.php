<script type="text/javascript">
    $(function(){
        MessageBox.setOptions({
            'header_title_success': lang('alert-success'),
            'header_title_info': lang('alert-info'),
            'header_title_warning': lang('alert-warning'),
            'header_title_danger': lang('alert-danger'),
            'close_button_text': lang('lang.close')
        });

        var type = (typeof MessageBox['<?=$type?>'] === "function") ? '<?=$type?>' : 'info';
        var title = '<?=$title?>';
        var message = '<?=$message?>';
        var redirect_url = '<?=(empty($redirect_url)) ? '/' : $redirect_url?>';

        MessageBox[type](message, title, function(){
            if(typeof window.top !== "undefined" && window.top !== window){
                Loader.show();
                window.top.location.href = redirect_url;
            }else{
                Loader.show();
                window.location.href = redirect_url;
            }
        });
    });
</script>