<?php 
 
session_start();
 
if ($_GET['login']) {
    // Only load the code below if the GET
    // variable 'login' is set. You will
    // set this when you submit the form
 
    if ($_POST['username'] == 'ad_verify'
	&& $_POST['password'] == 'f0urty9redbu11s') {
	// Load code below if both username
	// and password submitted are correct
 
	$_SESSION['loggedin'] = 1;
	// Set session variable
 
	header("Location: protected.php");
	exit;
	// Redirect to a protected page
 
    } else echo "<strong>NOPE</strong><br>";
    // Otherwise, echo the error message
 
}
 
?>
Log in:
<form action="?login=1" method="post">
    Username: <input type="text" name="username" />
    Password: <input type="password" name="password" />
<input type="submit" />
</form>