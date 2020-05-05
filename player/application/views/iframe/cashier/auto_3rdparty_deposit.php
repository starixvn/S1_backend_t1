<style type="text/css">
	.control-label required {
	}

	.notes-red {
	    color:  #ff0000;
	    padding: 0 15px;
	    font-size: 14px;
	}
</style>
<div class="row deposit-wrapper">
	<div class="deposit-list">
		<div class="list-group">
			<?php
				$this->load->view('iframe/cashier/deposit_sidebar');
			?>
		</div>
	</div>
	<div class="deposit-content custom-pdl-30">
        <?php include VIEWPATH . '/stable_center2/cashier/deposit/auto.php' ?>
	</div>
</div>
<div class="modal fade" id="waiting-modal" role='dialog'>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<?=lang('cashier.139')?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="finishedPayment();"><?=lang('cashier.140')?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal" onclick="chooseAnotherPayment();"><?=lang('cashier.141')?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
	function finishedPayment() {
		window.location.href = "<?=site_url('/iframe_module/iframe_viewCashier')?>";
	}

	function chooseAnotherPayment() {
		$('#btn-submit').removeClass('disabled');
	}

	function checkForm(){
		var rlt=document.getElementById('form-deposit').checkValidity();
		//check $("input[name=bank_type]")
		if($("input[name=bank_type]").length>0){
			rlt=$("input[name=bank_type]").val()!='' && $("input[name=bank_type]").val();
			if(!rlt){
				alert("<?php echo lang('error.choose.bank'); ?>");
			}
		}

		if(rlt){
			$('#waiting-modal').modal('show');
			$(this).addClass('disabled');
		}

		return rlt;
	}
</script>
