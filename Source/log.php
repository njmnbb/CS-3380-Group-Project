<?php
session_start();

if(($_SERVER['HTTPS']!=="on"))
{ 
	$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header("Location: $redir");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Movie Database Thing</title>
</head>
<body>
	<div align="center">
	<div id="login">
	Please Login<br />
<form method="POST">
	Username: <input type='text' name='username'/> Password: <input type='password' name='password'/><br />
	<input type='submit' name='submit' value='Submit' /><br /><br />
</form>
	<a href='registration.php'>Sign Up Here</a>
	</div>
	</div>
<?php
if(isset($_POST['submit']))
{
	//connecting to database
	include("../../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die("Couldn't connect to Database." . pg_last_error($conn));
	
	if($_POST['submit'] == 'Submit')
	{
		//finding username
		$q = "SELECT salt, password_hash FROM groupProj.authentication WHERE username=$1";
		//preparing
		$pre = pg_prepare($conn, "log_auth", $q);
		//executing
		$ans = pg_execute($conn, "log_auth", array($_POST['username']));
		//if nothing is loaded
		if(!$ans)
		{
			die("Unable to execute sir: " . pg_last_error($conn));
		}
	
		//getting array from query
		$row = pg_fetch_assoc($ans);
		//hashing password
		$newHash = sha1($row['salt'] . $_POST['password']);
	
		//if the passwords match then send to homepage
		if($newHash==$row['password_hash'])
		{
			header("Location: index.php");
			$_SESSION['username'] = $_POST['username'];
			
			//inserting username to database
			$q = "INSERT INTO groupProj.log(username, ip_address, action) VALUES ($1, $2, $3)";
			//preparing
			$pre = pg_prepare($conn, 'logging', $q);
			//executing
			$ans = pg_execute($conn, 'logging', array($_POST['username'], $_SERVER['REMOTE_ADDR'], "logged in"));
			//if nothing is entered
			if(!$ans) 
			{
				die("Unable to execute okay: " . pg_last_error($conn));
			}
		}
		else
		{
			//if password or username does not match
			echo "Please Enter Your Login Information Again";
		}
	}
	
}

?>
</body>
</html>