<div class="from-group">
    <label class="control-label"><?=$label;?></label>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu-input_type-list-<?=$inputInfo['name']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span><?=lang('Please Select')?></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-input_type-list-<?=$inputInfo['name']?>">
            <?php foreach($inputInfo['list'] as $key => $value): ?>
                <li data-text="<?=lang($value)?>" data-value="<?=$key?>">
                    <a href="javascript:void(0)" class="font-weight-bold"><strong><?=lang($value)?></strong></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" name="<?=$inputInfo['name']?>" class="field" value=""/>
    </div>
    <div class="clear"></div>
</div>