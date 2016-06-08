<?php
	setcookie( $GLOBALS['c_name'], "", -1, '/', 'localhost', false, true);
    setcookie( $GLOBALS['c_email'], "", -1, '/', 'localhost', false, true);
    setcookie( $GLOBALS['c_id'], "", -1, '/', 'localhost', false, true);
    setcookie( $GLOBALS['c_hash'], "", -1, '/', 'localhost', false, true);
    setcookie( $GLOBALS['c_isloggedin'], "", -1, '/', 'localhost', false, true);

    if (!headers_sent()) {
        header( "refresh:0;url=/socloud" );
    }

    echo '
    <div class="alert alert-info">
        আপনি <strong>নিবন্ধিত</strong> নন!<br>
<br>
আপনাকে স্বয়ংক্রিয় নীড় পাতায় কিছুক্ষণের মধ্যে নিয়ে যাওয়া হবে।<br>
যদি স্বয়ংক্রিয় নীড় পাতা না আসে তাহলে <a href="/socloud">এখানে</a> ক্লিক করুন।
    </div>';

?>