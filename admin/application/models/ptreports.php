<?php

class Ptreports extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Will get pt api issue
     *
     * @param   limit int
     * @param   offset int
     * @return  array
     */
    function getPTApiIssue($limit, $offset) {

        if($limit != null) {
            $limit = "LIMIT " . $limit;
        }

        if($offset != null && $offset != 'undefined') {
            $offset = "OFFSET " . $offset;
        } else {
            $offset = ' ';
        }

        $query = $this->db->query("SELECT issuereportptapi.issueReportPtApiId,issuereportptapi.playerId,issuereportptapi.reportType,
                                        issuereportptapi.errorReturn,issuereportptapi.description,
                                        issuereportptapi.apiCallSyntax,issuereportptapi.errorTimeStamp,
                                        issuereportptapi.status,player.username
                                   FROM issuereportptapi
                                   LEFT JOIN player ON issuereportptapi.playerId = player.playerId
                                   WHERE issuereportptapi.status = 'unresolve'
                                   ORDER BY issuereportptapi.errorTimeStamp DESC $limit $offset");

        $result = $query->result_array();
        if(!$result) {
            return false;
        } else {
            return $result ;
        }
    }

    /**
     * resolve issue in pt
     *
     * @param $issueData array
     * @return  void
     */
    public function resolveApiIssueReport($issueData) {
        $this->db->where('issueReportPtApiId', $issueData['issueReportPtApiId']);
        $this->db->update('issuereportptapi', $issueData);

        if ($this->db->affected_rows() == '1'){
            return TRUE;
        }
        return FALSE;
    }
}