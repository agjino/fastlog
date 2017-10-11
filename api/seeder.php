<?php

/*
 * Call this file through your browser and specify a row count to seed, like this:
 *
 * http://localhost/fastlog/tests/seeder.php?count=100000
 *
 * The environment of the receiving instance must have allowCustomDate => true 
 */

require_once '../config.php';
require_once '../functions.php';
require_once '../ILogger.php';
require_once '../ISeeder.php';

require_once '../' . $config['driver'] . 'Seeder.php';

$driver = $config['driver'] . 'Seeder';
$seeder = new $driver;
if (!$seeder instanceof ISeeder) {
  error('Invalid seeder');
}

if (!isset($_GET['method']))
  $method = 'http';
else
  $method = $_GET['method'];

if (!in_array($method, ['migrate', 'http', 'internal']))
  error("Invalid seed method. Use 'migrate', 'http' or 'internal'");

if ($method != 'migrate' && !isset($_GET['count'])) {
  error('Specify a seed count through ?count=n');
}

$count = 0;
if (isset($_GET['count'])) {
  $count = $_GET['count'];
  if (intval($count) == 0)
    error('Invalid seed count');
}

$startTime = microtime(true);

switch ($method) {
  case 'http':
    $seeder->seedHttp($count);
    echo "$count requests completed.";
    break;
  case 'internal':
    $seeder->seedInternal($count);
    echo "$count rows created.";
    break;
  case 'migrate':
    $seeder->migrate();
    echo "Database created.";
    break;
}

$endTime = microtime(true);
echo sprintf (" Time: %.2f s", ($endTime - $startTime));
