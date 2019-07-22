<?php
    
  //error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
  $dbhost = "172.16.88.21";
  $dbport = "5432";
  $dbname = "akademik";
  $users  = "postgres";
  $pass   = "sembarang"; 
  //$dbh = pg_connect("host=$dbhost dbname=$dbname user=$dbuser port=$dbport password=$dbpass");
  
  $koneksi      = pg_connect("host=$dbhost port=$dbport dbname=$dbname password=$pass user=$users");
  
  if(!$koneksi) {
      echo "Error : Unable TO Open database\n";
  }   else {
      echo "Akses Database Sukses\n";
  }

?>
