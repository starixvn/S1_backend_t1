<?php

require_once dirname(__FILE__) . "/BaseEvent.php";

/**
 * for exporting csv file
 */
class ExportEvent extends BaseEvent{

    protected $conditions;
    protected $reportName;
    protected $exportingFunc;
    protected $exportingModel;

    public function extractData($data){
        $this->conditions=$data['conditions'];
        $this->reportName=$data['reportName'];
        $this->exportingFunc=$data['exportingFunc'];
        $this->exportingModel=$data['exportingModel'];
    }

    public function getConditions(){
        return $this->conditions;
    }

    public function getReportName(){
        return $this->reportName;
    }

    public function getExportingFunc(){
        return $this->exportingFunc;
    }

    public function getExportingModel(){
        return $this->exportingModel;
    }

}
