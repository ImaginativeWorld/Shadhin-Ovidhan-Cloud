<?php 
	
	//include 'must-header.php'; 
	include 'func.php';
	
	$host = $GLOBALS['c_host'];
	
	setcookie( $GLOBALS['c_name'], "", -1, '/', $host, false, true);
	setcookie( $GLOBALS['c_email'], "", -1, '/', $host, false, true);
	setcookie( $GLOBALS['c_id'], "", -1, '/', $host, false, true);
	setcookie( $GLOBALS['c_hash'], "", -1, '/', $host, false, true);
	setcookie( $GLOBALS['c_isloggedin'], "", -1, '/', $host, false, true);

	//php function to check if headers sent or not
    if (!headers_sent()) {
        header( "refresh:0;url=/socloud" );
    }

?>

<!DOCTYPE html>
<html lang="bn">
<head>

<script
        src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
        crossorigin="anonymous"></script>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="_js/func.min.js"></script>

<link rel="stylesheet" type="text/css" href="style.min.css">

<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,600' rel='stylesheet' type='text/css'>

</head>

<body> 

	<div class="container">
		<div class="alert alert-info">
		<strong>Logout</strong> successfully!<br>
		<br>
		You are automatically redirect to home page in few seconds.<br>
		If not redirect automatically click <a href="/socloud">here</a> manually.
		</div>
	</div>


<?php
 //include 'must-footer.php';
 ?>

</body>

</html>