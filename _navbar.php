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
		<a class="nav-link <?= isActive(PAGE_HOME) ?>" href="<?= PAGE_HOME ?>">Home</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link <?= isActive(PAGE_STUDENT) ?>" href="<?= PAGE_STUDENT ?>">Students</a>
	  </li>
	  
	  <li class="nav-item dropdown float-right">
        <a class="nav-link dropdown-toggle" href="javascript:;" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= $_SESSION['uname'] ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item disabled" href="javascript:;" disabled><i class="bi bi-envelope"></i><?= $_SESSION['uemail'] ?></a>
		  <a class="dropdown-item disabled" href="javascript:;" disabled><i class="bi bi-envelope"></i><span class="badge badge-success text-white"><?= $_SESSION['urole'] ?></span></a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= PAGE_SIGNOUT ?>">Sign out</a>
        </div>
      </li>
	  
	</ul>
	
  </div>
  
  
</nav>

