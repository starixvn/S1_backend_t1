<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * Create Sale Orders Status History for deposit
 *
 * General behaviors include
 * * createDepositHistoryStatus
 *
 * @category Payment Model
 * @version 1.0.1
 * @copyright 2013-2018 TripleOneTech
 */

class Sale_orders_status_history extends BaseModel {

	protected $tableName = 'sale_orders_status_history';



    /**
     * detail: creating new deposit history status
     *
     * @param string $orderId order Id
     * @param int $status deposit history status
     *
     */
    public function createSaleOrderStatusHistory($orderId, $status)
    {
        $this->load->model('sale_order');
        $old_status = $this->getSaleOrderOldStatusByOrderId($orderId);
        $old_status = empty($old_status)?'0':$old_status;
        if($old_status == $status || $old_status == Sale_order::DEPOSIT_STATUS_APPROVE_SALE_ORDER){
            return;
        }
        $this->sale_order->updateSaleOrderDetailStatusById($orderId, $status);
        $this->utils->debug_log('========================createDepositHistoryStatus params', 'orderId: [' . $orderId .']', 'old_status:['.$old_status.'] ,status: [' . $status . ']');
        $depositHistory = array(
            'order_id' => $orderId,
            'old_status' => $old_status,
            'status' => $status,
            'created_at' => $this->utils->getNowForMysql(),
            'updated_at' => $this->utils->getNowForMysql()
        );

        $this->db->insert($this->tableName, $depositHistory);
    }

    /**
     * detail: get order old status
     * @param int $orderId sale order id
     * @return array
     */
    public function getSaleOrderOldStatusByOrderId($orderId)
    {
        $this->db->select('status');
        $this->db->where('order_id', $orderId);
        $this->db->order_by('id', 'desc');
        $qry = $this->db->get($this->tableName, 1);
        return $this->getOneRowOneField($qry, 'status');
    }

}

/////end of file///////
