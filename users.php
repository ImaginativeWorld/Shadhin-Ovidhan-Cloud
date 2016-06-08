<!DOCTYPE html>
<html lang="bn">
<head>

    <?php include 'must-head.php'; ?>

    <script>
        $(document).ready(function(){
            $(".upvote").click(function(){
                $val = $(this).attr("id");
                $newRating = +($("span.totalVote[id='"+$val+"']").text());
                
                // @voteType true means +1
                $.post("vote.php", {entryId: $val, voteType: 1, oldRating: $newRating}, function(data){

                    //alert(data);
                    $obj = jQuery.parseJSON(data);

                    //alert($obj.valChange+" "+$obj.hasError);
                    
                    if($obj.hasError==false)
                    {
                        $("span.totalVote[id='"+$val+"']").text($obj.valChange);
                        //alert(data);
                    }
                    else
                    {
                        showInfo("Error!","You already +1 this.");
                    }
                });
            });

            $(".downvote").click(function(){
                $val = $(this).attr("id");
                $newRating = +($("span.totalVote[id='"+$val+"']").text()); // +() make a string to int
                
                // @voteType false means -1
                $.post("vote.php", {entryId: $val, voteType: 0, oldRating: $newRating}, function(data){

                    //alert(data);
                    $obj = jQuery.parseJSON(data);

                    //alert($obj.valChange+" "+$obj.hasError);

                    if($obj.hasError==false)
                    {
                        $("span.totalVote[id='"+$val+"']").text($obj.valChange);
                        //alert(data);
                    }
                    else
                    {
                        showInfo("Error!","You already -1 this.");
                    }
                });
            });

            

        });



    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">

<?php

if(isLoggedIn() && isUserCan("control_user")){

    $total = 0;
    $from = 0;
    $maxShow = 30;
    $db = new db_util();

    $total =validateInput(isset($_GET['total']) ? $_GET['total'] : $total);   
    $from =validateInput(isset($_GET['from']) ? $_GET['from'] : $from); 
    //$to =validateInput(isset($_GET['to']) ? $_GET['to'] : $to);   


    if($total == 0) {
        $sql = "SELECT count(*) as total FROM sow_users, sow_usermeta
            WHERE sow_usermeta.user_id = sow_users.id AND sow_usermeta.meta_key = 'user_level'";
        $result = $db->query($sql);
        $var = $result->fetch_assoc();
        $total = intval($var['total']);

        $to = $from+$maxShow;

        if($total>=$maxShow)
        {
            //echo "1";
            $sql = "SELECT id, display_name, user_email, sow_usermeta.meta_value as role FROM sow_users, sow_usermeta
            WHERE sow_usermeta.user_id = sow_users.id AND sow_usermeta.meta_key = 'user_level' LIMIT ".$from.",".$maxShow;
        }
        else
        {
            $sql = "SELECT id, display_name, user_email, sow_usermeta.meta_value as role FROM sow_users, sow_usermeta
            WHERE sow_usermeta.user_id = sow_users.id AND sow_usermeta.meta_key = 'user_level'";
            //echo "2";
        }
     }  
     else
     {
        $sql = "SELECT id, display_name, user_email, sow_usermeta.meta_value as role FROM sow_users, sow_usermeta
            WHERE sow_usermeta.user_id = sow_users.id AND sow_usermeta.meta_key = 'user_level' LIMIT ".$from.",".$maxShow;
        //echo "3";
     }

     $to = (($from+$maxShow)<=$total ? ($from+$maxShow) : $total);


    $result = $db->select($sql);

    if($result!==false)
    {

        if(count($result) > 0) {

            echo "<h3 class='text-center'>Showing users <span class='label label-default'>".($from+1)." to ".$to."</span> (Total ",$total," users)</h3>";


            echo '<div class="table-responsive"><table class="table table-hover">';


            echo '<thead>
            <tr>
                <th>Name</th>
                <th>E-mail</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>';
            $role="";

            foreach ($result as $row) {

                switch ($row['role']) {
                    case '0':
                        $role = "Contributor";
                        break;

                    case '1':
                        $role = "Editor";
                        break;

                    case '10':
                        $role = "Administrator";
                        break;
                }


                echo '<tr>';

                echo '<td>';
                echo "<pstyle='font-size: 1.5em;'><strong>".$row["display_name"]."</strong>";
                if($row['role']!=10)
                    echo '<br><a href="#" class="btn btn-link remove" id="'.$row["id"].'">Remove</a>';
                echo '</td>';
                
                echo '<td>';
                echo "<pstyle='font-size: 1.5em;'><strong>".$row["user_email"]."</strong>";
                echo '</td>';

                echo '<td>';
                echo "<pstyle='font-size: 1.5em;'><strong>".$role."</strong>";
                if($row['role']==0)
                    echo '<br><a href="#" class="btn btn-link change-role" id="'.$row["id"].'">Make Editor</a>';
                if($row['role']==1)
                    echo '<br><a href="#" class="btn btn-link change-role" id="'.$row["id"].'">Make Contributor</a>';

                echo '</td>';

                // echo '<div class="btn-group">';
                // echo '<a class="btn btn-default upvote" id="'.$row["id"].'" href="#" ><span class="glyphicon glyphicon-thumbs-up"></span></a>';
                // echo '<a  class="btn btn-default disabled"><span class="badge totalVote" id="'.$row["id"].'">'.$row["total_vote"].'</span></a>';
                // echo '<a class="btn btn-default downvote" id="'.$row["id"].'" href="#"><span class="glyphicon glyphicon-thumbs-down"></span></a>';
                // echo '</div>';
            

                // echo '<div style="margin-left: 10px;" class="btn-group">';
                // echo '<a class="btn btn-default delete" id="'.$row["id"].'" href="#"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>';

                // echo '<a class="btn btn-default edit" id="'.$row["id"].'" href="#"><span class="glyphicon glyphicon-edit"></span> Edit</a>';
                // echo '</div>';


                // echo '</td>';
                echo '</tr>';

            }

            echo "</tbody></table></div>";

            if($total >= $maxShow)
            {
                echo '<ul class="pager">';

                $val1 = (($from-$maxShow)>=0 ? ($from-$maxShow) : 0);

                if($val1>=0 && $from!=0)
                    echo '<li><a href="?total='.$total.'&from='.$val1.'">Previous</a></li>';

                $val1=$to;

                if($to<$total)
                    echo '<li><a href="?total='.$total.'&from='.$val1.'">Next</a></li>';
                echo '</ul>';

            }

        }
        else
        {
            echo "<h3 class='text-center'>No entry yet!</h3>";
        }

    }
}
else
{
    
    msg_404notfound();

}


?>

    </div>

    <?php include 'must-footer.php'; ?>

</body>

</html>