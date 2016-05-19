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
    protected $connection;
    protected $chanel;
    protected $queue;

    private $host;
    private $port;
    private $user;
    private $password;

    public function __construct()
    {
        $this->getParamsFromEnv();
    }

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


    /**
     * @param $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
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
    }
    
    protected function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->host, $this->port, $this->user, $this->password
        );

        $this->setChanel($this->connection->channel());
    }

    /**
     * @param AMQPChannel $chanel
     */
    public function setChanel($chanel)
    {
        $this->chanel = $chanel;
    }

    /**
     * @return AMQPChannel
     */
    public function getChanel()
    {
        return $this->chanel;
    }

    public function close()
    {
        $this->chanel->close();
        $this->connection->close();
    }
}
