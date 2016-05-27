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
            if (class_exists($data['class'])) {
                $class = new $data['class']();
            } else {
                throw new \Exception(
                    'Could not find job class ' . $data['class']
                );
            }
            if (method_exists($class, 'setUp')) {
                $class->setUp();
            } else {
                throw new \Exception(
                    'Could not find method *setUp* in job class '
                    . $data['class']
                );
            }
            if (method_exists($class, 'setArgs')) {
                $class->setArgs($data['data']);
            } else {
                throw new \Exception(
                    'Could not find method *setArgs* in job class '
                    . $data['class']
                );
            }
            if (method_exists($class, 'perform')) {
                call_user_func_array([$class, 'perform'], $data['data']);
            } else {
                throw new \Exception(
                    'Could not find method *perform* in job class '
                    . $data['class']
                );
            }
            if (method_exists($class, 'tearDown')) {
                $class->tearDown();
            } else {
                throw new \Exception(
                    'Could not find method *tearDown* in job class '
                    . $data['class']
                );
            }
            $msg->delivery_info['channel']->basic_ack(
                $msg->delivery_info['delivery_tag']
            );
        } catch (\Exception $e) {
            var_dump(
                PHP_EOL . "RabbitMQ Error in callback function" . PHP_EOL,
                $e->getMessage()
            );
        }
    }
}
