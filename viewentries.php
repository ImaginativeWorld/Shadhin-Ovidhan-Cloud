<!DOCTYPE html>
<html lang="bn">
<head>

    <?php include 'must-head.php'; ?>

    <script>
        $(document).ready(function(){


            $("body").tooltip({ selector: '[data-toggle=tooltip]' });

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
                        showInfo("ত্রুটি!","একই অন্তর্ভুক্তিতে +১ ভোট আর কত বার দিবেন?");
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
                        showInfo("ত্রুটি!","একই অন্তর্ভুক্তিতে -১ ভোট আর কত বার দিবেন?");
                    }
                });
            });


            $("a.edit").click(function(event){
                $_entry_id = $(this).attr('id'); //c

                $_word = $("p.dic_word[id='"+$_entry_id+"'] strong").text(); 
                $_pos = $("p.dic_pos[id='"+$_entry_id+"']").text(); 
                $_meaning = $("p.dic_meaning[id='"+$_entry_id+"']").text(); 
                $_synonyms = $("p.dic_synonyms[id='"+$_entry_id+"']").text(); 


                //alert($_word);
                $("form input#word").val($_word);
                $("form input#pos").val($_pos);

                $arr = $_meaning.split(';');
                $now = "meaning";
                $now_meaning = "অর্থ";

                $("li.capsule").remove();

                $arr.forEach(function(currentValue){

                    $('<li class="capsule" data-toggle="tooltip" title="'+$now_meaning+' মুছতে ক্লিক করুন">'+currentValue+'</li>').insertBefore("li."+$now+"-li");
                    
                });

                if($_synonyms!="No Synonyms!")
                {
                    $arr = $_synonyms.split(';');
                    $now = "synonyms";
                    $now_meaning = "সমার্থক শব্দ";

                    $arr.forEach(function(currentValue){

                        $('<li class="capsule" data-toggle="tooltip" title="'+$now_meaning+' মুছতে ক্লিক করুন">'+currentValue+'</li>').insertBefore("li."+$now+"-li");
                        
                    });
                }


                $("#entryEditModal").modal('show');
            });

            // copied from add-entry module
            $("ul.capsule-ul").bind('keypress', function(e){
                
                var code = e.keyCode || e.which;
                $targetEle = $(e.target);

                if($targetEle.is("input#meaning"))
                {
                    $_now_element = "meaning";
                    $now = "অর্থ";
                }
                else
                {
                    $_now_element = "synonyms";
                    $now = "সমার্থক শব্দ";
                }

                // NOTE: Chrome Android: keypress return always zero. so need alternative solution. 
                if(code == 186 || code == 59) { //";" keycode
                    $val = $("input#"+$_now_element).val();
                    if($val!="") {
                        $('<li class="capsule" data-toggle="tooltip" title="'+$now+' মুছতে ক্লিক করুন">'+$val+'</li>').insertBefore("li."+$_now_element+"-li");
                        $("input#"+$_now_element).val("");

                        $('[data-toggle="tooltip"]').tooltip(); // as new element created, so init again..

                    }
                    return false;
                }
                if(code == 8) // delete keycode
                {
                    if($("input#"+$_now_element+", input#"+$_now_element).val()=="") {
                        $prev_el = $("li."+$_now_element+"-li").prev();
                        $("input#"+$_now_element).val($prev_el.text());
                        $prev_el.remove();
                        return false;
                    }
                }

                if($targetEle.is("input#synonyms"))
                {
                    if(code > 128) // check ASCII
                    {
                        
                        return false;
                        
                    }
                }

            });

            /*
                Dynamic Elements Event Handaling are done by
                .on function.. :3
            */
            $("ul.capsule-ul").on('click', 'li.capsule', function () {

                $(this).tooltip('hide');
                $(this).remove();

            });
            
        });



    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">

<?php

