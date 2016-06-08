<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	include 'func.php';

	$_isLogin = false;
	$_title = "";
	$_message = "";
	
	function loginNow(){
		
		$uname =validateInput(isset($_POST['uname']) ? $_POST['uname'] : '');	
		$pword =isset($_POST['pword']) ? $_POST['pword'] : '';

		if($uname=="" || $pword =="")
		{
			$GLOBALS['_isLogin'] = false;
			$GLOBALS['_title'] = "Error!";
			$GLOBALS['_message'] = "Wrong username or password! :(";

		}
		else
		{
			# make new db object
			$db = new db_util();

			$sql = "SELECT * FROM sow_users WHERE user_email='$uname'";
			$result = $db->query($sql);

			if($result!== FALSE){ // if there any error in sql then it will false
				if($result->num_rows > 0) { // if the sql execute successful then it give "num_ruws"
					//echo "user found!<br>";
					$row = $result->fetch_assoc();

					if(password_verify($pword, $row["user_pass"]))
					{
						$twoPassHash = hash('sha512', $row["user_pass"]);

						$host = $GLOBALS['c_host'];

						setcookie( $GLOBALS['c_name'], $row['display_name'], time() + (86400 * 30), '/', $host, false, true);
						setcookie( $GLOBALS['c_email'] , $uname, time() + (86400 * 30), '/', $host, false, true);
						setcookie( $GLOBALS['c_hash'] , $twoPassHash, time() + (86400 * 30), '/', $host, false, true);
						setcookie( $GLOBALS['c_id'] , $row['id'], time() + (86400 * 30), '/', $host, false, true);
						setcookie( $GLOBALS['c_isloggedin'] , true, time() + (86400 * 30), '/', $host, false, true);

						$GLOBALS['_isLogin'] = true;
						$GLOBALS['_title'] = "সফল!";
						$GLOBALS['_message'] = 'স্বাগতম <strong>'.$row['display_name'].'</strong>! :)';


					}
					else{

						$GLOBALS['_isLogin'] = false;
						$GLOBALS['_title'] = "ত্রুটি!";
						$GLOBALS['_message'] = 'ভুল ইমেইল বা পাসওয়ার্ড! :(';

					}

				}
				else 
				{
						$GLOBALS['_isLogin'] = false;
						$GLOBALS['_title'] = "সতর্ক হোন!";
						$GLOBALS['_message'] = 'আপনি এখনো নিবন্ধিত হননি!';


				}
			}
		}
	}

	loginNow();

	$data = array(
				"isLogin" => $_isLogin,
                "title" => $_title,
                "message" => $_message
            );

    echo json_encode($data);

    exit();

}

?>


<!DOCTYPE html>
<html lang="bn">
<head>

	<?php include 'must-head.php'; ?>
	
	<script>
		$(document).ready(function(){

			$('input[type="submit"]').addClass('disabled', false);
			
			$("input[type='email'], input[type='password']").keyup(function(){
				$val = $(this).val();
				if(!isASCII($(this).val())==true)
				{
					$(this).parent().addClass("has-error");
				}else{
					$(this).parent().removeClass("has-error");
				}

				checkToSubmit();

			});

			$( "form" ).submit(function( event ) {
				event.preventDefault();

                $uname = $("#uname").val();
                $pword = $("#pword").val();

                if($uname == "" || $pword == "")
                {
                    showInfo("Error!", "Fill all field!");
                    return false;
                }

                $options = {
                    uname: $uname,
                    pword: $pword
                };

                
                $.post("login.php", $options, function(data){

                    $obj = jQuery.parseJSON(data);

                    if($obj.isLogin==true)
                    {
                    	$("form")
                    	.replaceWith('<div class="alert alert-success">'+$obj.message+'<br>আপনাকে কিছুক্ষণের মধ্যে নীড় পাতায় নিয়ে যাওয়া হবে।<br>যদি স্বয়ংক্রিয় নীড় পাতায় নিয়ে যাওয়া না হয় তাহলে <a href="/socloud">এখানে</a> ক্লিক করুন।</div>');

                    	// JS function to redirect
                    	window.location = "/socloud";

                    }
                    else
                    {
                    	showInfo($obj.title, $obj.message);
                    }
                    
                });

                
            });
		});

		function checkToSubmit(){
			$name  = $("#uname").val();
			$pass = $("#pword").val();
			$(document).ready(function() {
				$('input[type="submit"]').addClass("disabled");
				if($pass!="" &&  $name!="" && isValidEmailAddress($name)==true)
				{
					$('input[type="submit"]').removeClass("disabled");
				}
			});
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

function displayForm()
{
	echo '<form class="form-box" role="form" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
	<div class="form-group">
		<label for="uname">E-mail:</label>
		<input id="uname" type="email" name="uname" class="form-control" placeholder="Enter your username" required>
	</div>

	<div class="form-group">
		<label for="pword">Password:</label>
		<input id="pword" type="password" name="pword" pattern=".{8,}"  title="Password should only contain uppercase, lowercase letters and number, and minimum 8 characters." class="form-control" placeholder="Enter your password" required>
	</div>

	<input type="submit" value="Login" class="btn btn-default">
</form>';
}



if(isLoggedIn())
{

	msg_redirectToHomePage('<div class="alert alert-warning">
<strong>Warning!</strong> You are already registered! :)
</div>');

}
else
{

displayForm();

}



?>

</div>

<?php include 'must-footer.php'; ?>

</body>

</html>

