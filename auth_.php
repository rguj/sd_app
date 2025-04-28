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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
    if (empty($username)) $errors['username'] = 'Username is required.';
    if (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*]).{6,}/", $password)) {
        $errors['password'] = 'Password must be at least 6 characters, with upper, lower, digit & symbol.';
    }

    if ($errors) {
        $response['errors'] = $errors;
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->rowCount()) {
            $response['message'] = 'Username or email already exists.';
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
echo json_encode($response);
?>
