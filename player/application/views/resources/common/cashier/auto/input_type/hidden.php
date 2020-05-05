<div class="from-group">
    <input type="hidden" class="form-control" name="<?=$inputInfo['name']?>" value="<?=$inputInfo['value']?>"
    	<?php
			foreach($inputInfo as $key => $value) {
				if(strpos($key,'attr_') !== false) {
					$new_key = explode('attr_', $key);
					if($new_key[1] !== ''){
						echo $new_key[1].'='.$value.' ';
					}
				}
			}
		?>
    >
    <?php if(($inputInfo['name'] == "deposit_amount") && !empty($hint = $external_system_api->getAmountHint())) : ?>
        <div class="helper-content deposit_hint text-danger font-weight-bold">
            <p><?=$hint?></p>
        </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>