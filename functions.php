<?php

/**
 * Function for debugging purposes. Vardumps the parameter and interrupts application execution.
 * @param  any $var
 */
function dd($var) {
  echo "<pre>";
  var_dump($var);
  die();
}

function error($message, $responseCode = 400) {
  http_response_code($responseCode);
  echo "{ status: 'error', message: '$message'}";
  die();
}