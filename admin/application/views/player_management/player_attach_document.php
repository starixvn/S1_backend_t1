<div class="modal-attach-proof">
	<?php if($this->utils->isEnabledFeature('show_kyc_status')) : ?>
		<a href="javascript:void(0);" class="view-kyc-btn" onclick="modal('/player_management/player_kyc/<?=$playerId?>','<?=lang('player kyc')?>');"><?=lang('View KYC Status')?></a>
	<?php endif; ?>

	<?php if(!empty($attachment_info)) : ?>
		<?php foreach ($attachment_info as $key => $value) :?>
			<form action="<?=site_url('player_management/uploadKYCPlayerImage/'.$playerId)?>" id="form_<?=$key?>"  method="post" enctype="multipart/form-data">
				<p class="title-docu"><?= lang($value['description'])?>:</p>
				<div class="panel panel-default attach-docu">
				    <div class="panel-body">
				        <div class="clearfix">
				            <div class="image-container">
				            	<?php if(!empty($value['img_file'])) : ?>
				            		<?php foreach ($value['img_file'] as $img_key => $img_value) : ?>
				            			<?php $classView = (count($value['img_file']) > 1) ? 'multi-img': 'single-img text-center' ; ?>
					            			<div class="<?= $classView ?>">
							                    <a href="javascript:void(0);" class="img_thumbnail" data-picid="<?= $img_value['id'] ?>" data-tag="<?=$value['tag']?>" data-playerid="<?= $playerId ?>">
													<img id="player_document_img" src="<?=$img_value['file_name']?>" />
												</a>
							                    <input type="hidden" class="uploaded-by" value="<?=$img_value['uploaded_by']?>">
							                    <input type="hidden" class="visible_to_player" value="<?=$img_value['visible_to_player']?>">
							                    <input type="hidden" class="timestamp" value="<?=$img_value['created_at']?>">
							                </div>
						                <?php if ((count($value['img_file']) > 1) && (end($value['img_file'])['file_name'] == $img_value['file_name'])) : ?>
							                <?php for ($i=0; $i < (8 - count($value['img_file']) ); $i++): ?>
						            			<div class="multi-img">
						            				<?php if($i == 0) :?>
									                    <button type="button" class="add-image-btn" onclick="">
									                        <i class="fa fa-plus-square-o"></i>
									                    </button>
								                    <?php endif; ?>
								                </div>
						            		<?php endfor; ?>
					            		<?php endif; ?>
				            		<?php endforeach; ?>
				            	<?php else: ?>
				            		<div class="single-img text-center">
					                    <button type="button" class="add-image-btn" onclick="">
					                        <i class="fa fa-plus-square-o"></i>
					                        <span><?= lang('Upload Image') ?></span>
					                    </button>
					                </div>
				            	<?php endif; ?>
				            </div>
				            <div class="image-description clearfix">
				                <label class="custom-file-upload <?=(count($value['img_file']) >= $limit_of_upload_attachment || (isset($value['activeStatusInfo']['verification']) && $value['activeStatusInfo']['verification'] == 'verified')) ? 'hidden' : ''?>" title="<?=sprintf(lang('kyc_attachment.upload_file_max_up_to'),$limit_of_upload_attachment)?>">
				                    <input type="file" id="txtImage" name="txtImage[]" hidden onchange="return PlayerAttachDocument.getFileData(this);" <?= (!$this->permissions->checkPermissions('update_attached_documents') || (count($value['img_file']) >= $limit_of_upload_attachment)) ? 'disabled' : ''?>/>
				                    <input type="hidden" id="player_id" name="player_id" value="<?php echo $playerId?>">
				                    <i class="fa fa-cloud-upload"></i> <span><?= lang('Upload Image') ?></span>
				                </label>
				                <input type="hidden" class="hidden-remarks" id="remarks" name="remarks" value="<?= isset($value['activeStatusInfo']['verification']) ? $value['activeStatusInfo']['verification'] : 'no_attach'?>">
				                <select onchange="return PlayerAttachDocument.InitRemarks(this);" <?= (empty($value['img_file']) || !$this->permissions->checkPermissions('update_attached_documents')) ? 'disabled' : ''?> >
				                    <?php if(!empty($value['verificationList'])): ?>
					                    <?php foreach ($value['verificationList'] as $verificationList_key => $verificationList_value) : ?>
					                    <?php $selectedRemarks = (isset($verificationList_value['active']) && ($verificationList_value['active'])) ?>
					                    	<option value="<?= $verificationList_key ?>" <?php echo ($selectedRemarks) ? "selected" : ""; ?> ><?= lang($verificationList_value['description']) ?></option>
					                    <?php endforeach; ?>
				                    <?php endif; ?>
				                </select>
				                <input type="hidden" id="tag" name="tag" value="<?=$value['tag']?>">
								<textarea class="hidden-comments hidden" id="comments" name="comments" ><?php if( isset($value['activeStatusInfo']['comments']) ): ?><?=$value['activeStatusInfo']['comments']?><?php endif ?></textarea>
				                <textarea id="txt_comments_<?=$key?>" placeholder="Comment" onchange="return PlayerAttachDocument.InitComments(this);" <?= (!$this->permissions->checkPermissions('update_attached_documents')) ? 'disabled' : ''?>><?= isset($value['activeStatusInfo']['comments']) ? $value['activeStatusInfo']['comments'] : ''?></textarea>
				            </div>
				        </div>
                        <?php if($value['description'] == "Photo ID"):?>
                            <div class="kyc_id_card_number">
                                <?=sprintf(lang('formvalidation.isset'), lang('ID Card Number'))?>
                                <?php if(isset($value['activeStatusInfo']['verification']) && $value['activeStatusInfo']['verification'] == 'verified'):?>
                                    <input type="text" class="form-control" name="id_card_number" value="<?=isset($id_card_number)?$id_card_number:''?>" readonly disabled>
                                <?php else:?>
                                    <input type="text" class="form-control" name="id_card_number" value="<?=isset($id_card_number)?$id_card_number:''?>">
                                <?php endif;?>
                            </div>
                        <?php endif;?>

				        <?php if($this->permissions->checkPermissions('update_attached_documents')) : ?>
				        	<input type="submit" value="save" class="save-btn">
				        <?php endif; ?>
				    </div>
				</div>
			</form>
		<?php endforeach; ?>
	<?php endif; ?>
	<div id="overlay" class="overlay">
	    <a href="javascript:void(0)" class="closebtn" onclick="return PlayerAttachDocument.closeNav()">&times;</a>
	    <div class="img-info">
	    	<div>
	    		<label><?= lang("Uploaded By") ?>:</label>
	    		<span class="overlay-uploaded-by"></span>
	    	</div>
	    	<div>
	    		<label><?= lang("Timestamp") ?>:</label>
	    		<span class="overlay-timestamp"></span>
	    	</div>
	    </div>
	    <div class="img_container">
	        <img id="" src="" alt="<?= lang('Image Document')?>">
	    </div>
	    <div class="image-on-hover">
        	<?php if($this->permissions->checkPermissions('update_attached_documents')) : ?>
                <button type="button" class="delete-btn">
                    <i class="fa fa-trash-o"></i> <?= lang('Delete') ?>
                </button>

	            <button type="button" class="zoom-btn active visible-to-player">
	                <i class="fa fa-eye" aria-hidden="true"></i> <?= lang('Visible to player') ?>
	            </button>
	            <button type="button" class="zoom-btn not-visible-to-player">
	                <i class="fa fa-eye-slash" aria-hidden="true"></i> <?= lang('Not Visible to player') ?>
	            </button>
            <?php endif; ?>
        </div>
	</div>
</div>
<script type="text/javascript" src="<?=$this->utils->jsUrl('player_management/player_attach_document.js')?>"></script>
<script type="text/javascript">
	$(function(){
	    PlayerAttachDocument.current_kyc_status = "<?=$current_kyc_level.' / '.$current_kyc_status?>";
	    PlayerAttachDocument.allowed_withdrawal_status = "<?=$allowed_withdrawal_status?>";
	    PlayerAttachDocument.confirmed_delete = "<?=lang('Do you want to delete this document?')?>";
	    PlayerAttachDocument.confirmed_visible = "<?=lang('Do you want to set this document visible to player center?')?>";
	    PlayerAttachDocument.confirmed_not_visible = "<?=lang('Do you want to set this document not visible to player center?')?>";
	    PlayerAttachDocument.txt_comment = JSON.parse('<?=json_encode(array_keys($attachment_info))?>');
	    PlayerAttachDocument.init();
	});
</script>