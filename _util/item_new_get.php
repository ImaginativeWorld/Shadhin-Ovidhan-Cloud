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
			$item_index = mysql_real_escape_string(isset($_POST['item_index']) ? $_POST['item_index'] : NULL);
			//$completed_item_id = mysql_real_escape_string(isset($_POST['completed_item_index']) ? $_POST['completed_item_index'] : NULL);

			
				if($item_index==NULL)
				{
					$result = $db->query('SELECT * FROM `item_list_en_new` ORDER BY id ASC LIMIT 1');
					if($result!==FALSE)
					{
						if($result->num_rows>0){
							$row = $result->fetch_assoc();

							$data = array(
								"id" => $row["id"],
								"item" => $row["item"]
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
					$result = $db->query("SELECT * FROM `item_list_en_new` ORDER BY id ASC LIMIT $item_index, 1");
					if($result!==FALSE)
					{
						if($result->num_rows>0){
							$row = $result->fetch_assoc();

							$data = array(
								"id" => $row["id"],
								"item" => $row["item"]
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