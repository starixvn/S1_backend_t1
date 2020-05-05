                    <th class="test"><?=lang('player.ug01');?></th>
                    <th class="test"><?=lang('Player Username');?></th>
                    <th class="test"><?=lang('Affiliate Username');?></th>
                    <th><?=lang('cms.gameprovider');?></th>
                    <th><?=lang('cms.gametype');?></th>
                    <th><?=lang('cms.gamename');?></th>
                    <th><?=lang('cms.betAmount');?></th>
                    <th><?=lang('mark.resultAmount');?></th>
                    <th><?=lang('lang.bet.plus.result');?></th>
                    <th><?php echo lang('Win Amount'); ?></th>
                    <th><?php echo lang('Loss Amount'); ?></th>
                    <th><?=lang('mark.afterBalance');?></th>
                    <th><?=lang('pay.transamount');?></th>
                    <th><?php echo lang('Round No'); ?></th>
                    <th><?=lang('player.ut12');?></th>
                    <?php if($this->utils->getPlayerCenterTemplate() == 'webet'):  ?>
                    <th class="hideThisColumnFromWebet"><?=lang('player.ut10');?></th>
                    <?php else: ?>
                     <th ><?=lang('player.ut10');?></th>
                    <?php endif; ?>
                     <th><?=lang('Actions');?></th>
