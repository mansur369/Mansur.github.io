<?php
require __DIR__ . '/config/app.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'client';

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || !in_array($role, ['client', 'practitioner'], true)) {
    flash('Please fill all fields correctly. Password must be at least 6 characters.');
    redirect_to('index.php');
}

try {
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    $_SESSION['user_id'] = (int) db()->lastInsertId();
    flash('Account created. Welcome to LexPortal.');
    redirect_to($role === 'practitioner' ? 'practitioner-dashboard.php' : 'client-portal.php');
} catch (PDOException $error) {
    flash('An account with this email already exists.');
    redirect_to('index.php');
}
