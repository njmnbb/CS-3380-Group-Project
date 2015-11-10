<?php include('header.php'); ?>
<?php
echo '<form method="POST" action="search.php">';
echo '<div align=center>';
echo "<input type='text' style='height:20px;' name='title' value=''></input>\n";
echo "<input type=submit name=search style='height:25px;' value='Click to Search A Movie' onclick='https://babbage.cs.missouri.edu/~cs3380s15grp14/project/src/search.php' ></input>\n";
echo '</div>';
echo '</form>';
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
		<div id = "content">
		
          <?php 
            //session_start();
                //connecting to database
	           include("../../secure/database.php");
	            $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)or die("Couldn't connect to Database." . pg_last_error($conn));
                $result = pg_query("SELECT * FROM final_proj.movie")or die(pg_last_error());

                echo "<div id = tableContainer class = faded> <table id = two_fity_table><tr>";  
                
                for($i=0; $i < pg_num_fields($result); $i++){
                	$name = ucwords(strtolower(pg_field_name($result,$i)));
                	echo"<td align = center> <strong>$name</strong></td>";
                }
                 echo"</tr>";
				
                 while($line = pg_fetch_array($result,null,PGSQL_ASSOC)){
 		            echo "\t<tr>\n";
     	
     	        $n = 0;
				$a = 1;
     	        foreach($line as $col_value){
				echo '<form method="POST" action="title.php">';
				  if(pg_field_name($result, $n) == "title")
				  {
					//make a button for each movie and redirect to its personal page
					$column=htmlspecialchars($col_value);
					echo"<td align = center> <input type=submit name=title value=\"$column\" onclick='https://babbage.cs.missouri.edu/~cs3380s15grp14/project/src/title.php' ></input></td>";
					
				  }
				echo '</form>';
     	        //make button for every director and preform graphic action if pressed
                if(pg_field_name($result, $n) == "director"){
     	           $col= $col_value;
					echo "<td align = center> <input type=submit name=button value=\"$col\" ></input><td>";
                  }
				  else if(pg_field_name($result, $n) == "votes"){
					$col = $col_value;
				   echo"\t\t<td align = center>$col</td>\n";
     	           }
				  else if(pg_field_name($result, $n) == "rating"){
					$col = $col_value;
				   echo"\t\t<td align = center>$col</td>\n";
				  }
				  else if(pg_field_name($result, $n) == "rank"){
					$col = $col_value;
				   echo"\t\t<td align = center>$col</td>\n";
				  }
     	          $n++;
     	         }
     	           echo "\t</tr>\n";
                  }
                   echo "</table></div>\n";
              

                //if one of the director buttons is pressed,
               //query the DB grabbing all movies in the table with a director of the same name
               //put all the data into an array that will be utilized by the d3 library to make a force layout    
               if(isset($_POST['button'])){
                 
                  $name = $_POST['button'];
                  
                  
                  $arr = array($name);
                  
                  //query the db
                  $result = pg_prepare($conn,"get data",'SELECT title FROM final_proj.movie WHERE (director = $1)') or die('Query Failed'.pg_last_error());
                  $result = pg_execute($conn,"get data",array($name))or die('Query Failed'.pg_last_error());
                  while($line = pg_fetch_array($result,null,PGSQL_ASSOC)){
                    
                    //push all values into the array
                    foreach ($line as $col_value) {
                  
                    array_push($arr,$col_value);
                       
                    }
                  }
                  
                  //encode the data for js use
                  $arr2 = json_encode($arr);
                   
                   //echo into the java script
                  echo "<script type='text/javascript'>
                      forceMap($arr2);
                     </script>";
                  
               }

                //if a review was submitted by a user
               if(isset($_POST['submitPane'])){

                //make sure they are logged in
                  if(!(isset($_SESSION['username']))){
                    echo("You must be logged in to use this feature!");
                  }
                //insert the review into the db
                  else{
                  $result = pg_prepare($conn,"insert review",'INSERT INTO final_proj.user_reviews_movie VALUES ($1,$2,$3)') or die('Query Failed'.pg_last_error());
                  $result = pg_execute($conn,"insert review",array($_POST['movieTitle'],$_SESSION['username'],$_POST['review']))or die('Query Failed'.pg_last_error());
                      }

               
               }

                //if  like button was pressed
                if(isset($_POST['like'])){
                  //make sure they are logged in
                    if(!$_SESSION['username']){
                    echo("You must be logged in to use this feature!");
                  }

                  //insert the like into the db
                   else{
                 
                  
                  $result = pg_prepare($conn,"insert fav",'INSERT INTO final_proj.user_favorites_movie VALUES ($1,$2,$3)') or die('Query Failed'.pg_last_error());
                  $result = pg_execute($conn,"insert fav",array($_POST['movieTitle'],$_SESSION['username'],true))or die('Query Failed'.pg_last_error());
                      
                  }
                }
                  //if a dislike button was pressed
                   if(isset($_POST['dislike'])){
                 //make sure they are logged in
                  if(!$_SESSION['username']){
                    echo("You must be loggen in to use this feature!");
                  }

                  else{
                
                    
                    

                  //insert the dislike in to the db
                   
                  $result = pg_prepare($conn,"insert notfav",'INSERT INTO final_proj.user_favorites_movie VALUES ($1,$2,$3)') or die('Query Failed'.pg_last_error());
                  $result = pg_execute($conn,"insert notfav",array($_POST['movieTitle'],$_SESSION['username'],f))or die('Query Failed'.pg_last_error());
                      }
                  }
				  
				


           ?>

		</div>
   </form>
	</div>
</body>
</html>