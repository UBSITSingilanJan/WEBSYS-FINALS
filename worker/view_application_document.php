<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'worker'
) {
    http_response_code(403);
    exit('Access denied.');
}

require_once "../dbconn.php";
require_once "../classes/application_manager.php";

$applicantId = filter_input(INPUT_GET, 'applicant_id', FILTER_VALIDATE_INT);
$documentId = filter_input(INPUT_GET, 'document_id', FILTER_VALIDATE_INT);
$legacyIndex = filter_input(INPUT_GET, 'legacy_index', FILTER_VALIDATE_INT);

if (
    !$applicantId ||
    $applicantId <= 0 ||
    (($documentId === null) === ($legacyIndex === null)) ||
    ($documentId !== null && ($documentId === false || $documentId <= 0)) ||
    ($legacyIndex !== null && ($legacyIndex === false || $legacyIndex < 0))
) {
    http_response_code(404);
    exit('Document not found.');
}

$manager = new ApplicationManager($pdo);
$document = $documentId !== null
    ? $manager->fetch_application_document($applicantId, $documentId)
    : $manager->fetch_legacy_application_document($applicantId, $legacyIndex);

if ($document === false) {
    http_response_code(404);
    exit('Document not found.');
}

$safeFilename = preg_replace('/[^A-Za-z0-9._-]/', '_', $document['filename']);
if ($safeFilename === '') {
    $safeFilename = 'document';
}

header('Content-Type: ' . $document['mime_type']);
header(
    'Content-Disposition: inline; filename="' . $safeFilename .
    '"; filename*=UTF-8\'\'' . rawurlencode($document['filename'])
);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
header('Content-Length: ' . strlen($document['file_data']));

echo $document['file_data'];
