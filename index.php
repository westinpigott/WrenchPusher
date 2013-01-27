<?php

//initialize autloaders
require_once 'SplClassLoader.php';
$classLoader = new SplClassLoader('WrenchPusher', 'lib');
$classLoader->register();
$classLoader = new SplClassLoader('Wrench', 'vendor/Wrench/lib');
$classLoader->register();

//enter your ap key here.
define('APP_KEY', 'XXXXXXXXXXXXX');

//create the pusher connection
$client = new WrenchPusher\PusherClient(APP_KEY, array(
            'isSecure' => TRUE,
            'keepAliveDuration' => 5,
        ));
$client->connect();

//subscribe to a channel
$channelName = 'test_channel';
$client->subscribeToChannel($channelName) or die("Error Subscribing to channel.");
echo 'Subscribed to channel: ' . $channelName . "\n";

//let it listen forever
while (true) {
    $client->keepAlive();
    $responses = $client->receive();
    foreach ($responses as $response) {
        /* @var $response WrenchPusher\PusherMessage */
        echo 'Event:' . $response->getEvent() . "\n";
        echo 'Channel:' . $response->getChannel() . "\n";
        echo 'Data: ';
        $data = $response->getData();
        if (is_array($data) || ($data instanceof Traversable) || ($data instanceof stdClass)) {
            echo "\n";
            foreach ($data as $id => $row) {
                echo '    ' . $id . ' : ' . $row . "\n";
            }
        } else {
            echo $data."\n";
        }
        echo "--------------------------------------------------------\n";
    }
}
?>