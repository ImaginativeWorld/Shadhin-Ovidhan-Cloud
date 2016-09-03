<!DOCTYPE html>
<html lang="bn">
<head>

    <?php include 'must-head.php'; ?>

    <script>
        $(document).ready(function(){

            
        });



    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">

<?php

if(isLoggedIn()){

    $total = 0;
    $from = 0;
    $maxShow = 10;
    $db = new db_util();

    $total =validateInput(isset($_GET['total']) ? $_GET['total'] : $total);   
    $from =validateInput(isset($_GET['from']) ? $_GET['from'] : $from); 


    if($total == 0) {
        $sql = "SELECT count(*) as total from ovidhan WHERE info='SUGGESTION'";
        $result = $db->query($sql);
        $var = $result->fetch_assoc();
        $total = intval($var['total']);

        $to = $from+$maxShow;

        if($total>=$maxShow)
        {
            //echo "1";
            $sql = "SELECT * FROM ovidhan WHERE info='SUGGESTION' ORDER BY id asc LIMIT ".$from.",".$maxShow;
        }
        else
        {
            $sql = "SELECT * FROM ovidhan WHERE info='SUGGESTION' ORDER BY id asc";
            //echo "2";
        }
     }  
     else
     {
        $sql = "SELECT * FROM ovidhan WHERE info='SUGGESTION' ORDER BY id asc LIMIT ".$from.",".$maxShow;
        //echo "3";
     }

     $to = (($from+$maxShow)<=$total ? ($from+$maxShow) : $total);


    $result = $db->select($sql);

    if($result!==false)
    {

        if(count($result) > 0) {

            echo "<h3 class='text-center'>দেখানো হচ্ছে  ".($from+1)." থেকে ".$to." বার্তা (সর্বমোট ",$total," বার্তা)</h3>";


            echo "<table class='table table-hover so-data-table'>";

            foreach ($result as $row) {

                echo '<tr><td>';

                echo '<p class="dic_word" id="'.$row["id"].'"><strong>'.$row["pron"].'</strong>';

                $_pizza  = $row["meaning"];
                
                echo '<p class="suggestion-msg" style="font-size: 1.3em;"><pre>',$_pizza,'</pre></p>';

                #cho '<p class="dic_meaning hide" id="'.$row["id"].'">'.$row["meaning"].'</p>';


                #echo '<div style="margin-left: 10px;" class="btn-group">';
                
                #echo '<a class="btn btn-default delete" id="'.$row["id"].'" href="#"><i class="fa fa-times" aria-hidden="true"></i> মুছুন</a>';

                #echo '</div>';


                echo '</td>';
                echo '</tr>';

            }

            echo "</table>";

            if($total >= $maxShow)
            {
                echo '<ul class="pager">';

                $val1 = (($from-$maxShow)>=0 ? ($from-$maxShow) : 0);

                if($val1>=0 && $from!=0)
                    echo '<li><a href="?total='.$total.'&from='.$val1.'"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>
 পূর্ববর্তী</a></li>';

                $val1=$to;

                if($to<$total)
                    echo '<li><a href="?total='.$total.'&from='.$val1.'">পরবর্তী <i class="fa fa-chevron-circle-right" aria-hidden="true"></i>
</a></li>';
                echo '</ul>';

            }

        }
        else
        {
            echo "<h3 class='text-center'>এখনো কোনো বার্তা নেই!</h3>";
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