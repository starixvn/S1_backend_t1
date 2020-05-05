<div class="from-group">
    <label class="control-label"><?=lang($inputInfo['label_lang']);?></label>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu-input_type-list-<?=$inputInfo['name']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span><?=lang($inputInfo['select_lang']);?></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu playerbank" aria-labelledby="dropdownMenu-input_type-list-<?=$inputInfo['name']?>">
            <?php foreach($inputInfo['list'] as $key => $rows): ?>
                <li data-text="<?=lang($rows['bankName']).' - '.$rows['bankAccountNumber']?>" data-value="<?=$rows['bankCode'].'-'.$rows['bankAccountNumber']?>">
                    <a href="javascript:void(0)" class="font-weight-bold"><strong><?=lang($rows['bankName']).' - '.$rows['bankAccountNumber']?></strong></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" name="<?=$inputInfo['name']?>" class="field" value=""/>
    </div>
    <div class="clear"></div>
</div>