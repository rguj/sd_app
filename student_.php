<?php
require_once('_autoload.php');
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function validate(array $inputs) {
    $regex = [
        'name' => ["/^[A-Za-z]+([-' ][A-Za-z]+)*$/u", "Name must only contain letters, spaces, or hyphens."],
        'email' => ['/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/u', "Email must be a valid format (e.g., user@example.com)."],
        'course' => ['/^[A-Za-z0-9][A-Za-z0-9 _-]{1,98}[A-Za-z0-9]$/u', "Course must be alphanumeric and 3+ characters."]
    ];
    $errors = [];

    foreach ($regex as $field => [$pattern, $message]) {
        if (isset($inputs[$field]) && !preg_match($pattern, trim($inputs[$field]))) {
            $errors[$field] = $message;
        }
    }

    return $errors;
}

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    echo json_encode(['data' => $stmt->fetchAll()]);
}

elseif ($action === 'edit' && isset($_GET['id'])) {
    if ($_SESSION['urole'] !== 'Admin') {
        die(json_encode(['status' => 'error', 'message' => 'Only the administrator can do this operation']));
    }
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetch());
}

elseif ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    if ($_SESSION['urole'] !== 'Admin') {
        die(json_encode(['status' => 'error', 'message' => 'Only the administrator can do this operation']));
    }

    $errors = validate($_POST);
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => 'Validation failed. Please fix the highlighted fields.', 'errors' => $errors]);
        exit;
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?");
        $success = $stmt->execute([$name, $email, $course, $id]);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Student updated.' : 'Update failed.']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
        $success = $stmt->execute([$name, $email, $course]);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Student added.' : 'Insert failed.']);
    }
}

elseif ($action === 'delete' && isset($_POST['id'])) {
    if ($_SESSION['urole'] !== 'Admin') {
        die(json_encode(['status' => 'error', 'message' => 'Only the administrator can do this operation']));
    }
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $success = $stmt->execute([$_POST['id']]);
    echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Student deleted.' : 'Delete failed.']);
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
