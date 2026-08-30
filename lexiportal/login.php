<?php
require __DIR__ . '/config/app.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$stmt = db()->prepare('SELECT * FROM users WHERE lower(email) = lower(?)');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    flash('Incorrect email or password.');
    redirect_to('index.php?login=1');
}

$_SESSION['user_id'] = $user['id'];
flash('Welcome back, ' . strtok($user['name'], ' ') . '.');
redirect_to($user['role'] === 'practitioner' ? 'practitioner-dashboard.php' : 'client-portal.php');
