<?php

exit();

?>

<!DOCTYPE html>
<html lang="bn">
<head>

	<?php include 'must-head.php'; ?>

	<script>
		$(document).ready(function(){
			
			$('input[type="submit"]').addClass('disabled', false);

			$("input[type='text'], input[type='email'], input[type='password']").keyup(function(){
				$val = $(this).val();
				if(!(isASCII($val)==true))
				{
					$(this).parent().addClass("has-error");
				}else{
					$(this).parent().removeClass("has-error");
				}

				checkToSubmit();

			});

			$("input:password").keyup(function(){

				$val1 = $("#textpass1").val();
				$val2 = $("#textpass2").val();

				if($val1!=$val2)
				{

					$(".missmatch")
						.text("Missmatch passwords!")
						.addClass("alert alert-warning");
				}
				else

					$(".missmatch")
						.text("")
						.removeClass("alert alert-warning");

				checkToSubmit();

			});

			$( "form" ).submit(function( event ) {
				if(!checkToSubmit())
				{
					return false;
				}
			});


		});

		function checkToSubmit(){
			$name  = $("#textname").val();
			$email = $("#textemail").val();
			$pass1 = $("#textpass1").val();
			$pass2 = $("#textpass2").val();
			
			$('input[type="submit"]').addClass('disabled', true);
			if($pass1!="" &&  $pass2!="" && $name!="" &&
				$email!="" && isValidEmailAddress($email)==true &&
				$pass1===$pass2)
			{
				$('input[type="submit"]').removeClass('disabled', false);
				return true;
			}
			return false;
			
		}

	</script>

</head>
<body>

	<?php include 'must-header.php'; ?>

	<div class="container">

<?php

/*
CHANGE LOG
----------------------------------------
Version 0.0
- 
*/

function displayRegisterForm()
{
	echo '<form class="form-box" role="form" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
	<div class="form-group">
		<label for="textname">Name:</label>
		<input id="textname" type="text" name="uname" pattern="[A-Za-z. ]{1,30}" title="Name should only contain uppercase and lowercase letters, and maximum 30 characters." class="form-control" placeholder="Enter your name" required>
	</div>

	<div class="form-group">
		<label for="textemail">E-mail:</label>
		<input id="textemail" type="email" name="email" class="form-control" placeholder="Enter your e-mail" required>
	</div>


	<div class="form-group">
		<label for="textpass1">Password:</label>
		<input id="textpass1" type="password" name="pword" class="form-control" placeholder="Enter password" required pattern=".{8,}" title="Password should contain minimum 8 characters.">
		<span class="help-block">Password should contain minimum 8 characters.</span>
	</div>

	<div class="form-group">
		<label for="textpass2">Password Again:</label>
		<input id="textpass2" type="password" class="form-control" placeholder="Enter password again" required>
		<div class="missmatch">
		</div>
	</div>

	<input class="btn btn-default" type="submit" value="Register"></input>

</form>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	function registerNow(){
		
		$uname =validateInput(isset($_POST['uname']) ? $_POST['uname'] : '');	
		$email =validateInput(isset($_POST['email']) ? $_POST['email'] : '');	
		$pword =isset($_POST['pword']) ? $_POST['pword'] : '';	

		if($uname=="" || $email =="" || $pword =="")
		{
			echo '<div class="alert alert-danger">
	<strong>Error!</strong> User registration failed! :(
	</div>';

			displayRegisterForm();
		}
		else
		{
			# make new db object
			$db = new db_util();

			$sql = "SELECT * FROM sow_users WHERE user_email='$email'";
			$result = $db->query($sql);

			if($result!== FALSE){ # if there any error in sql then it will false
				if($result->num_rows > 0) { # if the sql execute successful then it give "num_ruws"
				echo '<div class="alert alert-warning">
	  <strong>Warning!</strong> User already exists with this e-mail: ',$email,'.
	</div>';
					displayRegisterForm();
				}
				else
				{

					$stmt = $db->prepare("INSERT INTO sow_users(user_login,user_pass,user_email,user_registered,display_name)
						VALUES(?,?,?,now(),?)");
						# PHP now() function return the current date-time

					$stmt->bind_param("ssss", $user_login, $user_pass, $user_email, $display_name);

					$user_login = $email; // for now username is the email
					$user_pass = password_hash($pword, PASSWORD_BCRYPT, ['cost' => 10]);
					$user_email = $email;
					$display_name = $uname; // for now username is the name

					$result = $stmt->execute();

					if($result===true)
					{
						$user_id = $stmt->insert_id;
						
						$result = $db->add_user_feature(
							$user_id, 
							$GLOBALS['_user_level'], 
							$GLOBALS['_default_level']
							);
						if($result===true)
						{
							echo '<div class="alert alert-success">
	<strong>Success!</strong> User successfully registered! :)
	</div>';
						}
						else
						{
							echo '<div class="alert alert-warning">
	<strong>Warning!</strong> User successfully registered with some error! :3
	</div>';
						}
					}
					else
					{
						echo '<div class="alert alert-danger">
	<strong>Error!</strong> User registration failed! :(
	</div>';

						displayRegisterForm();
					}

					$stmt->close();

				}
			}
		}
	}

	registerNow();

}
else
{

	if(isLoggedIn())
	{
		
		msg_redirectToHomePage('<div class="alert alert-warning">
<strong>Warning!</strong> You are already registered! :)
</div>');

	}
	else
	{
	
	displayRegisterForm();

	}


}
?>

	</div>

	<?php include 'must-footer.php'; ?>

</body>
</html>
