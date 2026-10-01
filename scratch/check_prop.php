<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/data.php';

$all = node_rentals_search() ?: [];
foreach ($all as $x) {
    if (stripos($x['title'], 'floor') !== false || stripos($x['city'], 'abu') !== false) {
        print_r($x);
    }
}
