<!-- <div class="jumbotron"> -->
<header class="bg-1 m-padding">
	<div class="container">
		<h1 class="title">স্বাধীন অভিধান ক্লাউড <span class="label label-danger">আলফা</span>
		<br><small class="bg-1">স্বাধীন অভিধান এর জন্য অনলাইন অভিধান ব্যবস্থাপনা প্রক্রিয়া</small></h1>
		
	</div>
</header>

<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#sowNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span>                      
			</button>
		</div>
		<div class="collapse navbar-collapse" id="sowNavbar">
			<ul class="nav navbar-nav">
				<li><a href="/socloud">নীড়</a></li>

<?php

include 'func.php';

if(isLoggedIn()!=true) {
	echo '<li><a href="login.php" >প্রবেশ</a></li>
<li><a href="register.php" >নিবন্ধন</a></li>';
}
else
{
	echo '<li><a href="addentry.php" >যুক্ত</a></li>
	<li><a href="viewentries.php" >পর্যালোচনা</a></li>';
	echo '</ul>';

	$userDisplayName =  $_COOKIE[$GLOBALS["c_name"]];

	echo '<ul class="nav navbar-nav navbar-right">';
	if(isUserCan("control_user"))
		echo '<li><a href="users.php"><strong>ব্যবহারকারী নিয়ন্ত্রণ</strong></a></li>';
	echo '<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> প্রস্থান <strong>',$userDisplayName,'</strong></a></li>';
}
?>
			</ul>

		</div>
	</div>

</nav>

