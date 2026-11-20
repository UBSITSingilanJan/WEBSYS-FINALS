<?php
$search = $_GET['q'] ?? '';
$params = [];
if (is_string($search) && trim($search) !== '') {
    $params['q'] = trim($search);
}

$query = $params ? '?' . http_build_query($params) : '';
header('Location: resident_management.php' . $query);
exit();
