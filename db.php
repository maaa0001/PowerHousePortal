<?php
// Check if we are on InfinityFree or Localhost
$is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1');

if ($is_localhost) {
    $dbUserName = 'root';
    $dbPassword = 'password';
    $dbConnection = 'mysql:host=mariadb; dbname=power_house_portal; charset=utf8mb4';
} else {
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