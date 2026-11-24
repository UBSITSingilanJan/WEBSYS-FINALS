<?php
$search = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$query = $search !== '' ? '?' . http_build_query(['q' => $search]) : '';

header('Location: resident_management.php' . $query);
exit();