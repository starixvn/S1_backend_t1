<?php if($this->utils->isEnabledFeature('use_self_pick_promotion')): ?>
<div class="input_name_2 setup-deposit-promo">
    <div class="input_name_text"><?=lang('Select Promo')?></div>
    <div class="select_form col col-xs-12 col-sm-12 col-md-4 nopadding">
        <div class="dropdown setup-deposit-promo-dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="deposit_promo_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span><?=lang('Select Promo')?></span>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" aria-labelledby="deposit_promo_toggle">
                <li role="presentation" value=""><a href="javascript: void(0);" role="menuitem"><?=lang('Select Promo')?></a></li>
                <?php foreach($avail_promocms_list as $promoCmsSettingId => $promo_data){ ?>
                    <li role="presentation" value="<?=$promoCmsSettingId?>" disabled="disabled"><a href="javascript: void(0);" role="menuitem"><?=$promo_data?></a></li>
                <?php } ?>
            </ul>
        </div>
        <input type="hidden" id="promo_cms_id" name="promo_cms_id" class="field" value=""/>
    </div>
    <div class="col col-xs-12 col-sm-12 col-md-8">
        <button type="button" class="btn btn-primary hidden show-detail" onclick="show_promo_detail();"><?=lang('Show Details')?></button>
    </div>
    <div class="clearfix"></div>

    <div class="helper-content">
        <div class="promotion_select_description"></div>
    </div>
    <div class="clearfix"></div>

    <div class="help-block with-errors"></div>
</div>
<script>
var PROMO_LOADER_STR = "<?=lang('loader.auto_promotion')?>";
$(function(){
    $(".setup-deposit-amount input.form-control").on('change', function(){
        $(".setup-deposit-promo-dropdown .dropdown-menu li:first").trigger('click');

        $('.setup-deposit-promo-dropdown .dropdown-toggle').data('bs.dropdown', false);
        $('.setup-deposit-promo-dropdown .dropdown-toggle').removeAttr('data-toggle');
    });

    $('.setup-deposit-promo-dropdown .dropdown-toggle').on('click', function(e){
        var that = this;

        var is_processing = list_available_promotion(function(){
            if(!$(that).attr('data-toggle')){
                $(that).attr('data-toggle', 'dropdown');
                $(that).dropdown();
            }
            $(that).dropdown('toggle');
            return true;
        });

        if(!is_processing){
            if(!$(that).attr('data-toggle')){
                $(that).attr('data-toggle', 'dropdown');
                $(that).dropdown();
            }
            return true;
        }

        e.preventDefault();
        e.stopImmediatePropagation();
    });

    $(".setup-deposit-promo-dropdown .dropdown-menu li").on("click", function(){
        if($(this).attr('disabled')){
            return false;
        }

        var str = $(this).text();
        var val = $(this).attr('value');

        $(this).parent().parent().parent().find(".dropdown-toggle span:first").html(str);
        $(this).parent().parent().parent().find(".field").val(val);

        show_promotion_detail();
    });

    $('.setup-deposit-promo-dropdown .dropdown').on('show.bs.dropdown', function(e){
    })
});
</script>
<?php endif ?>