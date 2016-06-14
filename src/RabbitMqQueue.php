<?php

namespace Andreychuk\RabbitMq;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqQueue extends RabbitMq
{
    /**
     * @param       $class
     * @param array $args
     */
    public function enqueue($class, $args = [])
    {
        $date = new \DateTime();
        $this->enqueueAt($class, $args, $date);
    }

    /**
     * @param staring   $class
     * @param array     $args
     * @param \DateTime $date
     */
    public function enqueueAt($class, $args, $date)
    {
        echo "test 1";
        $this->connect();
        echo "test 2";
        $data['class'] = $class;
        $data['data'] = $args;
        $data = json_encode($data);
        $msg = new AMQPMessage($data, ['delivery_mode' => 2]);

        $now = new \DateTime();

        $sec = $date->getTimestamp() - $now->getTimestamp();
        if ($sec < 0) $sec = 0;
        $queue = $this->generateExchangeQueue($sec);

        $this->channel->queue_declare(
            $queue,
            false,
            false,
            false,
            true,
            true,
            array(
                'x-message-ttl' => array('I', $sec*1000),
                "x-expires" => array("I", $sec*1000+1000),
                'x-dead-letter-exchange' => array('S', $this->exchange)
            )
        );

        $this->channel->exchange_declare($queue.'.exchange', 'direct');
        $this->channel->queue_bind($queue, $queue.'.exchange');
        $this->channel->basic_publish($msg, $queue.'.exchange');
    }

}
