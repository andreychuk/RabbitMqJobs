<?php

namespace Andreychuk\RabbitMq;

/**
 * Class RabbitMqWork
 *
 * @package Andreychuk\RabbitMq
 */
class RabbitMqWork extends RabbitMq
{
    public function __construct()
    {
        $this->getParamsFromEnv();
    }

    public function work()
    {
        $this->connect();
        $this->chanel->queue_declare($this->queue, false, true, false, false);

        $this->chanel->basic_qos(null, 1, null);
        $this->chanel->basic_consume(
            $this->queue, '', false, false, false, false, [$this, 'callback']
        );

        while (count($this->chanel->callbacks)) {
            $this->chanel->wait();
        }

        $this->close();
    }

    /**
     * @param AMQPMessage $msg
     *
     * @throws \Exception
     */
    public function callback($msg)
    {
        $data = json_decode($msg->body, true);

        try {
            $class = new $data['class']();
            call_user_func_array([$class, 'perform'], $data['data']);
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf(
                    "\n\rRabbitMQ Error in callback function\n\r%s",
                    $e->getMessage()
                )
            );
        }
        $msg->delivery_info['channel']->basic_ack(
            $msg->delivery_info['delivery_tag']
        );
    }
}
