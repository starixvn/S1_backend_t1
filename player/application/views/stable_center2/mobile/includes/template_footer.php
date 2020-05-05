<div class="bottom">
<!--     <div class="bottom_icon">
        <ul>
            <li><img src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/18_logo.png" class="img-circle"></li>
        </ul>
    </div>
 -->    <div class="bottom_text">
        <ul><?php if(!$this->utils->isEnabledFeature('disable_mobile_access_comp_link')):?>
                <a href="<?php echo $this->utils->getSystemUrl('www');?>" target="_blank"><?=lang('access.comp.version')?></a>
            <?php endif;?>
        </ul>
    </div>
    <p>© 2014-2017 All Rights Reserved</p>
</div>