<?php
require_once('_autoload.php');

if(is_auth()) {
	session_destroy();
	session_start();
	add_msg('success', 'Successfully signed out!');
}

redirect(PAGE_AUTH);