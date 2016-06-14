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

//\Andreychuk\RabbitMq\RabbitMqQueue::setBackend('192.168.99.100', '8085', 'admin', 'GWzEk*DZEqd`eU2m');
//\Andreychuk\RabbitMq\RabbitMqQueue::enqueue('Test');

$queue = new \Andreychuk\RabbitMq\RabbitMqQueue();
$queue->setHost('192.168.99.100');
$queue->setPort(8085);
$queue->setUser('admin');
$queue->setPassword('GWzEk*DZEqd`eU2m');


$queue->enqueue('Test');
die("Done");