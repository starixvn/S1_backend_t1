<?php

use Symfony\Component\EventDispatcher\Event;

/**
 *
 * abstract event
 *
 */
class BaseEvent extends Event{

    protected $data;
    protected $eventName;
    protected $token;
    protected $queueResult=null;
    protected $error=false;

    public function __construct($token, $eventName, array $data){
        $this->token=$token;
        $this->data=$data;
        $this->eventName=$eventName;
        $this->extractData($data);
    }

    public function getToken(){
        return $this->token;
    }

    public function getData(){
        return $this->data;
    }

    public function getEventName(){
        return $this->eventName;
    }

    public function extractData($data){
        //convert $this->data to business data
    }

    /**
     * append this result to queue_result, it's optional
     * @param array $queueResult
     */
    public function setQueueResult(array $queueResult){
        $this->queueResult=$queueResult;
    }

    public function getQueueResult(){
        return $this->queueResult;
    }

    public function setError($error){
        $this->error=$error;
    }

    public function isError(){
        return $this->error;
    }

    public function toJson(){
        return ['token'=>$this->token, 'data'=>$this->data, 'event_name'=>$this->eventName];
    }
}
