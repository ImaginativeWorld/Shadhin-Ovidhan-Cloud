<?php

include "func.php";

$db = new db_util();

$query =validateInput(isset($_GET['query']) ? $_GET['query'] : $total);
$cleanQ = clean($query);

$_item = $query;
$_pos = array();

$sql = "SELECT * FROM o_collaboration_en_bn WHERE item=$query";

$result = $db->select($sql);

if($result!==false)
{

    foreach ($result as $row) {
        $_pos[$row["pos"]] = $row["meaning"]
    }

    

}
else
{
    echo 0;
}

}



?>