<?php
require_once('_autoload.php');

$action = $_POST['action'] ?? '';
$username = trim($_POST['username']);
$password = $_POST['password'];
$response = ['status' => 'error'];
$errors = [];

if ($action === 'signup') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name)) $errors['name'] = 'Name is required.';
	if (!preg_match("/^[A-Za-z]+([-' ][A-Za-z]+)*$/u", $name)) {
        $errors['name'] = 'Name must only contain letters, spaces, or hyphens.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
    if (empty($username)) $errors['username'] = 'Username is required.';
    if (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*]).{6,}/", $password)) {
        $errors['password'] = 'Password must be at least 6 characters, with upper, lower, digit & symbol.';
    }

    if ($errors) {
        $response['errors'] = $errors;
    } else {
		$hasError1 = false;
		$hasError2 = false;
        $check1 = $pdo->prepare("SELECT id FROM users WHERE username = ?");		
        $check2 = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check1->execute([$username]);
        $check2->execute([$email]);
        if ($check1->rowCount()) {
            $errors['username'] = 'Username already exists.';
			$hasError1 = true;
        }
		if ($check2->rowCount()) {
            $errors['email'] = 'Email already exists.';
			$hasError2 = true;
        }
		
		if($hasError1 || $hasError2) {
			$response['errors'] = $errors;
			goto point1;
		} else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = 'User';
            $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $username, $hashed, $role])) {
                $response = ['status' => 'success', 'message' => 'Signup successful!'];
            }
        }
    }
}
elseif ($action === 'signin') {
    if (empty($username) || empty($password)) {
        $response['message'] = 'Username and password required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
		if(empty($user)) {
			$errors['username'] = 'Username not found.';
			$response['errors'] = $errors;
			goto point1;
		}
        if ($user && password_verify($password, $user['password'])) {
			$_SESSION['uid'] = $user['id'];
			$_SESSION['uname'] = $user['username'];
			$_SESSION['uemail'] = $user['email'];
			$_SESSION['urole'] = $user['role'];
			add_msg('success', 'You have successfully logged in!');
            $response = [
                'status' => 'success',
                'message' => 'Login successful!',
                'user' => ['name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]
            ];
        } else {
            $response['message'] = 'Invalid credentials.';
        }
    }
}
point1:
echo json_encode($response);
?>
