<div id="third_payment-input_type-bank" class="form-group">
    <label class="control-label"><?=lang('lang.bank');?></label>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown_menu-input_type-bank" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span><?=lang('Please Select Bank')?></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu bank" aria-labelledby="dropdown_menu-input_type-bank">
            <?php foreach($inputInfo['bank_list'] as $key => $value): ?>
                <li data-text="<?=lang($value)?>" data-value="<?=$key?>">
                    <a href="javascript:void(0)" class="font-weight-bold"><strong><?=lang($value)?></strong></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" name="bank" class="field" value="<?=$inputInfo['bank_list_default']?>"/>
    </div>
    <div class="clear"></div>
</div>

<div id="bank_type" class="form-group hide">
    <label class="control-label"><?=lang('Type');?></label>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown_menu-input_type-bank_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span><?=lang('cashier.81')?></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu bank_type" aria-labelledby="dropdown_menu-input_type-bank_type">
        </ul>
        <input type="hidden" name="bank_type" class="field" value=""/>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    var bankTree = <?=json_encode($inputInfo['bank_tree']);?>;

    function changeBankType(value){
        var bankShortCode = value;

        $('ul.bank_type').find('li').remove();

        if(!bankTree.hasOwnProperty(bankShortCode)) return false;

        var bankTypeList = bankTree[bankShortCode];

        if(bankTypeList.length <= 0) return;

        for(i = 0; i < bankTypeList.length; i++){
            var bank_type_name = bankTypeList[i]['bank_type_name'];
            var bankId = bankTypeList[i]['bank_id'];

            var html = '<li data-value="' + bankId + '"  data-text="' + bank_type_name + '">';
            html += '<a href="javascript:void(0)" >';
            html += '<strong>' + bank_type_name + '</strong>';
            html += '</a>';
            html += '</li>';

            $('.bank_type').append($(html));
        }

        $('#bank_type').removeClass('hide');
    }

    $('.dropdown-menu.bank').on('selected.t1t.dropdown', function(e, data){
        $('.dropdown-menu.bank_type').trigger('reset.t1t.dropdown');

        changeBankType(data.value);
    });
</script>