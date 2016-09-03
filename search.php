<!DOCTYPE html>
<html lang="bn">
<head>

    <?php include 'must-head.php'; ?>

    <script>
        $(document).ready(function(){

            $("input#query").bind('keyup', function(e){
                var code = e.keyCode || e.which;

                if(code >= 65 && code <= 90 || code == 8)
                {
                    // Send data
                    $query = $("input#query").val();
                    if($query!="")
                    {
                        $options = {
                            item: $query
                        };

                        $.post("_util/get_suggestions.php", $options, function(result){

                            $("datalist#suggestions").empty();

                            if(result!="0")
                            {
                                $obj = jQuery.parseJSON(result);

                                $obj.forEach(function(item){
                                    $("datalist#suggestions").append('<option value="'+item['pron']+'">');

                                });
                            }
                            else
                            {

                                console.log("Error!");
                
                                $("datalist#suggestions").empty();
                
                            }
                            //console.log(result);
                        });
                    }
                }
                

            });

        });
    </script>

</head>

<body>

    <?php include 'must-header.php'; ?>

    <div class="container">


<form class="form-box-xlg" role="form" action="" method="post">
  <div class="form-group">
     <!-- <label for="query"></label> -->
     <input type="search" list="suggestions" autocomplete="off" pattern="[A-Za-z0-9-' ]{1,50}" name="query" class="form-control" id="query" placeholder="অনুসন্ধান করুন এখানে" required autofocus>
     <datalist id="suggestions"></datalist>
 </div>

<input type="submit" value="অনুসন্ধান" class="submit btn btn-primary">
<input type="reset" value="রিসেট" class="btn btn-primary">
</form>


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