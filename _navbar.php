<?php
require_once('_autoload.php');

function isActive(string $urlLocation) {
	return $urlLocation === basename($_SERVER['SCRIPT_FILENAME']) ? ' active text-primary' : '';
}

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light" style="border-bottom: 1px solid #dcdcdc;">
  <a class="navbar-brand text-secondary" href="./">CRUD App</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
	<ul class="navbar-nav">
	  <li class="nav-item">
		<a class="nav-link <?= isActive('home.php') ?>" href="./home.php">Home</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link <?= isActive('student.php') ?>" href="./student.php">Students</a>
	  </li>
	  
	  <li class="nav-item dropdown float-right">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= $_SESSION['uname'] ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="javascript:void;" disabled><i class="bi bi-envelope"></i><?= $_SESSION['uemail'] ?></a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="./logout.php">Logout</a>
        </div>
      </li>
	  
	  <li class="nav-item float-right">
		<a class="nav-link" href="./logout.php">Logout</a>
	  </li>
	  
	</ul>
	
  </div>
  
  
</nav>

