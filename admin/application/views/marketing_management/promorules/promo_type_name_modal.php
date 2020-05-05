<!-- Start promoName Modal -->
<div class="modal fade" id="promoNameModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
    <div class="modal-dialog" role="document">
        <input type="hidden" id="promoNameModalTarget">
        <div class="modal-content">
            <form role="form" id="form_promo_name">
                <div class="modal-header">
                    <h4 class="modal-title" id="promoNameLabel"><?=lang('cms.promoTypeName');?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="promo_name_english"><?=lang("lang.english.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_english" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.english.name"))?></span>
                            </div>
                            <div class="form-group">
                                <label for="promo_name_chinese"><?=lang("lang.chinese.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_chinese" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.chinese.name"))?></span>
                            </div>
                            <div class="form-group">
                                <label for="promo_name_indonesian"><?=lang("lang.indonesian.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_indonesian" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.indonesian.name"))?></span>
                            </div>
                            <div class="form-group">
                                <label for="promo_name_vietnamese"><?=lang("lang.vietnamese.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_vietnamese" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.vietnamese.name"))?></span>
                            </div>
                            <div class="form-group">
                                <label for="promo_name_korean"><?=lang("lang.korean.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_korean" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.korean.name"))?></span>
                            </div>
                            <div class="form-group">
                                <label for="promo_name_thai"><?=lang("lang.thai.name")?> </label>
                                <input type="text" class="form-control clear-fields" id="promo_name_thai" name="promo_name[]">
                                <span class="help-block hidden" style="color:#F04124"><?= sprintf(lang('formvalidation.required'),lang("lang.thai.name"))?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" >
                    <div style="height:70px;position:relative;">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('lang.close');?></button>
                        <button type="button" class="btn btn-primary"  onclick="return validatePromoName();"><?=lang('Done')?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End promoName Modal -->

<script type="text/javascript">
    var promo_name_language_fields_length = 6;
    function validatePromoName(){
        var form = $("#form_promo_name");
        var inputNames = form.find('input[name^="promo_name"]');
        var promoNames = {1:"",2:"",3:"",4:"",5:"",6:""};
        form.find('.hidden').length
        form.find('span').addClass("hidden");
        $("#form_promo_name input[type=text]").each(function(){
            if ( $(this).val().length == 0 ) {
                $(this).next().removeClass("hidden")
            }
        });

        if( form.find('.hidden').length == promo_name_language_fields_length ) {
            inputNames.each(function(index) {
                switch($(this).attr('id')) {
                    case "promo_name_english":
                        promoNames[1] = $(this).val();
                        break;
                    case "promo_name_chinese":
                        promoNames[2] = $(this).val();
                        break;
                    case "promo_name_indonesian":
                        promoNames[3] = $(this).val();
                        break;
                    case "promo_name_vietnamese":
                        promoNames[4] = $(this).val();
                        break;
                    case "promo_name_korean":
                        promoNames[5] = $(this).val();
                        break;
                    case "promo_name_thai":
                        promoNames[6] = $(this).val();
                        break;
                    default:
                        //console.log('unknown name!')
                }
            });

            $.each( promoNames, function( key, value ) {
                if(value == ""){
                    promoNames[key] = promoNames[1];
                }
            });

            var jsonPretty = '_json:'+JSON.stringify(promoNames);

            var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";
            var target =  $("#promoNameModalTarget").val();

            if( target === "promoTypeNameView" ){
                $("#promoTypeNameView").val(promoNames[currentLang]);
                $("#promoTypeName").val(jsonPretty);
            } else if( target === "editpromoTypeNameView" ) {
                $("#editpromoTypeNameView").val(promoNames[currentLang]);
                $("#editpromoTypeName").val(jsonPretty);
            }

            $('#promoNameModal').modal('hide');
        }
    }

    $(document).on("click","#editpromoTypeNameView",function(){
        $("#form_promo_name").find('span')
            .addClass("hidden")
            .find('span').text('');

        var inputNames = $('#editpromoTypeName').val();
        if( inputNames.indexOf("_json:") >= 0 ) {
            var langConvert = jQuery.parseJSON(inputNames.substring(6));
            $("#form_promo_name input[type=text]").each(function(index){
                $(this).val(langConvert[index+1]);
            });
        } else {
            $("#form_promo_name input[type=text]").val(inputNames);
        }
        $("#promoNameModalTarget").val('editpromoTypeNameView');
        $('#promoNameModal').modal('show');
    });

    $(document).on("click","#promoTypeNameView",function(){
        $("#form_promo_name").find('span').addClass("hidden");
        $("#promoNameModalTarget").val('promoTypeNameView');
        $('#promoNameModal').modal('show');
    });
</script>