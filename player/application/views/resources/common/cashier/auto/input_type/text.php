<div class="from-group setup-deposit-amount">
    <div class="input_name_text"><?=$label;?></div>
    <div class="input_form">
		<input type="text" class="form-control" name="<?=$inputInfo['name'];?>" value="<?=$inputInfo['value'];?>"
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
    </div>
    <?php if(isset($inputInfo['hint'])){ ?>
        <div class="text-danger">
            <?=$inputInfo['hint']?>
        </div>
    <?php } ?>
    <div class="clear"></div>
</div>
