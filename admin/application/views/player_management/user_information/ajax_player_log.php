<div role="tabpanel" class="tab-pane active" id="playersLog">
    <div class="form-inline">
        <input type="hidden" value="<?=$player['playerId']?>" id="player_id">
        <input type="hidden" value="<?=$player['username']?>" id="username">
        <div style="position: relative;">
        <select id="player_info_dropdown" class="form-control input-sm">
            <?php if ($this->utils->isEnabledFeature('deposit_withdraw_transfer_list_on_player_info')): ?>
                <option data-load="/player_management/depositHistory/" data-callback="depositHistory" data-dt-selector="#deposit-table" data-params='{"player_id":<?=$player_id?>}'><?=lang('Deposit List'); ?></option>
                <option data-load="/player_management/withdrawHistory/" data-callback="withdrawHistory" data-dt-selector="#withdraw-table" data-params='{"player_id":<?=$player_id?>}'><?=lang('Withdraw List'); ?></option>
                <option data-load="/player_management/transferHistory/<?=$player_id?>" data-callback="transferHistory" data-dt-selector="#transfer-table" data-params='{"player_id":<?=$player_id?>}'><?=lang('Transfer List'); ?></option>
            <?php endif ?>
            <option data-load="/player_management/balance_history/" data-callback="balance_history" data-params='{"player_id":<?=$player_id?>}'><?=lang('Balance History'); ?></option>
            <?php if (!$this->utils->isEnabledFeature('hide_old_adjustment_history')): ?>
                <option data-load="/player_management/adjustment_history_tab/" data-callback="adjustmentHistory" data-dt-selector="#adjustment_history" data-params='{"player_id":<?=$player_id?>}'><?=lang('Old Adjustment History')?></option>
            <?php endif ?>
            <option data-load="/player_management/adjustment_history_tab_v2/" data-callback="adjustmentHistoryV2" data-dt-selector="#adjustment_history_tab" data-params='{"player_id":<?=$player_id?>}'><?=lang('pay.adjustHistory')?></option>
            <?php if ($this->permissions->checkPermissions('transaction_report')): ?>
                <option data-load="/player_management/transactionHistory/" data-callback="transactionHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#transaction-table"><?=lang('pay.transact')?></option>
            <?php endif ?>
            <?php if ($this->permissions->checkPermissions('view_player_update_history')): ?>
                <option data-load="/player_management/personalHistory/<?=$player_id?>" data-callback="personalHistory" data-params='{"player_id":<?=$player_id?>}' ><?=lang('member.playerUpdateHistory')?></option>
            <?php endif ?>
            <option data-load="/player_management/bankHistory/" data-callback="bankHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#bank-table" ><?=lang('player.ui41')?></option>
            <option data-load="/player_management/promoStatus/" data-callback="promoStatus" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#promo-table"><?=lang('player.ui49')?></option>
            <?php if ($this->permissions->checkPermissions('gamelogs')): ?>
                <option data-load="/player_management/gamesHistory" data-callback="gamesHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#gamehistory-table"><?=lang('player.ui48')?></option>
                <option data-load="/player_management/gamesHistory/2" data-callback="gamesHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#gamehistory-table"><?=lang('Unsettle Game History'); ?></option>
            <?php endif; ?>
            <option data-load="/player_management/friendReferralStatus/" data-callback="friendReferralStatus" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#friendreferral-table"><?=lang('player.ui46')?></option>
            <option data-load="/player_management/chatHistory/" data-callback="chatHistory" data-dt-selector="#chatHistory-table" data-params='{"player_id":<?=$player_id?>}'><?=lang('cs.messagehistory')?></option>
            <option data-load="/player_management/ipHistory/" data-callback="ipHistory" data-dt-selector="#ip-table" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui75')?></option>
            <option data-load="/player_management/dupAccounts/" data-callback="dupAccounts" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#dup-table"><?=lang('pay.duplicateAccountList')?></option>
            <?php if ($this->utils->isEnabledFeature('linked_account') && $this->permissions->checkPermissions('linked_account')): ?>
                <option data-load="/player_management/linked_account/<?=$player_id?>" data-callback="linkedAccount" data-params='{"player_id":<?=$player_id?>}'><?=lang('Linked Account')?></option>
            <?php endif ?>
            <option data-load="/player_management/cancelled_withdrawal/" data-callback="cancelledWithdrawalCondition" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#cancelledwithdrawal-table"><?=lang('Withdrawal Condition History')?></option>
            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')):?>
                <option data-load="/player_management/cancelledTransferCondition/" data-callback="cancelledTransferCondition" data-params='{"player_id":<?=$player_id?>}'><?=lang('Transfer Condition History')?></option>
            <?php endif;?>
            <?php if ($this->utils->isEnabledFeature('show_kyc_status') && $this->permissions->checkPermissions('view_kyc_history')):?>
                <option data-load="/player_management/kyc_history/" data-callback="kycHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#kycHistory-table"><?=lang('KYC History')?></option>
            <?php endif ?>
            <?php if ($this->utils->isEnabledFeature('show_risk_score') && $this->permissions->checkPermissions('view_risk_score_history')):?>
                <option data-load="/player_management/risk_score_history/" data-callback="riskScoreHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Risk Score History')?></option>
            <?php endif ?>
            <option data-load="/player_management/rgHistory/" data-callback="rgHistory" data-params='{"player_id":<?=$player_id?>}' data-dt-selector="#responsible-gaming-table"><?=lang('Responsible Gaming History Log')?></option>
            <?php if ($this->permissions->checkPermissions('player_communication_preference') && $this->utils->isEnabledFeature('enable_communication_preferences')): ?>
                <option data-load="/player_management/communicationPreferenceHistory/" data-callback="communicationPreferenceHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Communication Preference History')?></option>
            <?php endif ?>
        </select>
        </div>
    </div>
    <hr>

    <div id="changeable_table"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('#player_info_dropdown').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonClass: 'form-control',
            maxHeight: 300,
        });

        var isEnable_column_visibility_report = false;
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            isEnable_column_visibility_report = true;
        <?php } ?>

        var theOptions = {};
        theOptions.isEnable_column_visibility_report = isEnable_column_visibility_report;
        changeableTable.initial(theOptions);
        changeableTable.onReady();

        changeableTable.appendInto('[id="duplicateAccountModal"].modal', 'footer');

        /// init UI By select:has(option[data-load]).
        $('#playersLog select:has(option[data-load])').trigger('change');


    });
</script>
