<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . pov_url('index.php?auth=login'));
exit;
