<?php
require __DIR__ . '/config/app.php';
unset($_SESSION['admin_id']);
flash('Admin signed out.');
redirect_to('admin-login.php');
