<?php

interface ISeeder {
  
  public function migrate();

  public function seedHttp($count);
    
  public function seedInternal($count);

}