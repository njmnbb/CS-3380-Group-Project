<?php 
    include("../../secure/database.php");
     $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

     if(!$conn){
   	    echo "Failed to connect to DB";
      }
      
      
     //$file = fopen("imdb_top_250.csv","r");

     $result = pg_prepare($conn, "insert", "INSERT INTO final_proj.movie (rank,rating,title,votes,director) VALUES ($1,$2,$3,$4,$5)");
     
     if(($file = fopen("imdb_top_250.csv","r"))!== FALSE){
        while(($movie = fgetcsv($file)) !== FALSE){
     	  if($movie[0] == null){
     	 
     	}
     	  else{
     	  $result = pg_execute($conn,"insert",array($movie[0],$movie[1],$movie[2],$movie[3],$movie[4])) or die(pg_last_error());
        } 
      } 
    }else{
    	echo "It no open";
    }
    echo " I think its done..... maybe....\n";
  pg_close($conn);
 ?>
