<?php

namespace Andreychuk\RabbitMq;

class Job implements RabbitMqJobInterface
{
    public function perform()
    {
        var_dump(func_get_args());
    }
}