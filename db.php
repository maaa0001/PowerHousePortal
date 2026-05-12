<?php
// Check if we are on InfinityFree or Localhost
$is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || 
                 $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || 
                 (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) ||
                 strpos('DB_HOST_PLACEHOLDER', 'PLACEHOLDER') !== false); 

if ($is_localhost) {
    $dbUserName = 'root';
    $dbPassword = 'password';
    $dbConnection = 'mysql:host=mariadb; dbname=power_house_portal; charset=utf8mb4';
} else {
    // Show errors on InfinityFree for debugging purposes
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $dbUserName = 'DB_USERNAME_PLACEHOLDER';
    $dbPassword = 'DB_PASSWORD_PLACEHOLDER';
    $dbConnection = 'mysql:host=DB_HOST_PLACEHOLDER; dbname=DB_NAME_PLACEHOLDER; charset=utf8mb4';
}

try {
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // try-catch
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // ['nickname']
    //PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // ->nickname
    // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM // [[2],[],[]]
  ];
  $_db = new PDO(  $dbConnection,
                  $dbUserName,
                  $dbPassword ,
                  $options );

}catch(PDOException $ex){
  echo $ex;
  exit(); //die
}