<?php

require_once 'MySqlLogger.php';

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
   * This is an experiemental functionality where data that would take a long time to compute is kept into a cache 
   * and served from that cache, until the cache is older that a certain number of seconds specified in config.php:maxCacheAge.
   * 
   * The function retrieves data from cache first. Verifies if this data is too old. If it is, does a second retrieval 
   * with the For Update intent. In this way, the cache table is now locked.
   * However, another request might have updated the cache in the meantime, therefore another check for cache age is
   * performed before it is decided whether to use the cached data or do a recalculation and cache update.
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