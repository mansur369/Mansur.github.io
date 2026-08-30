<?php
require __DIR__ . '/config/app.php';
$user = require_user('client');

$caseId = (int) ($_POST['case_id'] ?? 0);
$file = $_FILES['document'] ?? null;

if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    flash('Please choose a valid document to upload.');
    redirect_to('client-portal.php');
}

$allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $allowed, true) || $file['size'] > 25 * 1024 * 1024) {
    flash('Documents must be PDF, DOC, DOCX, JPG, or PNG up to 25MB.');
    redirect_to('client-portal.php');
}

if (!is_dir(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0775, true);
}
$storedName = 'doc_' . $user['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $storedName);

$stmt = db()->prepare('INSERT INTO documents (user_id, case_id, original_name, stored_name, size_bytes) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$user['id'], $caseId ?: null, $file['name'], $storedName, (int) $file['size']]);
flash('Document uploaded successfully.');
redirect_to('client-portal.php');
