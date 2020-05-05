<?php

/**
 *
 * Lock server
 *
 * for locking resource
 *
 */
class Lock_server
{
    private $retryDelay;
    private $retryCount;
    private $clockDriftFactor = 0.01;

    private $quorum;

    private $servers = array();
    private $instances = array();

    private $prefix;

    function __construct(array $servers)
    {
        $retryDelay = 3000; $retryCount = 20;
        $this->servers = $servers;

        $this->retryDelay = $retryDelay;
        $this->retryCount = $retryCount;

        $this->prefix=try_get_prefix();

        $this->quorum  = min(count($servers), (count($servers) / 2 + 1));

        log_message('debug', 'lock server create prefix:'.$this->prefix, ['servers'=>$servers, 'retryDelay'=>$retryDelay, 'retryCount'=>$retryCount]);
    }

    public function setRetryDelay($retryDelay){
        $this->retryDelay = $retryDelay;
    }

    public function setRetryCount($retryCount){
        $this->retryCount = $retryCount;
    }

    public function setPrefix($prefix){
        $this->prefix=$prefix;
    }

    public function lock($resource, $ttl, $add_prefix=true)
    {

        if($add_prefix){
            $resource=$this->prefix.'-'.$resource;
        }

        log_message('debug', 'lock resource', ['resource'=>$resource, 'ttl'=>$ttl,
            'retryDelay'=>$this->retryDelay, 'retryCount'=>$this->retryCount]);

        $this->initInstances();

        $token = uniqid().'-'.time();
        $retry = $this->retryCount;

        do {
            $n = 0;

            $startTime = microtime(true) * 1000;

            foreach ($this->instances as $instance) {
                if ($this->lockInstance($instance, $resource, $token, $ttl)) {
                    $n++;
                }
            }

            # Add 2 milliseconds to the drift to account for Redis expires
            # precision, which is 1 millisecond, plus 1 millisecond min drift
            # for small TTLs.
            $drift = ($ttl * $this->clockDriftFactor) + 2;

            $validityTime = $ttl - (microtime(true) * 1000 - $startTime) - $drift;

            if ($n >= $this->quorum && $validityTime > 0) {
                return [
                    'validity' => $validityTime,
                    'resource' => $resource,
                    'token'    => $token,
                    'add_prefix' => $add_prefix,
                ];

            } else {
                log_message('debug', 'resource: '.$resource.' , $validityTime:'.$validityTime.', token: '.$token.' , ttl:'.$ttl);
                foreach ($this->instances as $instance) {
                    $this->unlockInstance($instance, $resource, $token);
                }
            }

            // Wait a random delay before to retry
            $delay = mt_rand(floor($this->retryDelay / 2), $this->retryDelay);
            usleep($delay * 1000);

            $retry--;

        } while ($retry > 0);

        return false;
    }

    public function unlock(array $lock)
    {
        if(empty($lock)){
            return false;
        }
        $this->initInstances();
        $resource = $lock['resource'];
        $token    = $lock['token'];

        foreach ($this->instances as $instance) {
            $this->unlockInstance($instance, $resource, $token);
        }

        return true;
    }

    private function initInstances()
    {
        if (empty($this->instances)) {
            foreach ($this->servers as $server) {
                list($host, $port, $timeout) = $server;
                $redis = new \Redis();
                $redis->connect($host, $port, $timeout);

                $this->instances[] = $redis;
            }
        }
    }

    private function lockInstance($instance, $resource, $token, $ttl)
    {
        try{
            return $instance->set($resource, $token, ['NX', 'PX' => $ttl]);
        }catch(RedisException $e){
            log_message('error', 'redis error on lock' ,['exception'=>$e]);
        }

        return false;
    }

    private function unlockInstance($instance, $resource, $token)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        try{
            // return $instance->del($resource);
            return $instance->eval($script, [$resource, $token], 1);
        }catch(RedisException $e){
            log_message('error', 'redis error on unlock' ,['exception'=>$e]);
        }

        return false;
    }
}