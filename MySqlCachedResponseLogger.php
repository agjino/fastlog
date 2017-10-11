<?php

include 'MySqlLogger.php';

/**
 * MySql class that caches the calculations for the response
 * and returns cached data up until a certain time has passed from the last
 * calculation. If the cached data is older than a certain amount of seconds
 * it is considered outdated and a new response is calculated and saved.
 */
class MySqlCachedResponseLogger extends MySqlLogger {

  /**
   * The only function that needs overloading.
   * 
   */
  public function getTop5FromDb() {
    global $config;

    $cachedData = $this->executeRead("SELECT * FROM cache WHERE scope = 'top5Last7Days'");

    $timestamp = intval($cachedData[0]['timestamp']);
    $data = unserialize($cachedData[0]['data']);

    if (time() - $timestamp > $config['maxCacheAge']) {
      //Data in cache is old and needs to be refreshed
      db()->beginTransaction();
      $cachedData = $this->executeRead("SELECT * FROM cache WHERE scope = 'top5Last7Days' FOR UPDATE");
      //Do a second check, as another thread may have updated the data in the meantime
      $timestamp = intval($cachedData[0]['timestamp']);
      $data = unserialize($cachedData[0]['data']);
      if (time() - $timestamp > $config['maxCacheAge']) {
        //Data is still old, recalculate and save
        $data = parent::getTop5FromDb();
        $this->executeWrite("UPDATE cache SET data=:data, timestamp=:timestamp, refresh_count=refresh_count+1", ['data' => serialize($data), 'timestamp' => time()]);
        db()->commit();
      } else {
        $this->rollBack();
      }
    }

    return $data;
  }

}