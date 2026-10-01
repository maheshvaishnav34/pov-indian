<?php
require_once __DIR__ . '/../../../includes/data.php';
header('Content-Type: application/json');
echo json_encode($POV_RENTAL_LISTINGS, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
