<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-plus"></i> <?=lang('notify.form')?></h3>
			</div>
			<div class="panel-body">
				<form action="<?=site_url('notification_management/' . $this->uri->segment(2))?>" id="form" method="post" autocomplete="off" novalidate="novalidate"  enctype="multipart/form-data" onsubmit="return formSubmit();">

					<div class="form-group required" aria-required="true">
						<label for="username" class="control-label"><?=lang('notify.title')?></label>
						<input name="title" id="title" class="form-control user-error" value="" required="required" aria-required="true" aria-invalid="true" type="text">
					</div>

					<div class="form-group required" aria-required="true">
						<label for="file" class="control-label"><?=lang('notify.file')?></label>
						<!--input type="file" name="file" /-->


						<span id="form-input-file-container">
                           <input required="" id="file" name="file" class="form-control input-sm" onchange="setURL(this.value);" value="" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                           <div class="bootstrap-filestyle input-group">
                           <span class="group-span-filestyle input-group-btn" tabindex="0">
                           		<label for="file" class="btn btn-default ">
                           			<span class="icon-span-filestyle glyphicon glyphicon-plus"></span>
                           			<span class="buttonText">Choose File</span>
                           		</label>
                           </span>
                           <input class="form-control " placeholder="No file chosen" disabled="" type="text" id="file_name"> </div>
                        </span>


						<!--span id="form-input-file-container">
                           <input required="" id="file" name="file" class="form-control input-sm" onchange="setURL(this.value);" value="" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                           <div class="bootstrap-filestyle input-group">
                           		<span class="group-span-filestyle input-group-btn" tabindex="0">
	                           		<label for="file" class="btn btn-default ">
	                           			<span class="icon-span-filestyle glyphicon glyphicon-plus"></span>
	                           			<span class="buttonText">Choose File</span>
	                           		</label>
                           		</span>
                           		<input class="form-control " placeholder="No file chosen" disabled="" type="text">
                           	</div>
                        </span-->
                        <span style="font-size: 12px; font-style:italic;">* <?=lang('notify.upload.note')?></span>
					</div>

					<span style="color: red;font-size: 12px;"><?=lang('notify.allfield.required')?></span>

				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<button type="submit" form="form" class="btn btn_submit <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><i class="fa fa-plus"></i> <?=lang('notify.submit')?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	function setURL(value) {
	    var val = value;
	    var res = value.split('.').pop();

	    var oFile = document.getElementById("file").files[0];

        if( oFile.size > <?=$max_ogg_file_size?> ){
        	$('#file').val('');
	     	return alert('<?=lang('notify.invalid.filesize')?>');
        }

	    if( res != 'ogg' ){
	     	$('#file').val('');
	     	return alert('<?=lang('notify.invalid.file')?>');
	    }

	   	document.getElementById('file_name').value = val;
	}

	function formSubmit(){

		if( $('#title').val() == "" || $('#file').val() == "" ) return false;


	}

</script>