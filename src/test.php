<?php


$files = array(
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
);

foreach ($files as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

echo "Adding to queue", PHP_EOL;

\Andreychuk\RabbitMq\RabbitMqQueue::setBackend('192.168.99.100', '5672', 'admin', 'GWzEk*DZEqd`eU2m');
\Andreychuk\RabbitMq\RabbitMqQueue::enqueue('Test');

die("Done");