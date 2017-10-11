<?php

function db() {
  global $config;
  static $conn = null;
  
  if ($conn === null) {
    $conn = new PDO(sprintf("mysql:host=%s;port=%d;dbname=%s",
      $config['mysql']['dbHost'], $config['mysql']['dbPort'],  $config['mysql']['dbName']),
      $config['mysql']['dbUser'], $config['mysql']['dbPassword']);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  }

  return $conn;
}