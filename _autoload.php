<?php

require_once('_db.php');

if(!isset($_SESSION)) {
	session_start();
}

if(!array_key_exists('uid', $_SESSION)) {
	$_SESSION['uid'] = null;
}

if(!array_key_exists('msgs', $_SESSION)) {
	$_SESSION['msgs'] = [];
}





function dd2($var) {
	echo '<pre>'; var_dump($var); die('</pre>');
}

function redirect(string $location) {
    if (
		empty($_SERVER['HTTP_X_REQUESTED_WITH'])
		|| strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
    ) {
        header('Location: ' . $location);
        exit();
    } else {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden', 'message' => 'AJAX requests cannot be redirected.']);
        exit();
    }
}

function db_query(string $stmt, array $param = []) {
	global $pdo;
    try {
        $pdo_stmt = $pdo->prepare($stmt);
        $pdo_stmt->execute($param);
        return $pdo_stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch ALL rows as associative arrays
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}

function url(string $location = '') {
	if(!empty($location) && substr($location, 0, 1) !== '/') {
		$location = '/'.$location;
	}
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
	$url .= "://".$_SERVER['HTTP_HOST'].(dirname($_SERVER['REQUEST_URI'])).(!empty($location) ? $location : '');
	return $url;
}

function is_auth() {
	$user = db_query('SELECT * FROM users WHERE id=?', [$_SESSION['uid']]);
	return !empty($user);
}

// success, warning, error, info
// type, msg
function add_msg(string $type, string $desc) {
	$_SESSION['msgs'][] = [$type, $desc];
}

function get_msg(bool $clear_msg_session = false) {
	$msgs = $_SESSION['msgs'];
	if($clear_msg_session)
		$_SESSION['msgs'] = [];
	return $msgs;
}

function no_users() {
	$d1 = db_query('SELECT id FROM users');
	return empty($d1);
}




define('PAGE_CURRENT', basename($_SERVER['REQUEST_URI']));
define('PAGE_CURRENT_FILENAME', pathinfo(PAGE_CURRENT, PATHINFO_FILENAME)); // without ext
define('PAGE_AUTH', 'auth.php');
define('PAGE_SIGNOUT', 'signout.php');
define('PAGE_HOME', 'home.php');
define('PAGE_STUDENT', 'student.php');
define('PAGE_USER', 'user.php');


$pages = [
	'guest' => [
		PAGE_AUTH,
	],
	'auth' => [
		PAGE_SIGNOUT,
		PAGE_HOME,
		PAGE_STUDENT,		
		PAGE_USER,
	],
];

$redirect = '';
if((no_users() && PAGE_CURRENT !== PAGE_AUTH)) {
	$_SESSION['uid'] = null;
	add_msg('info', 'No user yet! Please sign up first.');
	redirect(PAGE_AUTH);
}
else if(!is_auth() && PAGE_CURRENT === PAGE_SIGNOUT) {
	add_msg('info', 'You have already signed out');
	redirect(PAGE_HOME);
}
else if(is_auth() && in_array(PAGE_CURRENT, $pages['guest'], true)) {
	add_msg('info', 'You are already signed in');
	redirect(PAGE_HOME);
}
else if(!is_auth() && in_array(PAGE_CURRENT, $pages['auth'], true)) {
	add_msg('error', 'Please sign in first.');
	redirect(PAGE_AUTH);
}








