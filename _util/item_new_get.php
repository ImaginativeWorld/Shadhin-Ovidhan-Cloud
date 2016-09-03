<?php
/*
	get the a new word from database
*/
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		include '../func.php';

		function get_new_item()
		{
	    //connect to database  
			$db = new db_util();

		//get the item
			$item_index = validateInput(isset($_POST['item_index']) ? $_POST['item_index'] : NULL);
			//$completed_item_id = mysql_real_escape_string(isset($_POST['completed_item_index']) ? $_POST['completed_item_index'] : NULL);

			
				if($item_index==NULL)
				{
					$result = $db->query('SELECT id, pron FROM `item_list_en_new` LIMIT 1');
					if($result!==FALSE)
					{
						if($result->num_rows>0){
							$row = $result->fetch_assoc();

							$data = array(
								"id" => 1,
								"item" => $row["pron"]
							);

        					echo json_encode($data);

						}else{
							echo 0;
						}
					}
					else
						echo 0;
				}
				else
				{
					// PROBLEM (Solved)
					// $item_index need to be a real index number like 1,2,3.....
					// but if you delete a recode from inside, it brake the index number!
					$result = $db->query("SELECT id, pron FROM `item_list_en_new` LIMIT $item_index, 1");
					if($result!==FALSE)
					{
						if($result->num_rows>0){
							$row = $result->fetch_assoc();
							$new_id = intval($item_index)+1;

							$data = array(
								"id" => $new_id,
								"item" => $row["pron"]
							);

        					echo json_encode($data);

						}else{
							echo 0;
						}
					}
					else
						echo 0;
				}
			

		}

		get_new_item();


	}

	?>