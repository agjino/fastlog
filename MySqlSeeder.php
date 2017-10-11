<?php

require_once 'MySqlLogger.php';

class MySqlSeeder implements ISeeder {

  static $countries = ['US', 'AU', 'DE', 'PL', 'RO', 'AL', 'CA', 'RU', 'IN', 'CH', 'FR', 'ES'];
  static $events = ['views', 'plays', 'clicks'];

  //Resets the database and build the base tables
  public function migrate() {
    db()->exec(
<<<SQL
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS log;
CREATE TABLE `log` (
  `date` date NOT NULL,
  `country` char(2) NOT NULL,
  `event` varchar(10) NOT NULL,
  `count` int(11) DEFAULT '0',
  PRIMARY KEY (`event`,`country`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `cache` (
  `scope` varchar(50) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `data` text,
  `refresh_count` int(11) DEFAULT '0',
  PRIMARY KEY (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO cache VALUES ('top5Last7Days', 0, null, 0);
SQL
);
  }

  /**
   * Seeds the database throught HTTP calls. Useful for performance tests
   * 
   * @param  int $count number of requests to send
   */
  public function seedHttp($count) {
    global $config;
    for ($i = 0; $i < $count; $i++) {
      //Generate a random date between now and 365 days ago
      $date = date('Y-m-d', time() - rand(0, 31536000));

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $config['appUrl'] . '/logger');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'country' => self::$countries[rand(0, sizeof(self::$countries) - 1)],
        'event' => self::$events[rand(0, sizeof(self::$events) - 1)],
        'date' => $date
      ]));

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $server_output = curl_exec ($ch);

      curl_close ($ch);
    }
  }

  /**
   * Seeds the database throught function calls. Useful for fast seeding.
   * 
   * @param  int $count number of rows to seed
   */
  public function seedInternal($count) {
    $logger = new MySqlLogger;
    for ($i = 0; $i < $count; $i++) {
      //Generate a random date between now and 365 days ago
      $date = date('Y-m-d', time() - rand(0, 31536000));

      $logger->logEvent(
        self::$countries[rand(0, sizeof(self::$countries) - 1)],
        self::$events[rand(0, sizeof(self::$events) - 1)],
        $date);
    }
  }

}