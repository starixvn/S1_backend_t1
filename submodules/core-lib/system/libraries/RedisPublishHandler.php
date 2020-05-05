<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Logs to a Redis key using rpush
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs", "prod");
 *   $log->pushHandler($redis);
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */
class RedisPublishHandler extends AbstractProcessingHandler
{
    private $redisClient;
    private $redisKey;
    protected $capSize;
    private $host;
    private $port;
    private $timeout;
    private $retry_timeout;
    private $password;
    private $extra;

    private $online=false;
    private $tried=false;

    /**
     * @param \Predis\Client|\Redis $redis   The redis instance
     * @param string                $key     The key name to push records to
     * @param int                   $level   The minimum logging level at which this handler will be triggered
     * @param bool                  $bubble  Whether the messages that are handled can bubble up the stack or not
     * @param int                   $capSize Number of entries to limit list size to
     */
    public function __construct($host, $port, $timeout, $retry_timeout, $password, $key, $extra=null, $level = Logger::DEBUG, $bubble = true, $capSize = false)
    {
        // if (!(($redis instanceof \Predis\Client) || ($redis instanceof \Redis))) {
        //     throw new \InvalidArgumentException('Predis\Client or Redis instance required');
        // }

        $this->redisClient = new \Redis();
        $this->redisKey = $key;
        $this->capSize = $capSize;

        $this->host=$host;
        $this->port=$port;
        $this->timeout=$timeout;
        $this->retry_timeout=$retry_timeout;
        $this->password=$password;

        $this->extra=$extra;

        parent::__construct($level, $bubble);
    }

    protected function activeRedis(){
        $this->tried=true;
        if(!$this->online){
            try{
                $this->online=!!$this->redisClient->connect($this->host, $this->port, $this->timeout, null, $this->retry_timeout);
                if(!empty($this->password)){
                    $this->redisClient->auth($this->password);
                }

                if(isset($this->extra['enabled_debug_redis_logger']) && $this->extra['enabled_debug_redis_logger']){
                    raw_debug_log('connect to redis enabled_logger_file_channel', $this->host, $this->port, $this->timeout, $this->retry_timeout);
                }

            }catch(Exception $e){
                raw_debug_log($e, $this->host, $this->port);
                $this->online=false;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {

        if(!$this->tried){
            $this->activeRedis();
        }

        if(!$this->online){
            return;
        }

        // if ($this->capSize) {
        //     $this->writeCapped($record);
        // } else {
            //debug

            // $this->redisClient->rpush($this->redisKey.'-debug', $record["formatted"]);
            try{
                $rlt=$this->redisClient->publish($this->redisKey, $record["formatted"]);
                // $rlt=$this->redisClient->lpush($this->redisKey, $record["formatted"]);
                // $this->redisClient->lpush('debug-log', $record["formatted"]);
                // raw_debug_log('write to redis '.$this->redisKey, $record["formatted"], $rlt);

                $key=str_replace('-', '_', $this->redisKey);
                if(isset($this->extra['enabled_logger_file_channel']) && $this->extra['enabled_logger_file_channel']){
                    // raw_debug_log('write to redis enabled_logger_file_channel '.$key, $record["formatted"], $rlt);
                    $this->redisClient->publish($key, $record["formatted"]);
                }
                if(isset($this->extra['enabled_debug_redis_logger']) && $this->extra['enabled_debug_redis_logger']){
                    raw_debug_log('write to redis enabled_logger_file_channel '.$key, $rlt, $record["formatted"]);
                }

            }catch(Exception $e){
                raw_error_log('write to redis failed', $e);
            }

        // }
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     *
     * @param  array $record associative record array
     * @return void
     */
    // protected function writeCapped(array $record)
    // {
    //     if ($this->redisClient instanceof \Redis) {
    //         $this->redisClient->multi()
    //             ->rpush($this->redisKey, $record["formatted"])
    //             ->ltrim($this->redisKey, -$this->capSize, -1)
    //             ->exec();
    //     } else {
    //         $redisKey = $this->redisKey;
    //         $capSize = $this->capSize;
    //         $this->redisClient->transaction(function ($tx) use ($record, $redisKey, $capSize) {
    //             $tx->rpush($redisKey, $record["formatted"]);
    //             $tx->ltrim($redisKey, -$capSize, -1);
    //         });
    //     }
    // }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }

    public function close()
    {
        try{
            if($this->online){
                $this->redisClient->close();
            }
        }catch(Exception $e){
            //ignore
        }
    }

}
