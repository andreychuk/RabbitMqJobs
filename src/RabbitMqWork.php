<?php

namespace Andreychuk\RabbitMq;

/**
 * Class RabbitMqWork
 *
 * @package Andreychuk\RabbitMq
 */
class RabbitMqWork extends RabbitMq
{
    public function work()
    {
        $this->connect();
        var_dump("worker", $this->queue);
        $this->channel->basic_consume(
            $this->queue, '', false, false, false, false, [$this, 'callback']
        );

        while (count($this->channel->callbacks) > 0) {
            $this->channel->wait();
        }

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
            $class->setUp();
            call_user_func_array([$class, 'perform'], $data['data']);
            $class->tearDown();
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
