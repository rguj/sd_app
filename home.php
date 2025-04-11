<?php
require_once('_autoload.php');


// check current logged in
if(!is_null($_SESSION['uid'])) {
	$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->execute([$_SESSION['uid']]);
	$user = $stmt->fetch();
	if(empty($user)) {
		$_SESSION['uid'] = null;
	}
}

// redirect invalid user
if(is_null($_SESSION['uid'])) {
	$_SESSION['msgs'][] = ['error', 'Please login first'];
	redirect('login.php');
}



$msgs = $_SESSION['msgs'];
unset($_SESSION['msgs']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/toastr.min.css">
	<script src="./assets/jquery.min.js"></script>
	<script src="./assets/toastr.min.js"></script>
	
    <style>
        .error-feedback { color: red; font-size: 0.9em; }
		.lbl-required:after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">
	<nav class="navbar navbar-expand-lg navbar-light bg-light" style="border-bottom: 1px solid #dcdcdc;">
	  <a class="navbar-brand text-primary" href="./">CRUD App</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarNav">
		<ul class="navbar-nav">
		  <li class="nav-item active">
			<a class="nav-link active" href="./home.php">Home</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="./">Features</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="./logout.php">Logout</a>
		  </li>
		</ul>
	  </div>
	</nav>

<div class="container mt-5">


	<div class="row">
		<div class="col-4 offset-4">
    <div class="card">
        <div class="card-body">
            Hi, Welcome <b><?= $_SESSION['uname'] ?? '' ?></b>!
        </div>
    </div>
	
	</div>
	</div>
	
	
</div>


<script>
$(function(){
	// display session messages
	const msgs = <?= json_encode($msgs) ?>;
	console.log(msgs)
	if (Array.isArray(msgs)) {
		msgs.forEach(([type, message]) => {
			// success, info, warning, error
			toastr[type](message);
		});
	}
	
});
</script>

<script>
$(function(){
    
});

</script>
</body>
</html>
