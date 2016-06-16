<?php

namespace Andreychuk\RabbitMq;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitMqWork
 *
 * @package Andreychuk\RabbitMq
 */
class RabbitMqWork extends RabbitMq
{
    public static function work()
    {

        self::connect();
        self::$channel->basic_consume(
            self::$queue, '', false, false, false, false,
            ['\Andreychuk\RabbitMq\RabbitMqWork', 'callback']
        );

        while (count(self::$channel->callbacks) > 0) {
            $pid = pcntl_fork();
            if ($pid === -1) {
                exit('failed to fork');
            } else {
                if ($pid === 0) {
                    self::$channel->wait();
                } else {
                    pcntl_wait($status);
                }
            }
        }

    }

    /**
     * @param AMQPMessage $msg
     *
     * @throws \Exception
     */
    public static function callback($msg)
    {

        try {
            $data = json_decode($msg->body, true);
            var_dump($data);
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

            $date = new \DateTime();
            $message = [
                'args' => $msg->body,
                'status' => $e->getMessage(),
                'date' => $date->format('c')
            ];
            $msgError = new AMQPMessage(json_encode($message));
            self::$channel->queue_declare(self::$queue.'_failure', false, true, false, false);
            self::$channel->exchange_declare(self::$queue.'_failure_'.'exchange', 'direct');
            self::$channel->queue_bind(self::$queue.'_failure', self::$queue.'_failure_'.'exchange');
            self::$channel->basic_publish($msgError, self::$queue.'_failure_'.'exchange');


            $msg->delivery_info['channel']->basic_ack(
                $msg->delivery_info['delivery_tag']
            );

        }
    }

}
