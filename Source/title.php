<?php include('header.php'); ?>
<?php
echo '<form method="POST" action="search.php">';
echo "<div align=center>";
echo "<input type='text' style='height:20px;' name='title' value=''></input>\n";
echo "<input type=submit name=search style='height:25px;' value='Click to Search A Movie' ></input>\n";
echo "</div>";
echo '</form>';
?>
<div id = "content">
	<h1 id = 'username' align = center><?php echo $_POST['title']; ?></h1>
		<?php
			//if(isset($_POST['title']))
			//{
				$title = $_POST['title'];
				$sameTitle=$title;
				$title = str_replace("'", "''", $title);
				echo "<h3 class = 'userHeaders'>$name</h3>";

				//Creating the review query
				$reviewResult = pg_query("SELECT username, review FROM final_proj.user_reviews_movie WHERE title = '$title' ")or die(pg_last_error());
				if(!$reviewResult)
				{
					echo "An error occured.";
				}

				//Creating the favorites query
				$favoriteResult = pg_query("SELECT like_or_dislike FROM final_proj.user_favorites_movie WHERE title = '$title'");

				if(!$favoriteResult)
				{
					echo "An error occured.";
				}

				//Creating the review table
				echo "<div id = titleContainer>";
				echo "<div class = titleTable>\n \t\t\t\t<table>";
				echo"\n<tr>";
				for($i=0; $i < pg_num_fields($reviewResult); $i++){
	                $name = ucwords(strtolower(pg_field_name($reviewResult,$i)));
					echo "\n<td align = center> <strong>$name</strong>\n</td>";
					}
				echo "</tr>";
				
				while($line = pg_fetch_array($reviewResult, null, PGSQL_ASSOC))
				{
					echo "\t<tr>\n";
					
					foreach ($line as $col_value)
					{
						echo "<td>$col_value</td>\n";
					}
					echo "</tr>\n";
				}
				echo"\n<tr>";
				echo "</table>";
				echo "</div>";


				//Crating the favorites table
				echo "<div id = favoritesPercentage>\n";

				//Creating like and dislike variables
				$dislikes = 0;
				$likes = 0;
				
				while($line = pg_fetch_array($favoriteResult, null, PGSQL_ASSOC))
				{
					foreach ($line as $col_value)
					{	
						if($col_value == 't')
						{
							$likes++;
						}
						else if($col_value == 'f')
						{
							$dislikes++;
						}	
					}				
				}
				//Creating percentages based on likes and dislikes
				$likePercentage = $likes / ($likes + $dislikes) * 100;
				$likePercentage = round($likePercentage,0);
				$dislikePercentage = $dislikes / ($likes + $dislikes) * 100;
				$dislikePercentage = round($dislikePercentage,0);
				$r = $_POST['title'];
				?>
				<form method='POST' action="<?=$_SERVER['PHP_SELF']?>">
				<?php
				echo "<div id=favoritesPercentage>\n";
				echo "<input type = hidden name = movieTitle value = \"$r\" ></input>";
				echo "<p><input align=center id=like type=submit style='width:50px;' name=like value='Like'></input>\n";
				//Printing out like and dislike percentages
				echo "<span id = like>" . $likePercentage . "% </span>of users like this movie</p>\n";
				echo "<p><input align=center id=dislike type=submit style='width:50px;' name=dislike value='Dislike'></input>\n";
				echo "<span id = dislike>" . $dislikePercentage . "% </span>of users do not like this movie</p>\n<br />";
				
				//printing textbox and like/dislike buttons				
				echo "</div><br />";
				echo "<p align=center>Write A Review!</p><br />";
				echo "<textarea align=center rows='5' cols='80' name='review'></textarea><br />";
				echo "<input type=submit name= submitPane style='width:577px;' value='Review'></input>";
				echo "</div>";
				echo "</div>";
			

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
					
                    header("Location: index.php" );
               
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
                  header("Location: index.php" );
                }
                  //if a dislike button was pressed
                   if(isset($_POST['dislike'])){
                    //make sure they are logged in
                    if(!$_SESSION['username']){
                    echo("You must be loggen in to use this feature!");
                  }

                  //insert the dislike in to the db
                   else{
                  $result = pg_prepare($conn,"insert notfav",'INSERT INTO final_proj.user_favorites_movie VALUES ($1,$2,$3)') or die('Query Failed'.pg_last_error());
                  $result = pg_execute($conn,"insert notfav",array($_POST['movieTitle'],$_SESSION['username'],f))or die('Query Failed'.pg_last_error());
                      }
                  header("Location: index.php" );
                  }

		?>
		<br style="clear:both;" />
	</div>
