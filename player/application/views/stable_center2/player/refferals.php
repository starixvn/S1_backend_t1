<div id="refferals" class="panel friend-referral">
    <div class="panel-heading">
        <h1 class="hidden-xs hidden-sm"><?= lang("Refer a Friend") ?></h1>
    </div>
    <div class="panel-body">
        <div class="clearfix refferal-url">
            <div class="col-md-12">
                <p><strong><?= lang("Referral URL") ?> :</strong></p>
            </div>
            <div class="col-md-10">
                <p id="referral-link" class="referral-code-content"><?= $this->utils->getSystemUrl('player').'/player_center/iframe_register?referralcode='.$player['invitationCode'] ?></p>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="return PlayerRefferals.pcCopyToclipboard('referral-link');"><?= lang('Copy') ?></a>
            </div>
        </div>
        <?php if(!$this->utils->isEnabledFeature('hidden_referFriend_referralcode')):?>
        <div class="clearfix refferal-code">
            <div class="col-md-12">
                <p><strong><?= lang("Referral Code") ?> :</strong></p>
            </div>
            <div class="col-md-10">
                <p id="referral-code" class="referral-code-content"><?= $player['invitationCode'] ?></p>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-info btn-sm" data-target="test" onclick="return PlayerRefferals.pcCopyToclipboard('referral-code');"><?= lang('Copy') ?></a>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-refferals.js') ?>"></script>
<script type="text/javascript">
	$(function(){
		PlayerRefferals.msg_success_copy = '<?= lang('Successfully copied to clipboard') ?>';
	});
</script>