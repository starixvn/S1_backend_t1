<?php
// $promotion_rules=$this->utils->getConfig('promotion_rules');
// $enabled_request_without_check=$promotion_rules['enabled_request_without_check'];
?>
    	<?php
if ($this->session->userdata('result') == 'success') {
	?>
            <div class="alert alert-success alert-dismissible" id="alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?=$this->session->userdata('message')?>
                <span id="message"></span>
            </div>
        <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');

} elseif ($this->session->userdata('result') == 'danger') {
	?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?=$this->session->userdata('message')?>
            </div>
        <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');
} elseif ($this->session->userdata('result') == 'warning') {
	?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?=$this->session->userdata('message')?>
            </div>
        <?php
$this->session->unset_userdata('result');
	$this->session->unset_userdata('promoMessage');
}
?>

		<table class="table">
            <tbody>
        		<?php if ($promo_list) {
	?>
        			<?php
foreach ($promo_list as $promo_item) {
		?>
        				<tr>
        					<td align="left"><?php echo $promo_item['promoName'] ?></td>
        					<td align="center"><a tabindex="0" type="button" data-trigger="focus" role="button" class="btn btn-default btn-sm" data-toggle="popover" title="<?php echo $promo_item['promoName'] ?>" data-content="<?php echo htmlentities($promo_item['promoDetails'], ENT_QUOTES, 'utf-8') ?>"><?=lang('tool.cms05');?></a></td>
        					<td >
                            <td align="left">
                                <?php if ($promo_item['promo_code']) {?>
                                <a class="btn btn-sm btn-success" href="<?php echo $this->utils->getSystemUrl('player') . '/iframe_module/show_promo/' . @$promo_item['promo_code'] ?>">
                                    <?php echo lang('cms.promocode') . ': ' . @$promo_item['promo_code'] ?></td>
                                </a>
                                <?php }?>
                            </td>
                            <td align="right">
        						<a href="javascript:void(0)" onclick="<?php if (!$promo_item['disabled']) {?>requestPromo(<?php echo $promo_item['promoCmsSettingId']; ?>)<?php } else {?>disabledPromo()<?php }?>" class="btn btn-primary btn-sm" <?php if ($promo_item['disabled']): ?>disabled<?php endif?>><?=lang('promo.applyNow');?></a>
                            </td>
        				</tr>
        			<?php }
	?>
        		<?php } else {?>
        			<tr>
        				<td colspan="2" align="center"><?=lang('lang.norecord')?></td>
        			</tr>
        		<?php }
?>
            </tbody>

		</table>
        <a href="<?php echo site_url('iframe_module/iframe_viewCashier') ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>

<script type="text/javascript">
    $(function() {
        $('button[data-toggle="popover"]').popover({
            html: true,
            placement: 'bottom',
        });
        $('#btn-reload').click(function() {
           $(this).text("<?=lang('text.loading')?>").prop('disabled', true);
           location.reload();
        });
    });

    function requestPromo(promoCmsSettingId){
        if(confirm('<?php echo lang("confirm.request"); ?>')){
            //goto page
            window.location.href='<?=site_url("iframe_module/request_promo");?>/'+promoCmsSettingId;
        }
    }
    function preapplicationPromo(promoCmsSettingId){
        if(confirm('<?php echo lang("confirm.request"); ?>')){
            //goto page
            window.location.href='<?=site_url("iframe_module/preapplicationPromo");?>/'+promoCmsSettingId;
        }
    }

    function disabledPromo(){
        alert('<?php echo lang('Sorry, you cannot apply this promotion yet'); ?>');
    }

</script>
