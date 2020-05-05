<!--game list start-->
<?php
$actived_game_api_list=$this->utils->getAllActiveGameApiId();
?>
<div class="game_lobby row">
    <?php if($this->utils->isAnyEnabledApi([AGIN_API, FISHINGGAME_API])){ ?>
    <div class="tab_title">
        <?=lang('Fishing')?>
    </div>
    <hr class="dividen-line">
    <ul class="game_list w150">
    <?php if(in_array(AGIN_API, $actived_game_api_list)){ ?>
    <div class="game_item">
    <a href="<?=site_url('/player_center/goto_agingame/default/6')?>">
    <p><img src="<?=$this->utils->imageUrl('game-ag-hunter.png')?>"></p>
    <p class="game_text">AG 捕鱼王</p>
    </a>
    </div>
    <?php }?>
    <?php if(in_array(FISHINGGAME_API, $actived_game_api_list)){ ?>
    <div class="game_item">
    <a href="<?=site_url('/player_center/goto_fishinggame/61/101')?>">
    <p><img src="<?=$this->utils->imageUrl('game-gg.png')?>"></p>
    <p class="game_text">GG 捕鱼王</p>
    </a>
    </div>
    <?php }?>
    </ul>
    <hr class="dividen-line">
    <?php } ?>
    <?php if($this->utils->isAnyEnabledApi([IMPT_API, TTG_API, MG_API, SPADE_GAMING_API, PRAGMATICPLAY_API])){ ?>
    <div class="tab_title">
        <?=lang('Slots')?>
    </div>
    <hr class="dividen-line">
    <ul class="game_list w100">
    <?php if(in_array(IMPT_API, $actived_game_api_list)){ ?>
    <a href="<?=$this->utils->getSystemUrl('m', '/lobby-slots-impt.html')?>">
        <img src="<?=$this->utils->imageUrl('game-pt.png')?>">
    </a>
    <?php }?>
    <?php if(in_array(TTG_API, $actived_game_api_list)){ ?>
    <a href="<?=$this->utils->getSystemUrl('m', '/lobby-slots-ttg.html')?>">
        <img src="<?=$this->utils->imageUrl('game-ttg.png')?>">
    </a>
    <?php }?>
    <?php if(in_array(MG_API, $actived_game_api_list)){ ?>
    <a href="<?=$this->utils->getSystemUrl('m', '/lobby-slots-mg.html')?>">
        <img src="<?=$this->utils->imageUrl('game-mg.png')?>">
    </a>
    <?php }?>
    <?php if(in_array(SPADE_GAMING_API, $actived_game_api_list)){ ?>
    <a href="<?=$this->utils->getSystemUrl('m', '/lobby-slots-spade.html')?>">
        <img src="<?=$this->utils->imageUrl('game-spade.png')?>">
    </a>
    <?php }?>
    <?php if(in_array(PRAGMATICPLAY_API, $actived_game_api_list)){ ?>
    <a href="<?=$this->utils->getSystemUrl('m', '/lobby-slots-pragmatic.html')?>">
        <img src="<?=$this->utils->imageUrl('game-pragmatic.png')?>">
    </a>
    <?php }?>
    </ul>
    <hr class="dividen-line">
    <?php } ?>
    <?php if($this->utils->isAnyEnabledApi([AGIN_API, BBIN_API])){ ?>
    <div class="tab_title">
        <?=lang('Casino')?>
    </div>
    <hr class="dividen-line">
    <ul class="game_list w150">
    <?php if(in_array(AGIN_API, $actived_game_api_list)){ ?>
    <a href="<?=site_url('/player_center/goto_agingame/default/0')?>">
        <img src="<?=$this->utils->imageUrl('game-ag.png')?>">
    </a>
    <?php } ?>
    <?php if(in_array(BBIN_API, $actived_game_api_list)){ ?>
    <a href="<?=site_url('/player_center/goto_bbingame/3')?>">
        <img src="<?=$this->utils->imageUrl('game-bbin.png')?>">
    </a>
    <?php } ?>
    </ul>
    <hr class="dividen-line">
    <?php } ?>
    <?php if($this->utils->isAnyEnabledApi([VR_API])){ ?>
    <div class="tab_title">
        <?=lang('header.keno')?>
    </div>
    <hr class="dividen-line">
    <ul class="game_list w150">
    <?php if(in_array(VR_API, $actived_game_api_list)){ ?>
    <a href="<?=site_url('/player_center/goto_vrgame')?>">
        <img src="<?=$this->utils->imageUrl('game-vr.png')?>">
    </a>
    <?php } ?>
    </ul>
    <hr class="dividen-line">
    <?php }?>
</div>

<!--game list end-->