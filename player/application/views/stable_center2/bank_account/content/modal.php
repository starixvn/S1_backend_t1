<!-- View Bank Modal -->
<div class="modal fade player_bank_account_modal" id="view-bank-acc" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title f24" id="myModalLabel">
					<?=lang('pay.bankinfo')?>
				</h4>
			</div>

			<div class="modal-body">
			</div>

            <div class="modal-footer text-center">
                <div class="row">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?= lang("lang.close") ?></button>
                </div>
            </div>
		</div>
	</div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade player_bank_account_modal" id="edit-bank-acc" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
            <form id="EditBankAccountForm">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title f24" id="myModalLabel">
                        <?=lang('pay.06')?>
                    </h4>
                </div>
                <div class="modal-body">
                </div>

                <div class="modal-footer text-center">
                    <div class="row">
                        <button type="submit" class="btn btn-primary submit-edit-bank-account"><?= lang("lang.save") ?></button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= lang("lang.close") ?></button>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>

<!-- Add Bank Modal -->
<div class="modal fade player_bank_account_modal" id="add-bank-acc" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
            <form id="AddBankAccountForm">
                <?=$double_submit_hidden_field?>
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title f24" id="myModalLabel">
                        <?=lang('Add Bank Account')?>
                    </h4>
                </div>
                <div class="modal-body">
                </div>

                <div class="modal-footer text-center">
                    <div class="row">
                        <button type="submit" class="btn btn-primary submit-add-bank-account"><?= lang("lang.submit") ?></button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= lang("lang.close") ?></button>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>

<!-- Delete Bank Modal -->
<div class="modal fade player_bank_account_modal" id="delete-bank-acc" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title f24" id="myModalLabel">
					<?=lang('Do you want delete this bank account')?>
				</h4>
			</div>
			<div class="modal-body">
			</div>

            <div class="modal-footer text-center">
                <div class="row">
                    <button type="button" class="btn btn-primary submit-btn"><?= lang("lang.delete") ?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?= lang("lang.close") ?></button>
                </div>
            </div>
		</div>
	</div>
</div>