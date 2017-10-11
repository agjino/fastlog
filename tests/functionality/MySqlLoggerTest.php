<?php

class MySqlLoggerTest extends PHPUnit\Framework\TestCase {

  private function countLogRows() {
    return (db()->query("SELECT COUNT(*) AS count FROM log")->fetch())['count'];
  }

  public function testAddEvent() {
    global $logger;
    resetDatabase();
    $logger->logEvent('US', 'clicks');
    $this->assertEquals(1, $this->countLogRows());
  }

  

}