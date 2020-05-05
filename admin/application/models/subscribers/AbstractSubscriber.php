<?php

abstract class AbstractSubscriber extends BaseModel{

    public function __construct(){
        parent::__construct();
        $this->load->model(['queue_result']);
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public function appendResult(BaseEvent $event, $result, $done=false, $error=false){
        $this->queue_result->appendResult($event->getToken(), $result, $done, $error);
    }

    public function doneResult(BaseEvent $event, $result=null, $error=false,
            $download_filelink=null, array $extra=[]){
        $done=true;
        if(empty($result)){
            $result=['done'=>$this->utils->getNowForMysql()];
        }
        $token=$event->getToken();
        $this->queue_result->appendResult($token, $result, $done, $error);
        $success=!$error;
        $progress=100;
        $total=100;
        $message=null;
        $this->queue_result->updateFinalResult($token, $success, $message, $progress, $total, $done,
            $download_filelink, $extra);
    }

    public function runAsyncCommand(BaseEvent $event, $func, $appendParams=null){
        $token=$event->getToken();
        $is_blocked=false;
        //first param should be token
        $params=[$token];
        if(!empty($appendParams)){
            $params=array_values(array_merge($params, $appendParams));
        }
        $cmd=$this->utils->generateCommandLine($func, $params, $is_blocked);
        $return_var=pclose(popen($cmd, 'r'));
        $success=$return_var==0;
        if($success){
            $this->appendResult($event, ['starting'=>$success], false, false);
            //let command to finish it
        }else{
            //failed
            $this->doneResult($event, ['failed'=>true, 'error_code'=>$return_var], true);
        }
    }

}
