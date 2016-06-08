<?php
/*
	check if a word already exists in database or not
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	include '../func.php';

	function check_item_is_exist()
	{
	    //connect to database  
		$db = new db_util();

		//get the item
		$item = mysql_real_escape_string($_POST['item']);  

		$result = $db->query('SELECT item FROM `item_list_en` where item = "'. $item .'"');  

		//if number of rows fields is bigger them 0 that means it's NOT available '  
		if($result->num_rows>0){
		    //and we send 0 to the ajax request  
			echo 1;
		}else{
		    //else if it's not bigger then 0, then it's available '  
		    //and we send 1 to the ajax request  
			echo 0;
		}
	}

	check_item_is_exist();


}

?>