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
        $this->connect();

        $data['class'] = $class;
        $data['data'] = $args;
        $data = json_encode($data);
        
        $msg = new AMQPMessage($data, ['delivery_mode' => 2]);
        
        $this->chanel->queue_declare($this->queue, false, true, false, false);
        $this->chanel->basic_publish($msg, '', $this->queue);

        $this->close();
    }
}
