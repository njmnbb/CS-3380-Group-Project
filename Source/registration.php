<?php include('header.php'); ?>

<div id = "content">

	<div align="center">

		<h1>Please Register</h1><br><br>
		<form method="POST" action="registration.php">
			<p>Username:</p> <input type='text' name='username' value='' id = 'credentials'/><br><br>
			<p>Password:</p> <input type='password' name='password' id = 'credentials'/><br><br>
			<input type='submit' name='reg' value='Submit' id = 'registrationButton'/><br><br>
		</form>

	</div>
<?php
//if it is post
if(isset($_POST['reg']))
{
	//connecting to database
	include("../../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die("Couldn't connect to database." . pg_last_error($conn));
		
	//making salt a random
	mt_srand();
	$salt = sha1(rand());
	//making the password randomized
	$pwhash = sha1($salt . $_POST['password']);
	//Inserting the username into database first
	$q = "INSERT INTO final_proj.user_info (username) VALUES ($1)";
	//preparing the array
	$pre = pg_prepare($conn, 'user_info', $q) or die("Couldn't prepare..." . pg_last_error($conn));
	//loading info into database
	$ans = pg_execute($conn, 'user_info', array($_POST['username']));
	//if nothing is loaded into the database
	if(!$ans)
	{
		echo 'Unable to: '. pg_last_error($conn);
		echo "Unable to add username<br />";
		echo "Click <a href='registration.php'>here</a> to try again.";
	}
	
	//inserting username into query
	$q = "INSERT INTO final_proj.authentication (username, password_hash, salt) VALUES ($1, $2, $3)";
	//preparing statment
	$pre = pg_prepare($conn, 'auth', $q);
	//executing the array
	$ans = pg_execute($conn, 'auth', array($_POST['username'], $pwhash, $salt));
	//if nothing is loaded into the database
	if(!$ans)
	{
		echo 'Unable to: '. pg_last_error($conn);
		echo "Unable to add username<br />";
		echo "Click <a href='registration.php'>here</a> to try again.";
	}
	
	//inserting username into username log table
	$q = "INSERT INTO final_proj.log (username, ip_address, action) VALUES ($1, $2, $3)";
	//preparing statement
	$pre = pg_prepare($conn, 'log', $q);
	//executing the array
	$ans = pg_execute($conn, 'log', array($_POST['username'], $_SERVER['REMOTE_ADDR'], "register"));
	//if nothing is loaded into the database
	if(!$ans)
	{
		echo 'Unable to perform: ' . pg_last_error($conn);
		echo "Unable to add username<br />";
		echo "Click <a href='registration.php'>here</a> to try again.";
	}
	else
	{
		header("Location: index.php");
	}
}

?>
</div>
</body>
</html>