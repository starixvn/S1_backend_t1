<?php

use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class BetterAmqpHandler extends Monolog\Handler\AbstractProcessingHandler{

    /**
     * @var AMQPExchange|AMQPChannel $exchange
     */
    protected $channel;

    /**
     * @var string
     */
    protected $exchangeName;

    /**
     * @param AMQPExchange|AMQPChannel $exchange     AMQPExchange (php AMQP ext) or PHP AMQP lib channel, ready for use
     * @param string                   $exchangeName
     * @param int                      $level
     * @param bool                     $bubble       Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($channel, $exchangeName, $routingKey='', $level = Logger::DEBUG, $bubble = true)
    {
        if (! $channel instanceof AMQPChannel) {
            throw new \InvalidArgumentException('PhpAmqpLib\Channel\AMQPChannel instance required');
        }

        $this->routingKey = $routingKey;
        $this->channel = $channel;
        $this->exchangeName=$exchangeName;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $data = $record["formatted"];
        // $routingKey = $this->getRoutingKey($record);

		// raw_debug_log('write to rabbitmq', $this->exchangeName, count($data));

        $this->channel->basic_publish(
            $this->createAmqpMessage($data),
            $this->exchangeName,
            $this->routingKey
        );
    }

    /**
     * {@inheritDoc}
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            $record = $this->processRecord($record);
            $data = $this->getFormatter()->format($record);

            $this->channel->batch_basic_publish(
                $this->createAmqpMessage($data),
                $this->exchangeName,
                $this->routingKey
            );
        }

        $this->channel->publish_batch();
    }

    // protected function getRoutingKey(array $record){
    //     $routingKey = sprintf(
    //         '%s.%s',
    //         $record['level_name'],
    //         $record['channel']
    //     );

    //     return strtolower($routingKey);
    // }

    /**
     * @param  string      $data
     * @return AMQPMessage
     */
    private function createAmqpMessage($data)
    {
        return new AMQPMessage(
            (string) $data,
            array(
                'delivery_mode' => 2,
                'content_type' => 'application/json',
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new BetterJsonFormatter();
    }

}
