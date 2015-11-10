<?php
session_start();

if(($_SERVER['HTTPS']!=="on"))
{ 
	$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header("Location: $redir");

	//connecting to database
	include("../../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)or die("Couldn't connect to Database." . pg_last_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Movies 250</title>
	
	<!--Linking CSS stylesheet-->
    <link rel="stylesheet" type="text/css" href="main.css">
    <script type="text/javascript" src="../d3.min.js"></script>
     <script type="text/javascript" src="../jquery-1.11.2.min.js"></script>
     <script type="text/javascript" src="forcemap.js"></script>
  <style>
   svg{
   	 display: block;
   	 margin: auto;
   }
   .
 text .text{
	pointer-events: none;
	font: 20 px sans-serif;
   	 fill: #3F3737;
   }

.node {
    fill: #ccc;
    stroke: #fff;
    stroke-width: 2px;
}

.link {
    stroke: #777;
    stroke-width: 2px;
}</style>
</head>
<body>
	<div id = "contentContainer">

		<div id = "header">
			<a href = "index.php"><img src = "images/movie-logo.png"></a>
			<img src = "images/header-quote.png" id = "quote">
			<ul>
				<!--If the user is already logged in, remove log in fields-->
				<?php
					if(!$_SESSION['username']) {
						echo "<form method = 'POST'>";
						echo	"<li class = 'login'>Log in:</li>";
						echo	"<li><input type = 'text' name = 'username' placeholder = 'username' class = 'login'></li>";
						echo	"<li><input type = 'password' name = 'password' placeholder = 'password' class = 'login'></li>";
						echo	"<li><input type = 'submit' name='submit' value='Submit' id = 'loginButton'></li>";
						echo "</form>";
					}

				?>

				<!--Checking if 'submit' button is pressed-->
				<?php
				if(isset($_POST['submit']))
				{
					
					
					if($_POST['submit'] == 'Submit')
					{
						//connecting to database
						include("../../secure/database.php");
							$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)or die("Couldn't connect to Database." . pg_last_error($conn));
							
						//finding username
						$q = "SELECT salt, password_hash FROM final_proj.authentication WHERE username=$1";
						//preparing
						$pre = pg_prepare($conn, "log_auth", $q);
						//executing
						$ans = pg_execute($conn, "log_auth", array($_POST['username']));
						//if nothing is loaded
						if(!$ans)
						{
							die("Unable to execute: " . pg_last_error($conn));
						}
					
						//getting array from query
						$row = pg_fetch_assoc($ans) or die("Not able to do: " . pg_last_error($conn));
						//hashing password
						$newHash = sha1($row['salt'] . $_POST['password']);
					
						//if the passwords match then send to homepage
						if($newHash==$row['password_hash'])
						{
							header("Location: index.php");
							$_SESSION['username'] = $_POST['username'];
							
							//inserting username to database
							$q = "INSERT INTO final_proj.log(username, ip_address, action) VALUES ($1, $2, $3)";
							//preparing
							$pre = pg_prepare($conn, 'logging', $q);
							//executing
							$ans = pg_execute($conn, 'logging', array($_POST['username'], $_SERVER['REMOTE_ADDR'], "logged in"));
							//if nothing is entered
							if(!$ans) 
							{
								die("Unable to execute: " . pg_last_error($conn));
							}
						}
						else
						{
							//if password or username does not match
							echo "<li class = 'login'>Incorrect username or password</li>";
						}
					}
					
				}

				?>
			</ul>
		</div>

		<div id = "menuBar">
			<ul>
				<li><a href = "index.php">Home</a></li>
			<!--	<li><a href = "#">About</a></li>
				<li><a href = "#">Actors</a></li>
				<li><a href = "#">Movies</a/></li>
				<li><a href = "#">My Page</a/></li>
				<li><a href = "#">FAQ</a></li>
				<li><a href = "#">Contact</a></li>
				-->
<?php
	//if the user is logged in
	if(isset($_SESSION['username']))
	{
		//connecting to database
		include("../../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
			
		//getting username
		$uName = $_SESSION['username'];
			
		//Loading query	with username for button
		$q = "SELECT user_info.username FROM final_proj.user_info 
				INNER JOIN final_proj.log USING (username) WHERE username=$1 AND log.action='register'";
		
		//preparing the query
		$pre = pg_prepare($conn, 'user', $q) or die("Unable to perform" . pg_last_error($conn));
		//execute query and fail if nothing is executed
		$query = pg_execute($conn, 'user', array($uName)) or die("Unable to execute." . pg_last_error($conn));
		
		//getting the username
		$user = pg_fetch_array($query,0,PGSQL_NUM);
		//listing the user and logout option
		echo "<li><a href = 'userpage.php'>" . $_SESSION['username'] . "</a></li>";
		echo "<li><a href = 'logout.php'>Logout</a></li>\n";
	}
	else
	{
		echo "<li><a href = 'registration.php'>Register</a></li>";
	}




?>
				
			</ul>
		</div>