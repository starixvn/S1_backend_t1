<?php
//$selectedGamePlatforms, $selectedGamesTypes, $selectedGameDescs
//POST: selectedGamePlatforms[], selectedGamesTypes[], selectedGameDescs[]
if (!isset($selectedGamePlatforms)) {
	$selectedGamePlatforms = array();
}
if (!isset($selectedGamesTypes)) {
	$selectedGamesTypes = array();
}
if (!isset($selectedGameDescs)) {
	$selectedGameDescs = array();
}

$showGameTree = $this->config->item('show_particular_game_in_tree');
$gamesTree = $this->utils->getGamesTree($showGameTree);
?>
<div class="tree">
    <ul id="treeAGT">
    <li><a style='text-decoration:none;'><input type="checkbox" name="selectedAll[]" /><?php echo lang('lang.selectall') ?></a>
    	<ul>
<?php
foreach ($gamesTree as $gpId => $gamePlatformInfo) {
	$gp_status = !empty($selectedGamePlatforms) && in_array($gpId, $selectedGamePlatforms) ? "checked" : "";
	?>
        <li><a style='text-decoration:none;'><input type="checkbox" name="selectedGamePlatforms[]" <?=$gp_status?> data-id="<?=$gpId;?>"/><?=$gamePlatformInfo['gamePlatformName']?></a>
            <ul>
<?php
// if ($gamePro['gameId'] == PT_API) {
	foreach ($gamePlatformInfo['gameTypeTree'] as $catId => $gameTypeInfo) {
		$gt_status = !empty($selectedGamesTypes) && in_array($catId, $selectedGamesTypes) ? "checked" : "";
		// if ($gamePro['gameId'] == $key['gameTypeId']) {
		//$gt_status = '';

		?>
                <li><a style='text-decoration:none;'><input type="checkbox" <?=$gt_status?> name="selectedGamesTypes[]" value="<?=$catId;?>" data-id="<?=$catId;?>" /><?=lang($gameTypeInfo['gameTypeLang']);?></a>
                    <ul>
<?php if ($showGameTree) {
			foreach ($gameTypeInfo['gameList'] as $gdId => $gameDescInfo) {
				$gm_status = !empty($selectedGameDescs) && in_array($gdId, $selectedGameDescs) ? "checked" : "";
				// if ($key['catId'] == $pt['gameType']) {
				?>
                        <li><input type="checkbox" name="selectedGameDescs[]" value="<?=$gdId;?>" <?=$gm_status?> data-id="<?=$gdId;?>" />
                            <span style="font-size:10px;"><?=lang($gameDescInfo['gameName'])?></span>&nbsp;
                        </li>
<?php
// }
			}
		}
		?>
                    </ul>
                </li>
<?php
// }
	}
// }
	?>
            </ul>
        </li>
    <?php }
?>
			</ul>
		</li>
    </ul>
</div>
