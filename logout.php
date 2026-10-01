<?php
require_once __DIR__ . '/includes/config.php';
unset($_SESSION['auth_user'], $_SESSION['auth_token'], $_SESSION['admin_user'], $_SESSION['admin_token']);
session_destroy();
header('Location: ' . pov_url('index.php'));
exit;
