<?php

error_reporting(0);

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new \App\Kernel($_SERVER['HTTP_HOST']);

$kernel->run();