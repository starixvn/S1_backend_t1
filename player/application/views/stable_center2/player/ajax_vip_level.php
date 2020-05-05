<?php
    
    if( isset( $on_top ) ){
?>
        <div class="vipmin" style="width: 100%;">
            <span class="vipstrv from_level"><?=$level_info->vipLevelName?></span>

        </div>
        <?php
            if( $playerCurrentLevel > 1 && $playerCurrentLevel >= $topLevelTarget ){
        ?>
                <div class="menu_text">
                    <label>祝贺你，你已经是最高级别了</label>
                </div>
        <?php
            }
        ?>
        
<?php
    }else{

        $progress = 0;

        if( isset( $deposit_amount ) ) $progress += $deposit_amount;
        if( isset( $bet_amount ) ) $progress += $bet_amount;

        $progress = round($progress);

?>
        
        <div class="vipmin">
            <span class="vipstrv from_level"><?=$from_level?></span>
        </div>

        <div class="vipline">
            <div id="level_progressbar" class="vipline_n" style="width:<?=$progress?>%"><?=$progress?></div>
        </div>

        <div class="vipmax">
            <span class="vipstrv to_level"><?=$to_level?></span>
        </div>

        <div class="menu_text">

            <?php
                if( isset( $deposit_amount ) ){
            ?>
                    <label>玩家必须在三天内存款</label>
                    <span style="font-size:14px;color:#f2970e;font-weight:600"><?=$upgrade_setting_deposit_amount?></span>
            <?php
                }
            ?>

            <?php
                if( isset( $deposit_amount ) ){
            ?>
                    <label>投注额达到</label>
                    <span style="font-size:14px;color:#f2970e;font-weight:600"><?=$upgrade_setting_bet_amount?></span>
            <?php
                }
            ?>

            <label>才可以升级到下一个级别</label>

        </div>

<?php
    }

?>
