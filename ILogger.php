<?php

interface ILogger {
  
  public function logEvent($country, $event, $date);

  public function getTop5Last7Days($format);

}