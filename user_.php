<?php
require_once('_autoload.php');
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function validate_inputs($name, $email, $username, $password = '', $checkPassword = false) {
    $errors = [];

    if (!preg_match("/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/", $name)) {
        $errors['name'] = "Name must only contain letters, spaces, or hyphens.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email must be a valid email address.";
    }

    if (!preg_match("/^[a-zA-Z0-9_.]{3,}$/", $username)) {
        $errors['username'] = "Username must be alphanumeric (3+ chars, with optional _ or .).";
    }

    if ($checkPassword && !preg_match("/^[A-Za-z0-9!@#\$%\^&\*\(\)_\+\-=\[\]\{\};:'\"\\|,.<>\/?]{6,}$/", $password)) {
        $errors['password'] = "Password must be 6+ characters (letters, numbers, symbols).";
    }

    return $errors;
}

if ($action === 'read') {
    $stmt = $pdo->query("SELECT id, name, email, username, role, created_at FROM users ORDER BY id DESC");
    echo json_encode(['data' => $stmt->fetchAll()]);
}

elseif ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT id, name, email, username, role FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetch());
}

elseif ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'User';

    if ($_SESSION['urole'] !== 'Admin') {
        die(json_encode(['status' => 'error', 'message' => 'Only the administrator can perform this operation.']));
    }

    $errors = validate_inputs($name, $email, $username, $password, empty($id));
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => 'Validation failed. Please check the fields.', 'errors' => $errors]);
        exit;
    }

    if ($id) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, username=?, password=?, role=? WHERE id=?");
            $success = $stmt->execute([$name, $email, $username, $hashed, $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, username=?, role=? WHERE id=?");
            $success = $stmt->execute([$name, $email, $username, $role, $id]);
        }
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'User updated successfully.' : 'Update failed.']);
    } else {
        if (empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password is required for new users.', 'errors' => ['password' => 'Password is required.']]);
            exit;
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$name, $email, $username, $hashed, $role]);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'User added successfully.' : 'Insert failed.']);
    }
}

elseif ($action === 'delete' && isset($_POST['id'])) {
    if ($_SESSION['urole'] !== 'Admin') {
        die(json_encode(['status' => 'error', 'message' => 'Only the administrator can perform this operation.']));
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $success = $stmt->execute([$_POST['id']]);
    echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'User deleted successfully.' : 'Delete failed.']);
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
