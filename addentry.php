<?php
 
# TODO:
# Add reset Button

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'func.php';

    $_title = "";
    $_message = "";

    function addNewEntry(){

        $userId =  $_COOKIE[$GLOBALS['c_id']];
        $infO = "OnlineEntry";  
        $proN =validateInput(isset($_POST['word']) ? $_POST['word'] : '');
        $worD = clean($proN);
        $poS =validateInput( isset($_POST['pos']) ? $_POST['pos'] : '');
        $meaninG = validateInput(isset($_POST['meaning']) ? $_POST['meaning'] : '');
        $synonymS =validateInput( isset($_POST['synonyms']) ? $_POST['synonyms'] : '');

        $db = new db_util();

//check if the value already exist or not
        $sql = "SELECT * FROM ovidhan WHERE word= '$worD'";
        $result = $db->query($sql);

        if($result->num_rows == 0)
        {
            $stmt = $db->prepare("INSERT INTO ovidhan(info,word,pron,pos,meaning,synonyms, creator_id )
                VALUES(?,?,?,?,?,?,?)");

            $stmt->bind_param("ssssssi", $_infO, $_worD, $_proN, $_poS, $_meaninG, $_synonymS, $_userId);

            $_infO = $infO;
            $_worD = $worD;
            $_proN = $proN;
            $_poS = $poS;
            $_meaninG = $meaninG;
            $_synonymS = $synonymS;
            $_userId = $userId;

            $result = $stmt->execute();

            if($result===true)
            {

                $GLOBALS['_title'] = "সফল!";
                $GLOBALS['_message'] = 'নতুন অন্তর্ভুক্তি "'.$proN.'" সফলভাবে যুক্ত হয়েছে! :)';
                
            }
            else
            {

//             displayNewEntryForm();

                $GLOBALS['_title'] = "ত্রুটি!";
                $GLOBALS['_message'] = 'অন্তর্ভুক্তি যুক্ত করা যায়নি! :(';
            }       

            $stmt->close();


        }
        else
        {

            $GLOBALS['_title'] = "সতর্ক হোন!";
            $GLOBALS['_message'] = 'শব্দ/শব্দ-গুচ্ছ "'.$proN.'" ডেটাবেজে ইতিমধ্যে যুক্ত করা আছে! :/';

        }


    }

    if(isReallyLoggedIn())
    {
        addNewEntry();
    }
    else
    {
        include 'kickout.php';
    }

    $data = array(
                "title" => $_title,
                "message" => $_message
            );

        echo json_encode($data);

    exit();

}


?>

<!DOCTYPE html>
<html lang="bn">
<head>

    <?php include 'must-head.php'; ?>

    <script type="text/javascript">
        $(document).ready(function(){

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

            //==============================================
            $("#word").keyup(function(){
                if($("#word").val().length > 0)
                {
                    check_availability();
                }
            });

            //function to check username availability  
            function check_availability(){  
              
                    //get the username  
                    $item = $('#word').val();  
              
                    //use ajax to run the check  
                    $.post("_util/item_check.php", { item: $item },  
                        function(result){  
                            //if the result is 1  
                            //console.log(result);
                            if(result == 1){  
                                $("div.item_info")
                                    .text("The word already in database!")
                                    .addClass("alert alert-danger");
                                    
                            }else{  
                                $("div.item_info")
                                    .text("")
                                    .removeClass("alert alert-danger");
                            }  
                    });  
              
            }

            /*
                Giving some design
            */
            $("li input").focus(function(){
                //$("ul.synonyms").addClass("input-focus");
                $(this).parentsUntil("div").not("input, li").addClass("input-focus");
            });
            $("li input").blur(function(){
                //$("ul.synonyms").removeClass("input-focus");
                $(this).parentsUntil("div").not("input, li").removeClass("input-focus");
            });
            $(".capsule-ul").click(function(){
                $(this).find("input").focus();
            });


            $( "form" ).submit(function( event ) {

                $word = $("#word").val();
                $pos = $("#pos").val();
                $meaning = "";
                $synonyms = "";

                $("ul.meaning li.capsule").each(function() {

                        if($meaning=="")
                            $meaning = $(this).text();
                        else
                            $meaning += "; "+$(this).text();
                });

                if($meaning == "")
                {
                    showInfo("ত্রুটি!", "কোনো অর্থ পাওয়া যায়নি! প্রতিটি অর্থ লিখার পর অবশ্যই একটি করে সেমি-কোলোন (\";\") দিতে হবে।");
                    return false;
                }

                $("ul.synonyms li.capsule").each(function() {

                        if($synonyms=="")
                            $synonyms = $(this).text();
                        else
                            $synonyms += "; "+$(this).text();
                });

                //alert($meaning);

                $options = {
                    word: $word,
                    pos: $pos,
                    meaning: $meaning,
                    synonyms: $synonyms
                };

                //TODO
                // - Check if the word already in db or not
                $.post("addentry.php", $options, function(data){

                    $obj = jQuery.parseJSON(data);

                    showInfo($obj.title, $obj.message);
                    
                });

                event.preventDefault();
            });


        });

    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">

    <div class="form-group">
        <label for="item_index">Select which alphabet you like most</label>
        <select id="item_index" name="item_index" class="form-control dropdown">
        <?php
        for($i=0;$i<26;$i++)
        {
            echo '<option value="'.chr(97+$i).'">'.chr(65+$i).'</option>';
            
        }
        ?>
        </select>
    </div>

    <?php

/*
CHANGE LOG
----------------------------------------
Version 0.0
- 
*/


function displayNewEntryForm()
{
  echo '<form class="form-box-lg" role="form" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
  <div class="form-group">
     <label for="word">শব্দ / শব্দ গুচ্ছ <span class="text-danger">(আবশ্যক)</span></label>
     <input type="text" pattern="[A-Za-z0-9-\' ]{1,50}" name="word" class="form-control" id="word" placeholder="ইংরেজি শব্দ অথবা শব্দ-গুচ্ছ এখানে লিখুন" required>
     <div class="item_info"></div>
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
        <input type="text" name="meaning" class="no-design capsule" id="meaning">
        
        </li>
    </ul>
    <span class="help-block alert alert-warning">প্রতিটি অর্থ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>
</div>


<div class="form-group">
   <label for="synonyms">সমার্থক শব্দ</label>
   <ul class="form-control synonyms inline capsule-ul">
        <li class="synonyms-li">
        <input type="text" name="synonyms" pattern="[A-Za-z;,\' -/]{1,1000}" class="no-design capsule" id="synonyms">
        </li>
    </ul>
   <span class="help-block alert alert-warning">
প্রতিটি সমার্থক শব্দ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>
</div>


<input type="submit" value="অন্তর্ভুক্তি যুক্ত করুন" class="submit btn btn-primary">
<input type="reset" value="রিসেট" class="btn btn-primary">
</form>';
}



    if(isLoggedIn() && isUserCan("add"))
    {
        displayNewEntryForm();
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