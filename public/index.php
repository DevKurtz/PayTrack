<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../controllers/AuthController.php';

Auth::start();

// Handle logout action
if (($_GET['action'] ?? '') === 'logout') {
    Auth::logout();
}

Auth::redirectIfLoggedIn();

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    AuthController::login();
}

// Pull flash data for the view
$error    = Auth::getFlash('error');
$openRole = Auth::getFlash('open_role'); // re-open modal on error

require_once __DIR__ . '/../views/auth/login.php';
