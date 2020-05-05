<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-tagsinput/2.3.2/dist/bootstrap-tagsinput.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-tagsinput/2.3.2/dist/bootstrap-tagsinput.min.js')?>"></script>

<script type="text/javascript">
(function(){
    var SBE_UI_TAGSINPUT_DEFAULT_OPTIONS = {
        tagClass: function(item) {
            return 'label label-info';
        },
        itemValue: function(item) {
            return (typeof item === 'string') ? item : item['id'];
        },
        itemText: function(item) {
            return (typeof item === 'string') ? item : item['text'];
        },
        itemTitle: function(item) {
            return null;
        },
        freeInput: true,
        addOnBlur: true,
        maxTags: undefined,
        maxChars: undefined,
        confirmKeys: [13, 44],
        delimiter: ',',
        delimiterRegex: null,
        cancelConfirmKeysOnEmpty: true,
        onTagExists: function(item, $tag) {
            $tag.hide().fadeIn();
        },
        trimValue: false,
        allowDuplicates: false
    };

    $(document).ready(function(){
        $('[sbe-ui-toogle=tagsinput]').each(function(){
            var options = $.extend({}, SBE_UI_TAGSINPUT_DEFAULT_OPTIONS);

            var data = $(this).data();

            $.each(SBE_UI_TAGSINPUT_DEFAULT_OPTIONS, function(key, value){
                if(data.hasOwnProperty(key.toLowerCase())){
                    options[key] = data[key.toLowerCase()];
                }
            });
            $(this).tagsinput(options);

            $(this).on('itemAdded', function(event) {
                // event.item: contains the item
                // event.cancel: set to true to prevent the item getting added

                var tagsinput = $(this).data('tagsinput');
                tagsinput.$container.find('.tag').each(function(){
                    if($(this).text() !== event.item.text){
                        return true;
                    }
                    if(event.item.hasOwnProperty('color')){
                        $(this).css('background-color', event.item.color);
                    }
                    if(event.item.hasOwnProperty('extra-class')){
                        $(this).addClass(event.item['extra-class']);
                    }

                });
            });

            var $elt = $(this).tagsinput('input');
            if(!options['freeInput']){
                $elt.attr('readonly', 'readonly');
            }
        });
    });
})();
</script>