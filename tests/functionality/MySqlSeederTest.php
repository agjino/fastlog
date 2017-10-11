<?php

final class MySqlSeederTest extends PHPUnit\Framework\TestCase {

  private function countLogRows() {
    return (db()->query("SELECT COUNT(*) AS count FROM log")->fetch())['count'];
  }

  private function sumLogRows() {
    return (db()->query("SELECT SUM(count) AS sum FROM log")->fetch())['sum'];
  }

  /**
   * @covers database creation
   */
  public function testMigrateDatabase() : void {
    global $seeder;
    $seeder->migrate();
    $this->assertEmpty($this->countLogRows());
  }

  public function testSeed1000() : void {
    global $seeder;
    resetDatabase();
    $seeder->seedInternal(1000);
    $this->assertEquals($this->sumLogRows(), 1000);
  }
  
}