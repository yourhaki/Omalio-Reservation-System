<?php

   $db_host = 'localhost';
   $db_name = 'homestay_db';
   $db_user_name = 'root';
   $db_user_pass = '';

   $dsn = "mysql:host=$db_host;dbname=$db_name";

   try {
      $conn = new PDO($dsn, $db_user_name, $db_user_pass);
      // Set PDO to throw exceptions on error
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
      // Handle the connection error appropriately (e.g., log it, display an error message, etc.)
      die();
   }

   function create_unique_id(){
      $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $rand = array();
      $length = strlen($str) - 1;

      for($i = 0; $i < 20; $i++){
         $n = mt_rand(0, $length);
         $rand[] = $str[$n];
      }
      return implode($rand);
   }

?>