if(isLoggedIn() && isUserCan("vote")){
    $canDelete = isUserCan("delete");

    $total = 0;
    $from = 0;
    $maxShow = 10;
    $db = new db_util();

    $total =validateInput(isset($_GET['total']) ? $_GET['total'] : $total);   
    $from =validateInput(isset($_GET['from']) ? $_GET['from'] : $from); 


    if($total == 0) {
        $sql = "SELECT count(*) as total from ovidhan WHERE info<>'WEBsearch'";
        $result = $db->query($sql);
        $var = $result->fetch_assoc();
        $total = intval($var['total']);

        $to = $from+$maxShow;

        if($total>=$maxShow)
        {
            //echo "1";
            $sql = "SELECT * FROM ovidhan WHERE info<>'WEBsearch' ORDER BY word asc LIMIT ".$from.",".$maxShow;
        }
        else
        {
            $sql = "SELECT * FROM ovidhan WHERE info<>'WEBsearch' ORDER BY word asc";
            //echo "2";
        }
     }  
     else
     {
        $sql = "SELECT * FROM ovidhan WHERE info<>'WEBsearch' ORDER BY word asc LIMIT ".$from.",".$maxShow;
        //echo "3";
     }

     $to = (($from+$maxShow)<=$total ? ($from+$maxShow) : $total);


    $result = $db->select($sql);

    if($result!==false)
    {

        if(count($result) > 0) {

            echo "<h3 class='text-center'>দেখানো হচ্ছে  ".($from+1)." থেকে ".$to." অন্তর্ভুক্তি (সর্বমোট ",$total," অন্তর্ভুক্তি)</h3>";


            echo "<table class='table table-hover so-data-table'>";

            foreach ($result as $row) {

                echo '<tr><td>';

                echo '<p class="dic_word" id="'.$row["id"].'" style="font-size: 1.5em;"><strong>'.$row["pron"].'</strong> <small>';


                if($row["info"] == "NEW")
                    echo "<span class='label label-success text-uppercase'>নতুন</span>";
                else if($row["info"] == "SENTbyBUTTON")
                    echo "<span class='label label-danger text-uppercase'>অভিযোগ কৃত</span>";
                else  if($row["info"] == "MODIFIED") 
                    echo "<span class='label label-warning text-uppercase'>পরিবর্তিত</span>";
                else  if($row["info"] == "OnlineEntry") 
                    echo "<span class='label label-info text-uppercase'>অনলাইন অন্তর্ভুক্তি</span>";
                else
                    echo "<span class='label label-default text-uppercase'>".$row["info"]."</span>";

                echo '</small></p>';


                echo '<p class="dic_pos" id="'.$row["id"].'"><em>'.(($row["pos"]!='') ? $row["pos"] : '-').'</em></p>';

                # Processing Meaning
                $_pizza  = $row["meaning"];
                $_pieces = explode(";", $_pizza);

                echo '<ul class="inline capsule-ul">';
                foreach ($_pieces as $value) {
                    echo '<li class="capsule-readonly">',$value,'</li>';
                }
                echo '</ul>';

                echo '<p class="dic_meaning hide" id="'.$row["id"].'">'.$row["meaning"].'</p>';

                # Processing Synonyms
                if($row["synonyms"]!='')
                {
                    echo '<p>SYNONYMS</p>';
                    $_pizza  = $row["synonyms"];
                    $_pieces = explode(";", $_pizza);

                    echo '<ul class="inline capsule-ul">';
                    foreach ($_pieces as $value) {
                        echo '<li class="capsule-readonly">',$value,'</li>';
                    }
                    echo '</ul>';

                    echo '<p class="dic_synonyms hide" id="'.$row["id"].'">'.$row["synonyms"].'</p>';
                }
                else
                {
                    echo '<p class="dic_synonyms" id="'.$row["id"].'">No Synonyms!</p>';
                }

                # Buttons
                echo '<div class="btn-group">';
                echo '<a class="btn btn-default upvote" id="'.$row["id"].'" href="#" ><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
</a>';
                echo '<a  class="btn btn-default disabled"><span class="badge totalVote" id="'.$row["id"].'">'.$row["total_vote"].'</span></a>';
                echo '<a class="btn btn-default downvote" id="'.$row["id"].'" href="#"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
</a>';
                echo '</div>';
            

                echo '<div style="margin-left: 10px;" class="btn-group">';
                if($canDelete)
                    echo '<a class="btn btn-default delete" id="'.$row["id"].'" href="#"><i class="fa fa-times" aria-hidden="true"></i>
 মুছুন</a>';

                echo '<a class="btn btn-default edit" id="'.$row["id"].'" href="#"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
 সম্পাদন</a>';
                echo '</div>';


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
            echo "<h3 class='text-center'>এখনো কোনো অন্তর্ভুক্তি নেই!</h3>";
        }

    }
}
else
{
    
    msg_404notfound();

}


?>

    </div>

    <div class="container">
        <!-- Modal -->
        <div id="entryEditModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">অন্তর্ভুক্তি পরিবর্তন / পরিবর্ধন করুন</h4>
                    </div>
                    <div class="modal-body">

                        <!-- Direct copy paste from add entry module -->
                        <form class="form-box-lg" role="form" action="" method="post">
                          <div class="form-group">
                           <label for="word">শব্দ / শব্দ গুচ্ছ <span class="text-danger">(আবশ্যক)</span></label>
                           <input type="text" pattern="[A-Za-z0-9-\' ]{1,50}" name="word" class="form-control" id="word" placeholder="ইংরেজি শব্দ অথবা শব্দ-গুচ্ছ এখানে লিখুন" required>
                       </div>

                       <div class="form-group">
                          <label for="pos">পদপ্রকরণ</label>
                          <input type="text" pattern="[A-Za-z,-/ ]{1,25}" name="pos" class="form-control" id="pos" placeholder="পদ এখানে লিখুন
                          ">
                          <span class="help-block alert alert-warning">পদ প্রকরণ সমূহের সংক্ষিপ্ত রূপ লিখুন। যেমনঃ <strong>n</strong> (noun), <strong>pro</strong> (pronoun), <strong>v</strong> (verb), <strong>adv</strong> (adverb), <strong>adj</strong> (adjective),  <strong>pre</strong> (preposition), <strong>conj</strong> (conjunction) ইত্যাদি।</span>
                      </div>

                      <div class="form-group">
                        <label for="meaning">বাংলা অর্থ <span class="text-danger">(আবশ্যক)</span></label>
                        <ul class="form-control meaning inline capsule-ul">
                            <li class="meaning-li">
                                <input name="meaning" class="no-design capsule" id="meaning" placeholder="বাংলা অর্থ লিখুন এখানে">

                            </li>
                        </ul>
                        <span class="help-block alert alert-warning">প্রতিটি অর্থ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>
                    </div>


                    <div class="form-group">
                     <label for="synonyms">সমার্থক শব্দ</label>
                     <ul class="form-control synonyms inline capsule-ul">
                        <li class="synonyms-li">
                            <input name="synonyms" pattern="[A-Za-z;,\' -/]{1,1000}" class="no-design capsule" id="synonyms" placeholder="সমার্থক শব্দ লিখুন এখানে">
                        </li>
                    </ul>
                    <span class="help-block alert alert-warning">
                        প্রতিটি সমার্থক শব্দ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>
                    </div>


                    <input type="submit" value="পরিবর্তন সংরক্ষণ করুন" class="btn btn-primary">
                </form>



                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'must-footer.php'; ?>

</body>

</html>