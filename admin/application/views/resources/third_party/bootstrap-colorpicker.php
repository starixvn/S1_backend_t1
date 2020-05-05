<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-colorpicker/2.5.1/dist/css/bootstrap-colorpicker.min.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-colorpicker/2.5.1/dist/js/bootstrap-colorpicker.min.js')?>"></script>
<style type="text/css">
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>
<script type="text/javascript">
(function(){
    $(document).ready(function(){
        $('[sbe-ui-toogle=colorpicker]').each(function(){
            var self = this;

            $(this).colorpicker($.extend({}, {
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            }, $(this).data()));

            var colorpicker_element = ($(this).hasClass('colorpicker-component')) ? $(this).find('input') : $(this);

            $(this).closest('form').on('reset', function(){
                $(self).colorpicker('setValue', colorpicker_element.attr('value'));
            });

            colorpicker_element.on('focus', function(){
                $(self).colorpicker('show');
            });
        });
    });
})();
</script>