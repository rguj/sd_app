<?php
if(!isset($_SESSION)) {
	session_start();
}


function dd2($var) {
	echo '<pre>'; var_dump($var); die('</pre>');
}

function redirect(string $location){
	header('Location:'.$location);
	exit();
}


if(!array_key_exists('uid', $_SESSION)) {
	$_SESSION['uid'] = null;
}

if(!array_key_exists('msgs', $_SESSION)) {
	$_SESSION['msgs'] = [];
}


require 'db.php';

