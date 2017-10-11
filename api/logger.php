<?php

require_once '../config.php';
require_once '../functions.php';

require_once '../ILogger.php';
require_once '../' . $config['driver'] . 'Logger.php';
$driver = $config['driver'] . 'Logger';
$logger = new $driver;
if (!$logger instanceof ILogger) {
  error('Invalid driver');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
  error('Invalid request method. Use GET or POST.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //Can process both application/x-www-form-urlencoded and application/json content types
  $requestRaw = file_get_contents('php://input');
  $request = json_decode($requestRaw);
  if ($request === null) {
    error('Malformatted JSON');
  }

  /* Validate country */
  if (!property_exists($request, 'country')) {
    error('Country code is missing.');
  };
  //Request must NOT be in Unicode!
  if (strlen($request->country) !== 2) {
    error('Invalid country code');
  };

  /* Validate event */
  if (!property_exists($request, 'event')) {
    error('Event is missing.');
  };
  if (!in_array($request->event, ['views', 'plays', 'clicks'])) {
    error('Invalid event. Must be in (views, plays, clicks)');
  };

  //By default, the date is today's date
  $date = date('Y-m-d');
  
  /* Validate date */
  if (property_exists($request, 'date')) {
    //Verify that this environment allows custom dates (probably a test environment)
    if ($config['allowCustomDate'] !== true)
      error('Custom dates are not allowed');
    //Try parse
    $date = DateTime::createFromFormat('Y-m-d', $request->date);
    if ($date === false)
      error('Invalid date format');
    else
      $date = $request->date;
  };
  
  //Request is valid. Log it do db
  //Include the driver file and instantiate the driver
  include $config['driver'] . '.php';
  $logger = new $config['driver'];
  if (!$logger instanceof ILogger) {
    error('Invalid driver');
  }
  $logger->logEvent($request->country, $request->event, $date);

  echo 'OK';
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $format = 'csv';
  if (isset($_GET['format'])) {
    if (!in_array($_GET['format'], ['json', 'csv'])) {
      error('Invalid format.');
    } else {
      $format = $_GET['format'];
      if ($format == 'json') {
        header('Content-Type: application/json');
      }
    }
  }

  $retval = $logger->getTop5Last7Days($format);
  echo $retval;
};