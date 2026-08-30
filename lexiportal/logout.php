<?php
require __DIR__ . '/config/app.php';
session_destroy();
session_start();
flash('You have signed out.');
redirect_to('index.php');
