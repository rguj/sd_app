<?php
require_once('_autoload.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
	
	<?php require_once('_head_asset.php'); ?>
	
    <style>
        .error-feedback { color: red; font-size: 0.9em; }
		.lbl-required:after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">

<?php require_once('_navbar.php'); ?>

<div class="container mt-5">


	<div class="row">
		<div class="col-4 offset-4">
			<div class="card">
				<div class="card-body">
					Hi, Welcome!
				</div>
			</div>
		</div>
	</div>
	
	<div class="row mt-3">
		<div class="col-4 offset-4">
			<div class="card">
				<div class="card-header">
					User Information
				</div>
				<div class="card-body">
					Username: <b><?= $_SESSION['uname'] ?? '' ?></b>
					<br>Email: <b><?= $_SESSION['uemail'] ?? '' ?></b>
					<br>Role: <b><?= $_SESSION['urole'] ?? '' ?></b>
				</div>
			</div>
		</div>
	</div>
	
</div>

<script>
$(function(){
    
});
</script>


<?php require_once('_footer.php'); ?>

</body>
</html>
