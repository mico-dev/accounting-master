<?php
 
$host = "localhost";
$user = "root";
$password = "2125";
$database = "db_pf_mailer";
 
$con = new mysqli("$host","$user","$password","$database");
	 
if ($con -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}
?>
 