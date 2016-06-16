<?php

namespace Andreychuk\RabbitMq;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class RabbitMq
 *
 * @package Andreychuk\RabbitMq
 */
class RabbitMq
{
    /** @var  AMQPStreamConnection */
    protected static $connection;
    /** @var AMQPChannel */
    protected static $channel;
    protected static $queue;
    protected static $exchange = 'exchange';

    protected static $host;
    protected static $port;
    protected static $user;
    protected static $password;
    protected static $vhost = '/';

    public static function setBackend($host, $port, $user, $password,
        $vhost = '/', $queue = 'resque'
    ) {
        self::$host = $host;
        self::$port = $port;
        self::$user = $user;
        self::$password = $password;
        self::$vhost = $vhost;
        self::$queue = $queue;
    }


    protected static function connect()
    {
        self::$connection = new AMQPStreamConnection(
            self::$host, self::$port, self::$user, self::$password, self::$vhost
        );
        self::$channel = self::$connection->channel();

        self::$channel->queue_declare(self::$queue, false, true, false, false);
        self::$channel->exchange_declare(self::$exchange, 'direct', false, true, false);
        self::$channel->queue_bind(self::$queue, self::$exchange);
    }


    public static function close()
    {
        self::$channel->close();
        self::$connection->close();
    }

    /**
     * @param $sec
     *
     * @return string
     */
    protected static function generateExchangeQueue($sec)
    {
        $queue = sprintf("%s.%s", self::$queue, $sec);
        return $queue;
    }
}
