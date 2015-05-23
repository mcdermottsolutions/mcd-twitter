<?php require "twitter.php"; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="favicon.ico">
		<title>Twitter</title>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="style.css">
	</head>

	<body>

		<nav class="navbar navbar-inverse navbar-fixed-top"> 
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" target="_blank" href="http://twitter.com/">twitter feed prototype</a>
				</div>
			</div>
		</nav>

		<div class="container">
			<div class="list-group">
					<?php foreach ($tweetsArray as $tweet) { echo '<div class="row list-group-item tweet">' . $tweet . '</div>'; } ?>
			</div>
		</div>

	</body>
</html>