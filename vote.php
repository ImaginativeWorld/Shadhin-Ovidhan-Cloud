<?php

/*
CHANGE LOG
----------------------------------------
Version 0.0
- 
*/
include 'func.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	function voteNow(){
		$resultValue = 0;
		$hasError = false;

		// Believe me, below all are String.. :3
		$userId =  validateInput($_COOKIE[$GLOBALS['c_id']]);
		$voteType =validateInput(isset($_POST['voteType']) ? $_POST['voteType'] : '');	
		$entryId =validateInput(isset($_POST['entryId']) ? $_POST['entryId'] : '');	
		$oldRating =validateInput(isset($_POST['oldRating']) ? $_POST['oldRating'] : '');	

		$_finalRating = $oldRating;

		//echo "oldRating: ",$oldRating, " ";

		$db = new db_util();

		$sql = "SELECT * FROM sow_vote WHERE entry_id='$entryId' AND user_id='$userId'";
		$result = $db->query($sql);

		if($result!== FALSE){ # if there any error in sql then it will false
			if($result->num_rows > 0) { //0 means no vote on that entry by this user yet
			 # if the sql execute successful then it give "num_ruws"
				$row = $result->fetch_assoc(); # get the 1st row of the result. for above query only one result will be present here.

				//echo '[',$row["vote_value"],']-[',$voteType,']';

				if($row["vote_value"]!= $voteType)
				{
					//echo ' {Dhukse} ';

					$stmt = $db->prepare("UPDATE sow_vote
						SET vote_value=?
						WHERE entry_id=? AND user_id=?");

					$stmt->bind_param("iii", $_voteType, $_entryId, $_userId);

					$_entryId = $entryId;
					$_userId = $userId;
					$_voteType = $voteType;

					$result = $stmt->execute();

					if($result!==false) // or die("FALSE"); //mysqli_error($db->conn));
					{

						if($voteType=="1"){
							$resultValue = 2;
						}
						else{
							$resultValue = -2;
						}
					}
					else $hasError=true;
				}
				else $hasError=true;
			}
			else
			{

				$stmt = $db->prepare("INSERT INTO sow_vote(entry_id , user_id, vote_value)
					VALUES(?,?,?)");

				$stmt->bind_param("iii", $_entryId, $_userId, $_voteType);

				$_entryId = intval($entryId);
				$_userId = $userId;
				$_voteType = $voteType;

				if($stmt->execute()!==false) // or die("FALSE"); //mysqli_error($db->conn));
				{

					if($voteType=="1")
						$resultValue = 1;
					else
						$resultValue = -1;
				}
				else $hasError = true;	
			}

			if(!$hasError)
			{
				# Second part
				$stmt = $db->prepare("UPDATE ovidhan 
					SET total_vote=?
					WHERE id=?");

				$stmt->bind_param("ii", $_finalRating, $_entryId);

				$_finalRating = intval($oldRating)+intval($resultValue);

				//echo '[[',intval($oldRating),' + ',intval($resultValue),' = ',$_finalRating,']]';

				$_entryId = intval($entryId);

				if($stmt->execute()===false) // or die("FALSE"); //mysqli_error($db->conn));
				{
					$hasError = true;
				}
			}

			//die("TRUE");
			
		}

		$data = array(
				"hasError" => $hasError,
				"valChange" => $_finalRating
			);

		echo json_encode($data);
	}

	if(isLoggedIn())
	{
		voteNow();
	}
	
}
else
{

	include 'not-found.php';

}
?>