<?php

namespace Andreychuk\RabbitMq;

/**
 * Interface RabbitMqJobInterface
 *
 * @package Andreychuk\RabbitMq
 */
interface RabbitMqJobInterface
{
    function setUp();
    function setArgs(array $args = []);
    function perform();
    function tearDown();
}
