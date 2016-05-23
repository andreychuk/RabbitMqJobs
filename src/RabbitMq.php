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
    /** @var AMQPChannel */
    protected $channel;
    protected $queue;
    protected $exchange;

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

    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

    public function getExchange()
    {
        return $this->exchange;
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
    
    protected function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->host, $this->port, $this->user, $this->password
        );

        $this->setChannel($this->connection->channel());

        $this->channel->queue_declare($this->queue);
        $this->channel->exchange_declare($this->exchange, 'direct');
        $this->channel->queue_bind($this->queue, $this->exchange);

        var_dump($this->queue, $this->exchange);
    }
    
    /**
     * @param AMQPChannel $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param $sec
     *
     * @return string
     */
    protected function generateExchangeQueue($sec)
    {
        $queue = sprintf("%s.%s", $this->queue, $sec);
        return $queue;
    }
}
