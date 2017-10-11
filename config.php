<?php

/**
 * WARNING! Do not push this file into the CVS before anonymizing it!
 */
$_config =
  [ 'test_local' =>  
    [
      'mysql' => [
        'dbHost' => 'localhost',
        'dbPort' => '3306',
        'dbName' => 'fastlog_test',
        'dbUser' => 'root',
        'dbPassword' => '',
      ],
      'maxCacheAge' => 1, //In seconds
      'appUrl' => 'http://localhost/fastlog',
      'allowCustomDate' => true,
      'driver' => 'MySqlCachedResponse'
    ],
    'local' =>  
    [
      'mysql' => [
        'dbHost' => 'localhost',
        'dbPort' => '3306',
        'dbName' => 'fastlog',
        'dbUser' => 'fastlog',
        'dbPassword' => 'fastlog',
      ],
      'maxCacheAge' => 1, //In seconds
      'appUrl' => 'http://localhost/fastlog',
      'allowCustomDate' => true,
      'driver' => 'MySqlCachedResponse'
    ],
    'remote' =>  
    [
      'mysql' => [
        'dbHost' => 'localhost',
        'dbPort' => '3306',
        'dbName' => 'fastlog',
        'dbUser' => 'fastlog',
        'dbPassword' => 'fastlog',
      ],
      'maxCacheAge' => 1, //In seconds
      'appUrl' => 'http://awesomeservices.com/fastlog',
      'allowCustomDate' => false,
      'driver' => 'MySql'
    ]
  ];

if (!isset($env)) $env = 'local';
$config = $_config[$env];