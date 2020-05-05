<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="glyphicon glyphicon-hdd"></i> <?=lang("bak.backupManager")?>
                </h4>
            </div>

            <div class="panel-collapse collapse in">
                <div class="panel-body">
                    <div>

					  <!-- Nav tabs -->
					  <ul class="nav nav-tabs" role="tablist">
					    <li role="presentation" class="active"><a href="#restore" aria-controls="profile" role="tab" data-toggle="tab">Restore</a></li>
					    <li role="presentation" ><a href="#backup" aria-controls="home" role="tab" data-toggle="tab">Backup</a></li>
					  </ul>

					  <!-- Tab panes -->
					  <div class="tab-content">
					  	<div role="tabpanel" class="tab-pane active" id="restore">
					  		<?php if(isset($message)){?>
					  			<div class="row">
                    <?php if(isset($success)&&$success){?>
  						  		    <div class="alert col-md-5 alert-success" role="alert">
                        <span class="glyphicon glyphicon glyphicon-ok" aria-hidden="true"></span>
                    <?php }else{ ?>
                        <div class="alert col-md-5 alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                    <?php } ?>

  									 <?php echo $message; ?>
  									</div>
  								</div>
					    	<?php } ?>
							<?php echo form_open_multipart('backup_manager/backuprestore_manager');?>
							<input type="file" name="userfile" size="20" />
							<br /><br />
							<button type="submit" class="btn btn-primary btn-sm"  />upload</button>
							</form>
					    </div>
					    <div role="tabpanel" class="tab-pane" id="backup">
					    	<?php echo form_open_multipart('backup_manager/backuprestore_manager');?>
								<button type="submit" class="btn btn-primary btn-sm"  />backup</button>
							</form>
					    </div>
					  </div>

					</div>
                </div>
                <div class="panel-footer text-right">
                	<br>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    	$('.alert').fadeOut(5000);
    });
</script>
