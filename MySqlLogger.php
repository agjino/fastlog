<?php

include 'mysql_db.php';

class MySqlLogger implements ILogger {

  protected function executeWrite($query, $bound) {
    $stmt = db()->prepare($query);
    $stmt->execute($bound);

    return $stmt->rowCount();
  }

  protected function executeRead($query, $bound = null) {
    $stmt = db()->prepare($query);
    $stmt->execute($bound);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function logEvent($country, $event, $date) {
    $retval = $this->executeWrite("UPDATE log SET count = count + 1 WHERE
      `date` = :date AND
      country = :country AND
      event = :event", compact('event', 'country', 'date'));

    if ($retval === 0) {
      //No rows have been updated because the key does not exist yet.
      //Another request may have performed the insert in the meantime, therefore an ON DUPLICATE is neccessary
      $retval = $this->executeWrite("INSERT INTO log(`date`, country, event, count) VALUES (:date, :country, :event, 1)
        ON DUPLICATE KEY UPDATE count = count + 1", compact('date', 'event', 'country'));
    }
  }

  public function getTop5Last7Days($format) {
    $top5Last7Days = $this->getTop5FromDb();
    switch ($format) {
      case 'json':
        return json_encode($top5Last7Days);
      case 'csv':
        $retval = '';
        foreach ($top5Last7Days['plays'] as $top) {
          $retval .= $top['country'] . ',plays,' . $top['count'] . PHP_EOL;
        }
        foreach ($top5Last7Days['clicks'] as $top) {
          $retval .= $top['country'] . ',clicks,' . $top['count'] . PHP_EOL;
        }
        foreach ($top5Last7Days['views'] as $top) {
          $retval .= $top['country'] . ',views,' . $top['count'] . PHP_EOL;
        }
        return $retval;
    }
  }

  protected function getTop5FromDb() {
    $topAllTime = $this->executeRead(
      'SELECT country, `event`, sum(count) AS `count`
      FROM log
      GROUP BY country, `event`
      ORDER BY `event`, `count` DESC');

    $top5AllTime = [
      'plays' => [],
      'clicks' => [],
      'views' => []
    ];
    //Extract the top 5 of all time for each group
    for ($i = 0; $i < sizeof($topAllTime); $i++) {
      if (sizeof($top5AllTime[$topAllTime[$i]['event']]) == 5) continue;
      else ($top5AllTime[$topAllTime[$i]['event']][] = $topAllTime[$i]['country']);
    }

    //Get the last 7 days stats for the groups extracted above
    //This part is tricky! To avoid running 3 queries on the database (or 3 UNIONs)
    //we will merge the list of all countries in the top 5 of each event.
    //In the end we will probably get more than 5 countries for each event's last 7 days count,
    //up to 15 actually. We will apply the same algorithm as above to extract the top 5 again.
    $merged = array_merge($top5AllTime['plays'], $top5AllTime['clicks'], $top5AllTime['views']);
    //We will use this array for an SQL IN. Quote it's elements before an implode
    $mergedQuoted = array_map(function($el) { return "'$el'"; }, $merged);

    //If there is no data in db
    if (sizeof($mergedQuoted) == 0)
      error('Database is empty');

    $topLast7Days = $this->executeRead(
      'SELECT `country`, `event`, sum(`count`) AS `count`
       FROM log
       WHERE `date` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
       AND country IN (' . implode($mergedQuoted, ',') . ')
       GROUP BY country, `event`
       ORDER BY `event`, `count` DESC');

    $top5Last7Days = [
      'plays' => [],
      'clicks' => [],
      'views' => []
    ];
    //Extract the top 5 of the last 7 days for each group
    for ($i = 0; $i < sizeof($topLast7Days); $i++) {
      if (sizeof($top5Last7Days[$topLast7Days[$i]['event']]) == 5) continue;
      else ($top5Last7Days[$topLast7Days[$i]['event']][] = 
        ['country' => $topLast7Days[$i]['country'], 'count' => $topLast7Days[$i]['count']]);
    }

    return $top5Last7Days;
  }

}