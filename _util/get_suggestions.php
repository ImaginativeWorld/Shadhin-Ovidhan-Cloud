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

		$arr = $db->select('SELECT pron FROM `item_list_en` where item LIKE "'. clean($item) .'%" LIMIT 5');  

		if($arr!==FALSE)
			echo json_encode($arr);
		else
			echo 0;

		//if number of rows fields is bigger them 0 that means it's NOT available '  
		// if($result->num_rows>0){
		    //and we send 0 to the ajax request  
		// 	echo 1;
		// }else{
		    //else if it's not bigger then 0, then it's available '  
		    //and we send 1 to the ajax request  
		// 	echo 0;
		// }
	}

	check_item_is_exist();


}

?>