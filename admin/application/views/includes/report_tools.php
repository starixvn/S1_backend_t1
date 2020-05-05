<a href="javascript:void(0);" class="copy-link btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> btn-xs pull-right" style="margin-right: 4px;" data-clipboard-text="<?php echo $this->utils->currentUrl();?>">
<i class="fa fa-clipboard"></i> <?php echo lang('Copy link'); ?>
</a>
<a href="javascript:void(0);" class="bookmark-this btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> btn-xs pull-right" style="margin-right: 4px;"><i class="fa fa-bookmark"></i> <?php echo lang('Add to bookmark'); ?></a>

<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('clipboard/clipboard.min.js')?>"></script>

<script type="text/javascript">
// new Clipboard('.btn');

$(function(){
	new Clipboard('.btn');
	// var url=window.location.href+'';
	// $("#current_link").val(url);
    $('.bookmark-this').click(_pubutils.addBookmark);

});

</script>
