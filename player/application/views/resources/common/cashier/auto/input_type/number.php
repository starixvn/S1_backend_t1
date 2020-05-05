<div class="from-group setup-deposit-amount">
    <label class="control-label"><?=$label;?></label>
    <input type="number" class="form-control" name="<?=$inputInfo['name'];?>"
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
    <div class="clear"></div>
</div>