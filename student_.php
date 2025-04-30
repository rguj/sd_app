<?php
require_once('_autoload.php');

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function validate(array $inputs) {
	$regex = [
		'name' => "/^[A-Za-z]+([-' ][A-Za-z]+)*$/u",
		'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/u',
		'course' => '/^[A-Za-z0-9][A-Za-z0-9 _-]{1,98}[A-Za-z0-9]$/u',
	];
	$res = [false, []];
	foreach($inputs as $k=>$v) {
		if(array_key_exists($k, $regex) && !preg_match($regex[$k], $v)) {
			$res[1][$k] = 'Invalid format';
		}
	}
	$res[0] = !empty($res[1]);
	return $res;
}

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    echo json_encode(['data' => $stmt->fetchAll() ?? []]);
}

elseif ($action === 'edit' && isset($_GET['id'])) {	
	if($_SESSION['urole'] !== 'Admin') {
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
	
	if($_SESSION['urole'] !== 'Admin') {
		die(json_encode(['status' => 'error', 'message' => 'Only the administrator can do this operation']));
	}
	
	$validate = SELF::validate($_POST);
	if(!$validate[0]) {
		echo json_encode(['status' => 'error', 'message' => 'Validation failed. Please resolve the fields.', 'errors' => $validate[1]]);
		exit();
	}
	
    if ($id) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?");
        $success = $stmt->execute([$name, $email, $course, $id]);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Student updated.' : 'Update failed.']);
    } else {
        // Insert new
        $stmt = $pdo->prepare("INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
        $success = $stmt->execute([$name, $email, $course]);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Student added.' : 'Insert failed.']);
    }
}

elseif ($action === 'delete' && isset($_POST['id'])) {	
	if($_SESSION['urole'] !== 'Admin') {
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
