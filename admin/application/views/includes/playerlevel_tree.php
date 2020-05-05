<?php
if (!isset($promoRuleSelectedGroup)) {
	$promoRuleSelectedGroup = array();
}
if (!isset($promoRuleSelectedPlayerLevels)) {
	$promoRuleSelectedPlayerLevels = array();
}
$this->utils->debug_log('promoRuleSelectedGroup promoRuleSelectedPlayerLevels', $promoRuleSelectedGroup, $promoRuleSelectedPlayerLevels);

$showPlayerLevelInTree = $this->config->item('show_player_level_in_tree');
$playerLevel = $this->utils->getPlayerLvlTree($showPlayerLevelInTree);
$this->utils->debug_log('player lv tree', $playerLevel);
?>
<div class="tree">
    <ul id="playerLvltree">
    	<li><a style='text-decoration:none;'><input type="checkbox" name="selectedAll[]" /><?php echo lang('lang.selectall') ?></a>
    	<ul>
<?php
foreach ($playerLevel as $gpId => $playerLvlInfo) {

	$gl_status = !empty($promoRuleSelectedGroup) && in_array($gpId, $promoRuleSelectedGroup) ? "checked" : "";
	?>
        <li><a style='text-decoration:none;'><input type="checkbox" class="playerlvl" name="selectedGroup[]" <?=$gl_status?> data-id="<?=$gpId;?>"/><?=$playerLvlInfo['groupName']?></a>
            <ul>
<?php
foreach ($playerLvlInfo['playerLvlTree'] as $lvlInfo) {
		$lvlStatus = !empty($promoRuleSelectedPlayerLevels) && in_array($lvlInfo['playerLevelId'], $promoRuleSelectedPlayerLevels) ? "checked" : "";
		?>
                <li><a style='text-decoration:none;'><input type="checkbox" <?=$lvlStatus?> name="player_lvl[]" value="<?=$lvlInfo['playerLevelId'];?>" data-id="<?=$lvlInfo['playerLevelId'];?>" /><?=$lvlInfo['playerLevelName'];?></a>
                </li>
<?php
}
	?>
            </ul>
        </li>
    <?php }
?>
			</ul>
		</li>
    </ul>
</div>
