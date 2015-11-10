<?php include('header.php'); ?>

<div id = "content">
	<!--Username-->
	<h1 id = 'username'><?php echo $_SESSION['username']; ?></h1>

	<!--Left-sider user box-->
	<div id = "userLeft">
		<h3 class = 'userHeaders'>Reviews</h3>
			<?php
			$username = $_SESSION['username'];
				$result = pg_query("SELECT title, review FROM final_proj.user_reviews_movie
									WHERE username = '$username'
									ORDER BY title ASC");
				if(!$result)
					echo "An error occured.";
				
				echo "<div id = userTable>\n \t\t\t\t<table id = two_fity_table>";  
				
	                $rows = pg_num_rows($result);
					$field = pg_num_fields($result);
				echo "\n<tr>";
	            for($i=0; $i < pg_num_fields($result); $i++){
	                $name = pg_field_name($result,$i);
					if($name=='title')
						$name='Titles';
					else
						$name='Reviews';
	              	echo"\n<td align = center> <strong>$name</strong>\n</td>";
	            }
				echo "\n</tr>";
				
				while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
					echo "\t<tr>\n";
					
					//prints data by the line
					foreach ($line as $col_value) {
						echo "<td>$col_value</td>\n";
					}
						echo "</tr>\n";
				}
	            echo "\n</table>\n</div>\n";
			
			?>

		<!--Favorites-->
		<h3 class = 'userHeaders'>Favorites</h3>
		<?php
			$username = $_SESSION['username'];
			$result = pg_query("SELECT title, like_or_dislike FROM final_proj.user_favorites_movie
								WHERE username = '$username'
								ORDER BY title ASC")or die("Query Failed: " . pg_last_error());

			echo "<div id = userTable> <table id = two_fity_table>";  
	        echo "<tr>"; 
	        for($i=0; $i < pg_num_fields($result); $i++){
	            $name = pg_field_name($result,$i);
				if($name=='title')
					$name='Titles';
				else
					$name='Likes or Dislikes';
	            echo"<td align = center> <strong>$name</strong></td>";
				
					
	        }
			echo "</tr>";
			$line = pg_fetch_array($result, null, PGSQL_ASSOC) or die("Query Failed: " . pg_last_error());
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
					echo "\t<tr>\n";
					//prints data by the line
					foreach ($line as $col_value) {
						if($col_value=='t')
						{
							$col_value='LIKED';
						}
						else if($col_value=='f')
						{
							$col_value='DISLIKED';
						}
						echo "<td>$col_value</td>\n";
							
					}
						echo "</tr>\n";
			}
	        echo "</table></div>\n";
		?>
	</div>

	<!--Right-side user box-->
	<div id = "userRight">
		<h3 class = 'userHeaders'>Nearby Theater Map</h3>
		<iframe width="500" height="550" frameborder="0" src="https://www.google.com/maps/embed/v1/search?q=nearby%20movie%20theaters&key=AIzaSyBVpBSK-qh7opdR9OFDZcSGfizk7HeVulo"></iframe>
	</div>
	<br style="clear:both;" />
</div>
</body>
</html>