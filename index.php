<?php
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';
$test = new \HDESDK\SDKInit();

$test->Auth('leo40230@gmail.com', 'da736d56-c09d-4c7f-96c3-d961c29b7ee6', 'https://grimleo.helpdeskeddy.com');

$options = array(
    'ticket_id' => 3,
    'page'      => 1
);

print_r($test->CommentsGet($options));



