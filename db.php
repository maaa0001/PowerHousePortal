<?php
// PDO
try{
  $dbUserName = 'root';
  $dbPassword = 'password'; // root | admin
  $dbConnection = 'mysql:host=mariadb; dbname=power_house_portal; charset=utf8mb4';
  // utf8 every character in the world
  // mb4 every character and also emojies
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