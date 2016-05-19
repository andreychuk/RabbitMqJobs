<?php

namespace Andreychuk\RabbitMq;

/**
 * Interface RabbitMqJobInterface
 *
 * @package Andreychuk\RabbitMq
 */
interface RabbitMqJobInterface
{
    public function perform();
}
