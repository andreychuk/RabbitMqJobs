<?php

namespace Andreychuk\RabbitMq;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqQueue extends RabbitMq
{
    /**
     * @param       $class
     * @param array $args
     */
    public static function enqueue($class, $args = [])
    {
        $date = new \DateTime();
        echo "In enqueue", PHP_EOL;
        self::enqueueAt($class, $args, $date);
    }

    /**
     * @param staring   $class
     * @param array     $args
     * @param \DateTime $date
     */
    public static function enqueueAt($class, $args, $date)
    {

        self::connect();
        echo "in enqueueAt", PHP_EOL;

        $data['class'] = $class;
        $data['data'] = $args;
        $data = json_encode($data);
        $msg = new AMQPMessage($data, ['delivery_mode' => 2]);

        $now = new \DateTime();

        $sec = $date->getTimestamp() - $now->getTimestamp();
        if ($sec < 0) $sec = 0;
        $queue = self::generateExchangeQueue($sec);

        self::$channel->queue_declare(
            $queue,
            false,
            false,
            false,
            true,
            true,
            array(
                'x-message-ttl' => array('I', $sec*1000),
                "x-expires" => array("I", $sec*1000+1000),
                'x-dead-letter-exchange' => array('S', self::$exchange)
            )
        );

        self::$channel->exchange_declare($queue.'.exchange', 'direct');
        self::$channel->queue_bind($queue, $queue.'.exchange');
        self::$channel->basic_publish($msg, $queue.'.exchange');

        self::close();
        echo "DONE enqueueAt", PHP_EOL;
    }

}
