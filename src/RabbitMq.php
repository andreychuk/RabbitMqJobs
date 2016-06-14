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
    protected static $exchange;

    protected static $host;
    protected static $port;
    protected static $user;
    protected static $password;
    protected static $vhost = '/';

    private static $_instance = null;

    public static function setBackend($host, $port, $user, $password,
        $vhost = '/', $queue = 'resque'
    ) {
        self::$host = $host;
        self::$port = $port;
        self::$user = $user;
        self::$password = $password;
        self::$vhost = $vhost;
        self::$queue = $queue;
        echo "setBackend Done", PHP_EOL;
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        echo "IN CONSTRUCT", PHP_EOL;
        self::$_instance = self::getInstance();
    }


    public function getParamsFromEnv()
    {
        $host = trim(getenv('RABBITMQ_HOST'), "'");
        $port = trim(getenv('RABBITMQ_PORT'), "'");
        $user = trim(getenv('RABBITMQ_DEFAULT_USER'), "'");
        $password = trim(getenv('RABBITMQ_DEFAULT_PASS'), "'");
        $queue = trim(getenv('RABBITMQ_QUEUE'), "'");

        $this->setHost($host);
        $this->setPort($port);
        $this->setUser($user);
        $this->setPassword($password);
        $this->setQueue($queue);
        $this->setExchange(sprintf("%s.exchange", $queue));
    }

    protected static function connect()
    {
        self::$connection = new AMQPStreamConnection(
            self::$host, self::$port, self::$user, self::$password, self::$vhost
        );
        self::$channel = self::$connection->channel();
        echo "In Connect", PHP_EOL;
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
