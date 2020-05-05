<?php
if(!isset($player_center_template)) { # To cover the case of /iframe_module/callback_success page
    $player_center_template = $this->utils->getPlayerCenterTemplate();
}
?>
<div class="container dashboar-container">
    <?php include __DIR__ . '/../../includes/overview.php';?>
    <div class="member-center row">
        <div class="col-md-3 mc-ul navigation-menu">
            <?php include VIEWPATH . '/resources/common/components/player_center_navigation.php';?>
        </div>

        <div class="col-md-12">
            <?= $main_content ?>
        </div>
    </div>
</div>