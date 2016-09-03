<?php
 
# TODO:
# Add reset Button

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'func.php';

    $_title = "";
    $_message = "";

    function addNewEntry(){

        $userId =  $_COOKIE[$GLOBALS['c_id']];
        //$infO = "OnlineEntry";  
        $proN =validateInput(isset($_POST['word']) ? $_POST['word'] : '');
        $worD = clean($proN);
        //$poS =validateInput( isset($_POST['pos']) ? $_POST['pos'] : '');
        $meaninG = isset($_POST['meaning']) ? $_POST['meaning'] : NULL;
        $synonymS =validateInput( isset($_POST['synonyms']) ? $_POST['synonyms'] : '');
        $antonymS =validateInput( isset($_POST['antonyms']) ? $_POST['antonyms'] : '');

        if($meaninG===NULL)
        {
            echo 0;
            exit();
        }


        // echo $proN,"\n";
        // echo $worD,"\n";

        // echo gettype ($meaninG),"\n";

        // echo $synonymS,"\n";
        // echo $antonymS;

        $db = new db_util();

        //check if the value already exist or not
        $sql = "SELECT * FROM o_collaboration_en_bn WHERE item= '$worD'";
        $result = $db->query($sql);

        if($result->num_rows == 0)
        {
            // Meaning part
            $stmt = $db->prepare("INSERT INTO o_collaboration_en_bn(item, pron, pos, meaning, creator_id, revision)
                VALUES(?,?,?,?,?,?)");

            $stmt->bind_param("ssssii", $_worD, $_proN, $_poS, $_meaninG, $_userId, $_revision);

            $_worD = $worD;
            $_proN = $proN;
            $_userId = $userId;
            $_revision = 0;

            foreach ($meaninG as $key => $value) {
                $_poS = $key;
                $_meaninG = $value;

                if($stmt->execute()===false)
                {
                    echo 0;
                    exit();
                }
            }

            $stmt->close();

            // Synonyms part
            $stmt = $db->prepare("INSERT INTO o_collaboration_syno_anto(item, synonyms, antonyms, creator_id, revision)
                VALUES(?,?,?,?,?)");

            $stmt->bind_param("sssii", $_worD, $_synonymS, $_antonymS, $_userId, $_revision);

            $_worD = $worD;
            $_userId = $userId;
            $_synonymS = $synonymS; 
            $_antonymS = $antonymS;
            $_revision = 0;

            if($stmt->execute()===false)
            {
                echo 0;
                exit();
            }


            $stmt->close();

            // add to word list
            $stmt = $db->prepare("INSERT INTO item_list_en(item)
                VALUES(?)");

            $stmt->bind_param("s", $_proN);

            $_proN = $proN;

            if($stmt->execute()===false)
            {
                echo 0;
                exit();
            }

            $stmt->close();

            // delete from new word list
            $stmt = $db->prepare("DELETE FROM item_list_en_new WHERE item = ?");

            $stmt->bind_param("s", $_proN);

            $_proN = $proN;

            if($stmt->execute()===false)
            {
                echo 0;
                exit();
            }

            $stmt->close();
            

            $GLOBALS['_title'] = "সফল!";
            $GLOBALS['_message'] = 'নতুন অন্তর্ভুক্তি "'.$proN.'" সফলভাবে যুক্ত হয়েছে! :)';
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

            $("form").bind('keypress', function(e){
                
                var code = e.keyCode || e.which;
                $targetEle = $(e.target);

                if($targetEle.is("input#synonyms"))
                {
                    $_now_element = "synonyms";
                    $_now_element_id = $_now_element;
                    $now = "সমার্থক শব্দ";
                }
                else if($targetEle.is("input#antonyms"))
                {
                    $_now_element = "antonyms";
                    $_now_element_id = $_now_element;
                    $now = "বিপরীতার্থক শব্দ";
                }
                else
                {
                    $_now_element = "meaning";
                    $_now_element_id = $targetEle.attr("id");
                    $now = "অর্থ";
                }

                // NOTE: Chrome Android: keypress return always zero. so need alternative solution.
                if(code == 186 || code == 59) { //";" keycode
                    $val = $targetEle.val();
                    // console.log("val: "+$val);
                    // console.log("attr: "+$_now_element_id);
                    if($val!="") {
                        $('<li class="capsule" data-toggle="tooltip" title="'+$now+' মুছতে ক্লিক করুন">'+$val+'</li>').insertBefore($targetEle.parent());
                        $targetEle.val("");

                        $('[data-toggle="tooltip"]').tooltip(); // as new element created, so init again..

                    }
                    return false;
                }

                if(code == 8) // delete keycode
                {
                    //+", input#"+$_now_element
                    if($targetEle.val()=="") {
                        $prev_el = $targetEle.parent().prev();
                        $targetEle.val($prev_el.text());
                        $prev_el.remove();
                        return false;
                    }
                }

                if($targetEle.is("input#synonyms, input#antonyms"))
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
            $("form").on('click', 'li.capsule', function () {
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
            /*
                Dynamic Elements Event Handaling are done by
                .on function.. :3
            */
            $("form").on('focus', 'li input', function () {
                $(this).parentsUntil("div").not("input, li").addClass("input-focus");
            });
            $("form").on('blur', 'li input', function () {
                $(this).parentsUntil("div").not("input, li").removeClass("input-focus");
            });
            $("form").on('click', '.capsule-ul', function () {
                $(this).find("input").focus();
            });

            /*
                Lets reset all ;)
            */
            $("input[type=reset]").click(function(){
                $("li.capsule").tooltip('hide');
                $("li.capsule").remove();
            });

            /*
                Get word
            */
            $("input.skip").click(function(){

                $id = $("input.skip").attr("id");
				
				//console.log($id);

                $.post("_util/item_new_get.php", { item_index: $id },  
                    function(result){  
                        //console.log(result);
                        if(result == 0){ 

                            console.log("Error input.skip!");

                        }else{
                            //console.log(result);
                            
                            $obj = jQuery.parseJSON(result);
                            $('#word').val($obj.item);
                            $("input.skip").attr("id", $obj.id);

                            // remove the alert dialog (already in database)
                            $("div.item_info")
                                    .text("")
                                    .removeClass("alert alert-danger");
									
							
                        }
                    });  
                
            });

            /**
				Show in Google Translate
            */
            /*
                Get word
            */
            $("input.gt-go").click(function(){


            		var __word, __id;
                	$id = $("input.skip").attr("id");

                	for(var k=1;k<=20;k++)
                	{
		                $.post("_util/item_new_get.php", { item_index: $id },  
		                    function(result){  
		                        //console.log(result);
		                        if(result == 0){ 

		                            console.log("Error input.skip!");

		                        }else{
		                            //console.log(result);
		                            
		                            $obj = jQuery.parseJSON(result);

		                            //__word = $obj.item;
		                            //__id = $obj.id;
		                            //$('#word').val($obj.item);
		                            //$("input.skip").attr("id", $obj.id);
		                            //console.log($obj.id);
									//console.log($obj.item);
		                            // remove the alert dialog (already in database)
		                            $("div.item_info")
		                                    .text("")
		                                    .removeClass("alert alert-danger");
		                            var __url = "https://translate.google.com/#en/bn/" + $obj.item;
		                            window.open(__url, '_blank');
										
		                        }
		                    });

		                $id++;
                	}

					$('#word').val("");
                    $("input.skip").attr("id", $id);

            });

            function get_word()
            {
                $.post("_util/item_new_get.php",  
                    function(result){  
                        //console.log(result);
                        if(result == 0){ 
                            console.log("Error fn g_w!");
                        }else{
                            $obj = jQuery.parseJSON(result);
                            $('#word').val($obj.item);
                            $("input.skip").attr("id", $obj.id);
                        }
                    });  
            }

            get_word();

            $("input.add-pos").click(function(){
                $(this).before('<div id="meaning-0" class="form-group meaning"> \
<label for="pos">পদপ্রকরণ</label> \
<select class="form-control" id="opt-pos"> \
<option value="0" selected="selected">Not defined</option> \
<option value="n">Noun</option> \
<option value="pro">Pronoun</option> \
<option value="v">Verb</option> \
<option value="adv">Adverb</option> \
<option value="adj">Adjective</option> \
<option value="pre">Preposition</option> \
<option value="conj">Conjunction</option> \
<option value="i">Phrase/Idioms</option> \
</select>  \
<label for="meaning">বাংলা অর্থ</label> \
<ul class="form-control meaning inline capsule-ul" id="meaning-0"> \
<li class="meaning-li"> \
<input type="text" name="meaning" class="no-design capsule" id="meaning-0"> \
</li> \
</ul> \
<input class="btn btn-primary" type="button" id="remove-pos" value="এ পদপ্রকরণটি বাদ দিন"> \
</div>');
            });

            /*
                Dynamic Elements Event Handaling are done by
                .on function.. :3
            */
            $("form").on('click', 'input#remove-pos', function () {

                $(this).parentsUntil("form").remove();

            });

            $("form").on('click keypress', 'select#opt-pos', function () {
                $val = $(this).val();
                $(this).parent().attr("id", "meaning-"+$val);
                $("div#meaning-"+$val+" ul").attr("id", "meaning-"+$val);
                $("div#meaning-"+$val+" ul#meaning-"+$val+" input").attr("id", "meaning-"+$val);
            });

            $( "form" ).submit(function( event ) {
                event.preventDefault();
                
//TODO Use map

                 $word = $("#word").val();
                // $pos = $("#pos").val();
                 $meaning = "";
                 $synonyms = "";
                 $antonyms = "";

                var arr = {};
                var data = {};
                arr["0"]=0;
                arr["n"]=0;
                arr["pro"]=0;
                arr["v"]=0;
                arr["adv"]=0;
                arr["adj"]=0;
                arr["pre"]=0;
                arr["conj"]=0;
                arr["i"]=0;

                $flag=0;
                
                $( "select#opt-pos.form-control" ).each(function() {
                    $meaning="";
                    $posId = $(this).val();
                    if(arr[$posId]==0)
                    {
                        arr[$posId]=1;

                        $("ul#meaning-"+$posId+".meaning li.capsule").each(function() {

                                if($meaning=="")
                                    $meaning = $(this).text();
                                else
                                    $meaning += "; "+$(this).text();
                        });


                        if($meaning == "")
                        {
                            showInfo("ত্রুটি!", "কোনো অর্থ পাওয়া যায়নি! প্রতিটি অর্থ লিখার পর অবশ্যই একটি করে সেমি-কোলোন (\";\") দিতে হবে।");
                            $flag=1;
                            ///console.log("1111");
                            return false;
                        }
                        else
                        {
                            data[$posId]=$meaning;
                            ///console.log(Object.keys(data).length);
                        }
                    }
                    else
                    {
                        showInfo("ত্রুটি!","একই পদ প্রকরণ একাধিক! এটি মুছুন বা পরিবর্তন করুন।");
                        ///console.log("2222");
                        $flag=1;
                        return false; // its work like "break" in loop
                    }
                });

                if($flag==1)
                {
                    //console.log("3333");
                    return false;
                }

                // traverse object data arbitarily
                // for (var value in data) {
                //     console.log(data[value]);
                // }

                $("ul.synonyms li.capsule").each(function() {

                        if($synonyms=="")
                            $synonyms = $(this).text();
                        else
                            $synonyms += "; "+$(this).text();
                });

                console.log($synonyms);

                $("ul.antonyms li.capsule").each(function() {

                        if($antonyms=="")
                            $antonyms = $(this).text();
                        else
                            $antonyms += "; "+$(this).text();
                });

                console.log($antonyms);

                $options = {
                    word: $word,
                    meaning: data,
                    synonyms: $synonyms,
                    antonyms: $antonyms
                };

                // Send data
                $.post("addentry.php", $options, function(result){

                    if(result!="0")
                    {
                        $obj = jQuery.parseJSON(result);
                        showInfo($obj.title, $obj.message);
                    }
                    else
                    {
                        showInfo("ত্রুটি!", "অন্তর্ভুক্তিটি সফলভাবে যুক্ত করা যায়নি! :(");
                    }

                    //console.log(result);
                    
                });

                
            });


        });

    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">

    <?php

// NOTE: sometimes the dropdown exist the previous value. So the elements id not changed!

function displayNewEntryForm()
{
  echo '<form class="form-box-lg" role="form" action="" method="post">
  <div class="form-group">
     <label for="word">শব্দ / শব্দ গুচ্ছ <span class="text-danger">(আবশ্যক)</span></label>
     <input type="text" pattern="[A-Za-z0-9-\' ]{1,50}" name="word" class="form-control" id="word" placeholder="ইংরেজি শব্দ অথবা শব্দ-গুচ্ছ এখানে লিখুন" required autofocus>
     <div class="item_info"></div>
     <input class="btn btn-primary skip" type="button" id="0" value="এটা বাদ দিন">
     <input class="btn btn-primary gt-go" type="button" id="0" value="Google Translate">
 </div>

<!-- <div class="form-group">
  <label for="pos">পদপ্রকরণ</label>
  <input type="text" pattern="[A-Za-z,-/ ]{1,25}" name="pos" class="form-control" id="pos" placeholder="পদ এখানে লিখুন
">
<span class="help-block alert alert-warning">পদ প্রকরণ সমূহের সংক্ষিপ্ত রূপ লিখুন। যেমনঃ <strong>n</strong> (noun), <strong>pro</strong> (pronoun), <strong>v</strong> (verb), <strong>adv</strong> (adverb), <strong>adj</strong> (adjective),  <strong>pre</strong> (preposition), <strong>conj</strong> (conjunction) ইত্যাদি।</span>
</div>
-->

<span class="help-block alert alert-warning">প্রতিটি অর্থ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>

<!-- meaning start -->

<div class="form-group meaning" id="meaning-0">
    <label for="pos">পদপ্রকরণ <span class="text-danger">(আবশ্যক)</span></label>
    <select class="form-control" id="opt-pos">
        <option value="0" selected="selected">Not defined</option>
        <option value="n">Noun</option>
        <option value="pro">Pronoun</option>
        <option value="v">Verb</option>
        <option value="adv">Adverb</option>
        <option value="adj">Adjective</option>
        <option value="pre">Preposition</option>
        <option value="conj">Conjunction</option>
        <option value="i">Phrase/Idioms</option>
    </select> 
    <label for="meaning">বাংলা অর্থ <span class="text-danger">(আবশ্যক)</span></label>
    <ul class="form-control meaning inline capsule-ul" id="meaning-0">
        <li class="meaning-li">
        <input type="text" name="meaning" class="no-design capsule" id="meaning-0">
        
        </li>
    </ul>
</div>

<input class="btn btn-primary add-pos" type="button" id="0" value="আরেকটি পদপ্রকরণ যুক্ত করুন">

<!-- meaning end -->



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

<div class="form-group">
   <label for="antonyms">বিপরীতার্থক শব্দ</label>
   <ul class="form-control antonyms inline capsule-ul">
        <li class="antonyms-li">
        <input type="text" name="antonyms" pattern="[A-Za-z;,\' -/]{1,1000}" class="no-design capsule" id="antonyms">
        </li>
    </ul>
   <span class="help-block alert alert-warning">
প্রতিটি বিপরীতার্থক শব্দ লিখার পর <strong>অবশ্যই</strong> একটি করে <strong>সেমি-কোলোন (";")</strong> দিতে হবে।</span>
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