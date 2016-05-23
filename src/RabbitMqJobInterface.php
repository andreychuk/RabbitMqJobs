<?php

namespace Andreychuk\RabbitMq;

/**
 * Interface RabbitMqJobInterface
 *
 * @package Andreychuk\RabbitMq
 */
interface RabbitMqJobInterface
{
    public function setUp();
    public function perform();
    public function tearDown();
}
