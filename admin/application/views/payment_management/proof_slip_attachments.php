<style>

.player_document_img{
    width: 144px;
    height: 96px;
    margin: 4px 0px;
}

.image-container{
	width: 33.33333333% !important;
}

.modal-attach-proof .panel-body{
    min-height: 500px;
}

.modal-attach-proof .overlay-proof {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 3;
    left: 0;
    top: 0;
    background-color: rgb(0,0,0);
    background-color: rgb(0,0,0,0.96);
    overflow-x: hidden;
    transition: 0.5s;
}

.modal-attach-proof .overlay-proof .img-info{
    margin-top: 10%;
    padding: 20px 20px 0 20px;
}
.modal-attach-proof .overlay-proof .img-info label{
    color: #c5c5c5;
    font-size: 14px;
}
.modal-attach-proof .overlay-proof .img-info span{
    color: #fff;
    margin-left: 10px;
}
.modal-attach-proof .overlay-proof a {
    padding: 5px;
    text-decoration: none;
    font-size: 20px;
    color: #818181;
    display: block;
    transition: 0.3s;
}
.modal-attach-proof .overlay-proof a:hover, .overlay-proof a:focus {
    color: #f1f1f1;
}
.modal-attach-proof .overlay-proof .closebtn {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 40px;
    height: 40px;
    width: 40px;
    padding: 0;
    text-align: center;
    line-height: 40px;
}
.modal-attach-proof .overlay-proof .img_container{
    position: relative;
    height: 400px;
    text-align: center;
    padding: 15px 0;
    background: rgba(0,0,0,1);
    margin-bottom: 15px;
}
.modal-attach-proof .overlay-proof .img_container img {
    height: 100%;
}	
</style>
<div class="modal-attach-proof">
	<?php if(!empty($attachment_info)) : ?>
		<div class="panel panel-default attach-docu">
		    <div class="panel-body">
		        <div class="clearfix">
		        	<?php foreach ($attachment_info as $key => $value) :?>
		        		<?php 
		        			 $admin = $this->users->selectUsersById($value['admin_user_id']);
		        		 ?>
		            <div class="image-container col-md-4">
		            	
		            	<?php if(!empty($value['file_name'])) : ?>
		            		<a href="javascript:void(0);" class="img_thumbnail" data-picid="<?= $value['id'] ?>" data-tag="<?=$value['tag']?>" data-playerid="<?= $playerId ?>">
							<img id="player_document_img" class="player_document_img img-thumbnail" src="<?php echo base_url(); ?>upload/player/<?php echo $value['file_name']?>"/>
							</a>
							<input type="hidden" class="uploaded-by" value="<?=$admin['username']?>">
		                    <input type="hidden" class="timestamp" value="<?=$value['created_at']?>">
		            	<?php endif; ?>
		            	
		            </div>
		            <?php endforeach; ?>
		        </div>
		    </div>
		</div>
	<?php endif; ?>

	<div id="overlay-proof" class="overlay-proof">
	    <a href="javascript:void(0)" class="closebtn" onclick="return ProofSlipAttachment.closeNav()">&times;</a>
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
	</div>
</div>


<script>
	var ProofSlipAttachment = {

    init: function(){
        var self = this;
        $('.img_thumbnail').click(function(){
            $src = $(this).find('img').attr("src");
            $(".overlay-proof").find(".img_container").find("img").attr("src",$src);
            $uploadedBy = $(this).parent().find(".uploaded-by").val();
            $(".overlay-proof").find(".img-info").find(".overlay-uploaded-by").html($uploadedBy);
            $dateUploaded = $(this).parent().find(".timestamp").val();
            $(".overlay-proof").find(".img-info").find(".overlay-timestamp").html($dateUploaded);
            self.openNav();
        });
    },

	openNav: function() {
		var self = this;
        document.getElementById("overlay-proof").style.width = "100%";
    },

    closeNav: function() {
		var self = this;
        document.getElementById("overlay-proof").style.width = "0%";
    },
}
</script>
<script>ProofSlipAttachment.init();</script>