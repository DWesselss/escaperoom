<?php
$server = "localhost"; 
$username = "root";
$password = "";  //macbook gebruikers vullen bij wachtwoord "root" in.
$db = "escape-room"; //pas dit aan indien de naam van jullie database anders is

try {
  $db_connection = new PDO("mysql:host=$server; dbname=$db", $username, $password);
  $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Verbinding mislukt" . $e->getMessage();
}