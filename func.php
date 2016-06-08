<?php
/*
Common Functions are live here.. :)
*/
include 'cnames.php';
include 'db-util.php';

function validateInput($data) {
   $data = trim($data); //Strip unnecessary characters (extra space, tab, newline)
   $data = stripslashes($data); //Remove backslashes (\)
   $data = htmlspecialchars($data, ENT_NOQUOTES, "UTF-8");
   return $data;
}

function isValidEmailAddress($emailAddress) {
   return filter_var($emailAddress, FILTER_VALIDATE_EMAIL) !== false;
}

function clean($string) {
   return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}

/*
Loose check if any user logged in now
*/
function isLoggedIn() {

   if(isset($_COOKIE[$GLOBALS["c_isloggedin"]])==true && isset($_COOKIE[$GLOBALS["c_email"]])==true) {

   $cookie_isloggedin =  $_COOKIE[$GLOBALS["c_isloggedin"]];
   $cookie_email =  $_COOKIE[$GLOBALS["c_email"]];

   if($cookie_isloggedin == true && isValidEmailAddress($cookie_email)==true)
      {
         return true;
      }
      else
         return false;
   }
   else
   {
      return false;
   }
}

/*
Hard check if any user logged in now
*/
function isReallyLoggedIn(){

    if(isset($_COOKIE[$GLOBALS['c_email']])==true) {

        $cookie_uname =  validateInput($_COOKIE[$GLOBALS['c_email']]);
        $cookie_hash = $_COOKIE[$GLOBALS['c_hash']];

        if(isValidEmailAddress($cookie_uname)===false) return false;

        $db = new db_util();

        $sql = "SELECT * FROM sow_users WHERE user_email='$cookie_uname'";
        $result = $db->query($sql);

        if($result!== FALSE){ # if there any error in sql then it will false
            if($result->num_rows > 0) { # if the sql execute successful then it give "num_ruws"
            
            $row = $result->fetch_assoc();
            $twoPassHash = hash('sha512', $row["user_pass"]);

            return true;
            }
        }
   }

   return false;
}

/*

*/
function isUserCan($operation)
{
    if(isset($_COOKIE[$GLOBALS['c_id']])==true) 
    {
        $user_id =  validateInput($_COOKIE[$GLOBALS['c_id']]);

        $db = new db_util();
        $user_level = $db->get_user_level($user_id);

        if($user_level!==FALSE)
        {

            switch ($operation)
            {
                case 'add':
                    // As now all user can Add so nothing compare here
                    return true;
                    break;

                case 'edit':
                    // As now all user can Edit so nothing compare here
                    return true;
                    break;

                case 'vote':
                    // As now all user can Vote so nothing compare here
                    return true;
                    break;

                case 'delete':
                    if(intval($user_level) == 10)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                    break;
                case 'control_user':
                    if(intval($user_level) == 10)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                    break;
            }
        }
    }

    return false;

}

/*
Show 404 message
*/
function msg_404notfound() {

    //php function to check if headers sent or not
    if (!headers_sent()) {
        header( "refresh:5;url=/socloud" );
    }

    echo '<div class="alert alert-warning">
<strong>৪০৪ - পাওয়া যায়নি!!!</strong>
</div>';

}

/*
Show a message and Redirect to Homepage (if possible)
*/
function msg_redirectToHomePage($msg=null) {

    //php function to check if headers sent or not
    if (!headers_sent()) {
        header( "refresh:5;url=/socloud" );
    }

    if($msg===null){
    echo 'আপনাকে কিছুক্ষণের মধ্যে নীড় পাতায় নিয়ে যাওয়া হবে।';
    }
    else
    {
        echo $msg;
    }

}

?>