<?php include('header.php'); ?>

<div id = "content">
<?php
	$title = $_POST['title']or die;
	$title = str_replace("'", "''", $title);
	
	$titleResult = pg_query("SELECT title FROM final_proj.movie WHERE title ILIKE '%$title%' ORDER BY title ASC ");
		
		
		
				echo "<div align=center id = titleContainer>";
				echo "<div style='width:700px;' class = titleTable>\n \t\t\t\t<table style='width:700px;'>";
				
				echo"\n<tr>";
				for($i=0; $i < pg_num_fields($titleResult); $i++){
	                $name = ucwords(strtolower(pg_field_name($titleResult,$i)));
					echo "\n<td> <strong>$name</strong>\n</td>";
					}
				echo "</tr>";
	
				while($line = pg_fetch_array($titleResult, null, PGSQL_ASSOC))
				{
					if(!$line)
						echo"Sorry, coulnd't find movie";
					echo "\t<tr>\n";
					
					foreach ($line as $col_value)
					{
						$column=htmlspecialchars($col_value);
						if($column==NULL)
						{
						echo "Sorry the movie you are looking for could not be found.";
						}
						else
						{
						echo "<form method='POST' action='title.php'>";
						echo "<td><input type=submit style='height:25px;' align=center name=title value=\"$column\" ></input></td>\n";
						echo "</form>";
						}
					}
					echo "</tr>\n";
				}
				echo"\n<tr>";
				echo "</table>";
				echo "</div>";
		
?>
</div>