<?php
define('POV_ROOT', __DIR__);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/form-store.php';
$r = pov_password_reset_validate('716d5b74cd58290aa9d26e92fa1b9706c052f892a9254abc07a73ce0d8e779d2');
echo json_encode($r, JSON_PRETTY_PRINT);
