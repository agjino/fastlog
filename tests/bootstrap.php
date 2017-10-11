<?php

$env = 'test_local';

require_once 'config.php';
require_once 'functions.php';

require_once 'ILogger.php';
require_once 'ISeeder.php';
require_once $config['driver'] . 'Logger.php';
$loggerDriver = $config['driver'] . 'Logger';
$logger = new $loggerDriver;
require_once $config['driver'] . 'Seeder.php';
$seederDriver = $config['driver'] . 'Seeder';
$seeder = new $seederDriver;

function resetDatabase() {
  global $seeder;
  $seeder->migrate();
}