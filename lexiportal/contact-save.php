<?php
require __DIR__ . '/config/app.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
    flash('Please complete every contact field.');
    redirect_to('contact.php');
}

$stmt = db()->prepare('INSERT INTO contact_requests (name, email, subject, message) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $subject, $message]);
flash('Thanks. Your request has been saved and the team can follow up.');
redirect_to('contact.php');
