<?php

//Database connection.

$con = MySQLi_connect(

   "localhost", //Server host name.

   "root", //Database username.

   "", //Database password.

   "CampusNavSEGP" //Database Name

);



//Check connection

if (MySQLi_connect_errno()) {

   echo "Failed to connect to MySQL: " . MySQLi_connect_error();

}

?>