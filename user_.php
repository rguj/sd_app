<?php
require_once('_autoload.php');
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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

    if ($id) {
        // Update existing
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
        // Insert new
        if (empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password is required for new users.']);
            exit();
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
