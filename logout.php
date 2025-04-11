<?php
require_once('_autoload.php');

if(!is_null($_SESSION['uid'])) {
	$msgs = $_SESSION['msgs'];
	session_destroy();
	$_SESSION['msgs'] = $msgs;
	$_SESSION['msgs'][] = ['success', 'Successfully logged out!'];
	
} else {
	$_SESSION['msgs'][] = ['info', 'You have already logged out'];
}

redirect('login.php');