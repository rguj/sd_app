<?php
// File: db.php
$host = 'localhost';
$db = 'sd_app';
$user = 'admin';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];


try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

    // Now connect to the newly created (or existing) database
    $pdo->exec("USE `$db`");

    // 2. Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `students` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `course` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('Admin','User') DEFAULT 'User',
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
    ");

    // 3. Insert default data into users table if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM `users`");
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("
			INSERT INTO `users` (`name`, `email`, `username`, `password`, `role`, `created_at`) VALUES
			(:name, :email, :username, :password, :role, NOW())
		");

		$users_ = [
			['administrator', 'admin@example.com', 'admin', '$2a$10$Pve8jCJGRBdasFA4Zpvwl.5q0v1ZB3k0Z9FAxmMYxs83faXdyStyu', 'Admin'],
			['hello', 'hello@gmail.com', 'hello', '$2a$10$Pve8jCJGRBdasFA4Zpvwl.5q0v1ZB3k0Z9FAxmMYxs83faXdyStyu', 'User'],
			['John', 'john@gmail.com', 'john', '$2a$10$Pve8jCJGRBdasFA4Zpvwl.5q0v1ZB3k0Z9FAxmMYxs83faXdyStyu', 'User'],
			['jane', 'jane@gmail.com', 'jane', '$2a$10$Pve8jCJGRBdasFA4Zpvwl.5q0v1ZB3k0Z9FAxmMYxs83faXdyStyu', 'User']
		];

		foreach ($users_ as $user_) {
			$stmt->execute([
				'name' => $user_[0],
				'email' => $user_[1],
				'username' => $user_[2],
				'password' => $user_[3],
				'role' => $user_[4]
			]);
		}
    }

    //echo "Database setup complete.";
	$pdo = null;
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
