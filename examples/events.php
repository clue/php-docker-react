<?php
// this simple example displays all docker events that happen in the next 10s.
// try starting / removing a container in the meantime to see some output.

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory as LoopFactory;
use Clue\React\Docker\Factory;

$loop = LoopFactory::create();

$factory = new Factory($loop);
$client = $factory->createClient();

// get a list of all events that happened up until this point
// expect this list to be limited to the last 64 (or so) events
// $events = $client->events(0, microtime(true));

// get a list of all events that happened in the last 10 seconds
// $events = $client->events(microtime(true) - 10, microtime(true));

// stream all events for 10 seconds
$stream = $client->eventsStream(null, microtime(true) + 10.0);

$stream->on('progress', function ($event) {
    echo json_encode($event) . PHP_EOL;
});

$stream->on('error', 'printf');

$loop->run();
