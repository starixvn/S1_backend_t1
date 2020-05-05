<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Create Walletaccount Status History for withdrawal
 *
 * General behaviors include
 * * createWalletaccountStatusHistory
 *
 * @category Payment Model
 * @version 1.0.1
 * @copyright 2013-2018 TripleOneTech
 */

class Walletaccount_status_history extends BaseModel {

    const REQUEST_STATUS        = 'request';
    const PENDING_REVIEW_STATUS = 'pending_review';
    const PAY_PROC_STATUS       = 'payProc';
    const DECLINED_STATUS       = 'declined';
    const PAID_STATUS           = 'paid';

    //Withdraw status history
    //1.  Create
    const WITHDRAW_STATUS_REQUEST        = 'request';
    const WITHDRAW_STATUS_PENDING_REVIEW = 'pending_review';
    //2.  Submit
    const WITHDRAW_STATUS_SUBMIT_ORDER   = 200;
    //3.  Submit Return
    const WITHDRAW_STATUS_SUBMIT_SUCCESS = 300;
    const WITHDRAW_STATUS_SUBMIT_FAILED  = 301;
    //4.  Payment Process
    const WITHDRAW_STATUS_PAY_PROC       = 'payProc';

    //5.1.1 Get Callback
    const WITHDRAW_STATUS_GET_CALLBACK                        = 510;
    //5.1.2 Callback
    const WITHDRAW_STATUS_BEFORE_HANDLE_CALLBACK_EXPIRED      = 511;
    const WITHDRAW_STATUS_BEFORE_HANDLE_CALLBACK_API_DISABLED = 512;

    //5.1.3 Handle Callback
    const WITHDRAW_STATUS_HANDLE_CALLBACK_FAILED              = 513;
    //5.2.1 Check Status
    const WITHDRAW_STATUS_CHECK_STATUS_SUBMIT                 = 520;
    //5.2.2 Check Status Return
    const WITHDRAW_STATUS_CHECK_STATUS_SUBMIT_SUCCESS         = 520;
    const WITHDRAW_STATUS_CHECK_STATUS_SUBMIT_FAILED          = 521;

    //6.  Settled
    const WITHDRAW_STATUS_PAID           = 'paid';
    const WITHDRAW_STATUS_DECLINED       = 'declined';


	protected $tableName = 'walletaccount_status_history';

    /**
     * detail: creating new withdrawal history status
     *
     * @param string $walletAccountId order Id
     * @param int $status withdrawal history status
     *
     */
    public function createWalletaccountStatusHistory($walletAccountId, $status) {
        $old_status = $this->getWalletaccountOldStatusByWalletAccountId($walletAccountId);
        $old_status = empty($old_status) ? 'none' : $old_status;
        if($old_status == $status || $old_status == self::WITHDRAW_STATUS_PAID || $old_status == self::WITHDRAW_STATUS_DECLINED){
            return;
        }
        $this->sale_order->updateSaleOrderDetailStatusById($walletAccountId, $status);
        $this->utils->debug_log('========================createWithdrawalHistoryStatus params', 'walletAccountId: [' . $walletAccountId .']', 'old_status:['.$old_status.'] ,status: [' . $status . ']');
        $depositHistory = array(
            'walletAccountId' => $walletAccountId,
            'old_status' => $old_status,
            'status' => $status,
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql()
        );

        $this->db->insert($this->tableName, $depositHistory);
    }

    /**
     * detail: get order old status
     * @param int $walletAccountId wallet account id
     * @return array
     */
    public function getWalletaccountOldStatusByWalletAccountId($walletAccountId) {
        $this->db->select('status');
        $this->db->where('walletAccountId', $walletAccountId);
        $this->db->order_by('id', 'desc');
        $qry = $this->db->get($this->tableName, 1);
        return $this->getOneRowOneField($qry, 'status');
    }

}

/////end of file///////
