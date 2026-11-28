<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'worker'
) {
    header("Location: ../index.php");
    exit();
}

require_once "../dbconn.php";
require_once "../classes/resident.php";
require_once "../classes/applicant.php";
require_once "../classes/program_manager.php";
require_once "../classes/resident_profile.php";

$residentManager = new Resident($pdo);
$applicantManager = new Applicant($pdo);
$programManager = new ProgramManager($pdo);
$activePrograms = $programManager->fetch_active_programs();
$escape = function ($value) {
    return htmlspecialchars(is_scalar($value ?? null) ? (string)$value : '', ENT_QUOTES, 'UTF-8');
};
$search = trim((string)($_GET['q'] ?? ''));
$flash = $_SESSION['resident_flash'] ?? '';
unset($_SESSION['resident_flash']);
$error = '';
$duplicates = [];
$createApplication = ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['create_application'] ?? '') === '1');
$residentId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT);
$isEditing = $residentId !== false && $residentId !== null && $residentId > 0;
$resident = $isEditing ? $residentManager->find_resident($residentId) : null;

if ($isEditing && !$resident) {
    http_response_code(404);
    exit('Resident record not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $postedId = filter_var($_POST['resident_id'] ?? null, FILTER_VALIDATE_INT);
    $isEditing = $postedId !== false && $postedId !== null && $postedId > 0;
    $residentId = $isEditing ? $postedId : null;
    $resident = [];
    if ($isEditing && !$residentManager->find_resident($residentId)) {
        http_response_code(404);
        exit('Resident record not found.');
    }
    $profileResult = ResidentProfile::from_post($_POST);
    $resident = $profileResult['data'] ? array_merge($_POST, $profileResult['data']) : $_POST;
    $resident['email'] = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $error = $profileResult['error'] ?? '';
    $accountPassword = is_string($_POST['account_password'] ?? null) ? $_POST['account_password'] : '';
    if ($error === '' && !$isEditing && $resident['email'] === '') {
        $error = 'Enter an email address for the resident account.';
    } elseif (
        $error === '' &&
        ($resident['email'] !== '' && (strlen($resident['email']) > 100 || !filter_var($resident['email'], FILTER_VALIDATE_EMAIL)))
    ) {
        $error = 'Enter a valid email address.';
    } elseif ($error === '' && !$isEditing && !preg_match('/^[0-9]$/', $accountPassword)) {
        $error = 'The temporary password must be exactly one digit.';
    }

    $addressParts = array_filter([
        $resident['housenumber'] ?? '', $resident['street'] ?? '',
        $resident['barangay'] ?? '', $resident['city'] ?? '',
        $resident['province'] ?? ''
    ], function ($part) {
        return is_string($part) && $part !== '';
    });
    $resident['address'] = implode(', ', $addressParts);

    if ($error === '') {
        $duplicates = $residentManager->find_possible_duplicates($resident, $residentId);
        if ($duplicates) {
            $error = 'A resident with the same name and contact number or address already exists. Review the matching record before continuing.';
        } else {
            $applicationData = [];
            $uploadedDocuments = [];

            if ($createApplication && $isEditing) {
                $error = 'An application can only be created while adding a new resident.';
            } elseif ($createApplication) {
                $postedApplication = $_POST['application'] ?? [];
                if (!is_array($postedApplication)) {
                    $postedApplication = [];
                    $error = 'Enter valid application details.';
                }

                $aidType = $postedApplication['aid_type'] ?? '';
                $applicationData = [
                    'gender' => $resident['gender'],
                    'civilstatus' => $resident['civilstatus'],
                    'education' => $resident['education'],
                    'soloparent' => $resident['soloparent'],
                    'children' => $resident['children'],
                    'member' => $resident['member'],
                    'program' => $resident['program'],
                    'dependent' => $resident['dependent'],
                    'relationship' => $resident['relationship'],
                    'birthplace' => $resident['birthplace'],
                    'occupation' => $resident['occupation'],
                    'school' => $resident['school'],
                    'yearlevel' => $resident['yearlevel'],
                    'course' => $resident['course'],
                    'benefits' => $resident['benefits'],
                    'enrolled' => $resident['enrolled'],
                    'attained' => $resident['attained'],
                    'senior' => $resident['senior'],
                    'issues' => $resident['issues'],
                    'aid_type' => is_string($aidType) ? trim($aidType) : '',
                ];

                if (
                    $error === '' &&
                    !in_array($applicationData['gender'], ['Male', 'Female', 'Other'], true)
                ) {
                    $error = 'Select a valid gender for the application.';
                } elseif (
                    $error === '' &&
                    !in_array($applicationData['civilstatus'], ['Single', 'Married', 'Widowed', 'Annulled'], true)
                ) {
                    $error = 'Select a civil status for the application.';
                } elseif (
                    $error === '' &&
                    !in_array($applicationData['education'], ['Elementary', 'High School', 'Senior High School', 'College', 'Graduate', 'Vocational'], true)
                ) {
                    $error = 'Select the highest educational attainment for the application.';
                } elseif (
                    $error === '' &&
                    $applicationData['birthplace'] === ''
                ) {
                    $error = 'Enter the applicant\'s place of birth.';
                } elseif (
                    $error === '' &&
                    $applicationData['occupation'] === ''
                ) {
                    $error = 'Enter the applicant’s occupation.';
                } elseif (
                    $error === '' &&
                    !$programManager->is_active_program($applicationData['aid_type'])
                ) {
                    $error = 'Select an active assistance program.';
                }

                $eligibilityDate = ResidentProfile::aid_eligibility_date($resident['move_in_date'] ?? null);
                if ($error === '' && ($eligibilityDate === null || $eligibilityDate > new DateTimeImmutable('today'))) {
                    $error = $eligibilityDate === null
                        ? 'Enter a valid move-in date before creating an aid application.'
                        : 'This resident can apply for financial aid after six months of residency, on ' . $eligibilityDate->format('F j, Y') . '.';
                }

                if ($error === '' && strlen($applicationData['aid_type']) > 150) {
                    $error = 'Select a valid assistance program.';
                }

                if ($error === '') {
                    $documentUpload = $_FILES['application_documents'] ?? null;
                    if (
                        !is_array($documentUpload) ||
                        !isset($documentUpload['error'], $documentUpload['name'], $documentUpload['tmp_name'], $documentUpload['size']) ||
                        !is_array($documentUpload['error']) ||
                        !is_array($documentUpload['name']) ||
                        !is_array($documentUpload['tmp_name']) ||
                        !is_array($documentUpload['size']) ||
                        count($documentUpload['error']) === 0
                    ) {
                        $error = 'Upload at least one supporting document for the application.';
                    } else {
                        $allowedTypes = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                            'application/pdf' => 'pdf',
                        ];
                        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

                        foreach ($documentUpload['error'] as $key => $uploadError) {
                            if (
                                $uploadError !== UPLOAD_ERR_OK ||
                                !isset($documentUpload['tmp_name'][$key], $documentUpload['size'][$key], $documentUpload['name'][$key])
                            ) {
                                $error = 'A supporting document could not be uploaded.';
                                break;
                            }

                            $temporaryName = $documentUpload['tmp_name'][$key];
                            $fileSize = $documentUpload['size'][$key];
                            if (
                                !is_string($temporaryName) ||
                                !is_uploaded_file($temporaryName) ||
                                !is_numeric($fileSize) ||
                                (int)$fileSize <= 0 ||
                                (int)$fileSize > 5 * 1024 * 1024
                            ) {
                                $error = 'Each supporting document must be a valid file no larger than 5 MB.';
                                break;
                            }

                            $mimeType = $fileInfo->file($temporaryName);
                            if (!isset($allowedTypes[$mimeType])) {
                                $error = 'Supporting documents must be JPEG, PNG, WebP, or PDF files.';
                                break;
                            }

                            $fileData = file_get_contents($temporaryName);
                            if ($fileData === false) {
                                $error = 'A supporting document could not be read.';
                                break;
                            }

                            $originalName = $documentUpload['name'][$key];
                            if (!is_string($originalName)) {
                                $error = 'A supporting document has an invalid filename.';
                                break;
                            }
                            $originalName = basename(str_replace('\\', '/', $originalName));
                            $originalName = preg_replace('/[\x00-\x1F\x7F]/', '', $originalName);
                            $originalName = substr(trim($originalName), 0, 255);
                            if ($originalName === '') {
                                $originalName = 'document.' . $allowedTypes[$mimeType];
                            }

                            $uploadedDocuments[] = [
                                'original_name' => $originalName,
                                'mime_type' => $mimeType,
                                'file_data' => $fileData,
                            ];
                        }
                    }
                }
            }

            if ($error === '') {
                try {
                    $pdo->beginTransaction();
                    if ($isEditing) {
                        $residentManager->update_resident($residentId, $resident);
                        $_SESSION['resident_flash'] = 'Resident information updated.';
                    } else {
                        $createAccount = $pdo->prepare(
                            "INSERT INTO user (firstname, lastname, email, password, role, must_change_password) VALUES (:firstname, :lastname, :email, :password, 'resident', 1)"
                        );
                        $createAccount->execute([
                            ':firstname' => $resident['firstname'],
                            ':lastname' => $resident['lastname'],
                            ':email' => $resident['email'],
                            ':password' => password_hash($accountPassword, PASSWORD_DEFAULT),
                        ]);
                        $newUserId = (int)$pdo->lastInsertId();
                        $residentId = $residentManager->create_resident($resident, $newUserId);
                        $_SESSION['resident_flash'] = 'Resident and login account created. The resident must change the temporary password at first login.';

                        if ($createApplication) {
                            $applicantManager->insert_data(
                                $resident['firstname'],
                                $resident['middlename'],
                                $resident['lastname'],
                                $resident['suffix'],
                                $resident['contact'],
                                $resident['email'] !== '' ? $resident['email'] : null,
                                $applicationData['gender'],
                                $applicationData['civilstatus'],
                                $applicationData['education'],
                                $applicationData['soloparent'] !== '' ? $applicationData['soloparent'] : null,
                                $applicationData['children'] !== '' ? $applicationData['children'] : null,
                                $applicationData['member'] !== '' ? $applicationData['member'] : null,
                                $applicationData['program'] !== '' ? $applicationData['program'] : null,
                                $applicationData['dependent'] !== '' ? $applicationData['dependent'] : null,
                                $applicationData['relationship'] !== '' ? $applicationData['relationship'] : null,
                                $resident['housenumber'],
                                $resident['street'],
                                $resident['barangay'],
                                $resident['city'],
                                $resident['province'],
                                $resident['zipcode'],
                                $applicationData['birthplace'],
                                $applicationData['occupation'],
                                $applicationData['school'] !== '' ? $applicationData['school'] : null,
                                $applicationData['yearlevel'] !== '' ? $applicationData['yearlevel'] : null,
                                $applicationData['course'] !== '' ? $applicationData['course'] : null,
                                $applicationData['benefits'],
                                $applicationData['enrolled'] !== '' ? $applicationData['enrolled'] : null,
                                $applicationData['attained'] !== '' ? $applicationData['attained'] : null,
                                $applicationData['senior'],
                                $applicationData['issues'] !== '' ? $applicationData['issues'] : null,
                                $applicationData['aid_type'],
                                $residentId,
                                null,
                                $uploadedDocuments
                            );
                            $_SESSION['resident_flash'] = 'Resident and pending application added successfully.';
                        }
                    }
                    $pdo->commit();
                    header('Location: resident_management.php?edit=' . urlencode((string)$residentId));
                    exit();
                } catch (PDOException $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    error_log('Unable to save worker-created resident/application: ' . $exception->getMessage());
                    $error = 'The resident and application could not be saved. No changes were kept; please review the details and try again.';
                }
            }
        }
    }
}

$residents = $residentManager->search_residents($search);
$formOpen = $isEditing || (($_GET['action'] ?? '') === 'add') || ($_SERVER['REQUEST_METHOD'] === 'POST');
$displayResident = is_array($resident) ? $resident : [];
$profileValues = $displayResident;
$postedApplication = $_POST['application'] ?? [];
if (!is_array($postedApplication)) {
    $postedApplication = [];
}
$postedAidType = is_string($postedApplication['aid_type'] ?? null) ? trim($postedApplication['aid_type']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Residents</title>
    <link rel="stylesheet" href="../theme.css">
    <link rel="stylesheet" href="../application/application_style.css">
    <style>
        .resident-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .resident-form > label { display: flex; flex-direction: column; gap: 6px; color: #555; font-size: 13px; }
        .resident-form > label input, .resident-form > label select, .search-input { width: 100%; padding: 10px; border: 1px solid #bbb; border-radius: 5px; font: inherit; }
        .resident-form .full-width, .resident-form > .application-form-section { grid-column: 1 / -1; }
        .resident-actions { display: flex; gap: 10px; align-items: center; }
        .resident-actions button, .resident-actions a, .resident-link { display: inline-block; padding: 9px 13px; border: 0; border-radius: 5px; background: #2563eb; color: white; text-decoration: none; cursor: pointer; font: inherit; }
        .resident-actions a { background: #666; }
        .resident-search { display: flex; gap: 10px; margin-bottom: 16px; }
        .resident-notice { margin-bottom: 15px; padding: 12px; border-radius: 5px; background: #fff3cd; color: #664d03; }
        .resident-table { width: 100%; min-width: 0; table-layout: fixed; }
        .resident-table th, .resident-table td { padding: 8px; white-space: normal; overflow-wrap: anywhere; font-size: 12px; }
        .resident-table th { font-size: 11px; }
        .resident-table td:last-child a { display: block; width: 100%; margin: 0 0 5px; padding: 6px; text-align: center; white-space: normal; font-size: 11px; }
        @media (max-width: 900px) {
            .resident-table, .resident-table tbody { display: block; width: 100%; }
            .resident-table colgroup { display: none; }
            .resident-table thead { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            .resident-table tbody tr { display: block; margin-bottom: 12px; padding: 4px 10px; border: 1px solid #e6ebf1; border-radius: 8px; }
            .resident-table tbody td { display: grid; grid-template-columns: minmax(110px, 34%) minmax(0, 1fr); gap: 10px; width: 100%; padding: 9px 0; border-bottom: 1px solid #eef1f5; }
            .resident-table tbody td::before { content: attr(data-label); color: #6b7c8f; font-size: 10px; font-weight: 600; text-transform: uppercase; }
            .resident-table tbody td.empty-message { display: block; text-align: center; }
            .resident-table tbody td.empty-message::before { content: none; }
        }
        @media (max-width: 480px) {
            .resident-table tbody td { grid-template-columns: minmax(0, 1fr); gap: 4px; }
        }
        @media (max-width: 600px) { .resident-form { grid-template-columns: 1fr; } .resident-form .full-width { grid-column: auto; } }
    </style>
</head>
<body>
<div class="dashboard">
    <?php $activePage = 'residents'; require __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="topbar">
            <h2>Manage Residents</h2>
            <a class="search-clear" href="resident_management.php?action=add">Add New Resident</a>
        </div>

        <?php if ($flash !== ''): ?><div class="panel resident-notice" role="status"><?php echo $escape($flash); ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="panel resident-notice" role="alert"><?php echo $escape($error); ?></div><?php endif; ?>

        <section class="panel">
            <div class="panel-header"><h3>Find a resident</h3></div>
            <form method="get" class="resident-search">
                <input class="search-input" type="search" name="q" value="<?php echo $escape($search); ?>" placeholder="Search by name, resident ID, address, email, or contact">
                <button class="search-btn" type="submit">Search</button>
                <?php if ($search !== ''): ?><a class="search-clear" href="resident_management.php">Clear</a><?php endif; ?>
            </form>

            <?php if ($search !== '' || !$formOpen): ?>
                <div class="table-wrapper">
                    <table class="applicant-table resident-table">
                        <colgroup>
                            <col style="width: 11%">
                            <col style="width: 15%">
                            <col style="width: 6%">
                            <col style="width: 13%">
                            <col style="width: 22%">
                            <col style="width: 14%">
                            <col style="width: 19%">
                        </colgroup>
                        <thead><tr><th scope="col">Resident ID</th><th scope="col">Name</th><th scope="col">Age</th><th scope="col">Contact</th><th scope="col">Address</th><th scope="col">Aid Status</th><th scope="col">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($residents as $item): ?>
                            <tr>
                                <td data-label="Resident ID"><?php echo $escape($item['resident_id']); ?></td>
                                <td data-label="Name"><?php echo $escape(trim(implode(' ', array_filter([$item['firstname'], $item['middlename'], $item['lastname'], $item['suffix']])))); ?></td>
                                <td data-label="Age"><?php echo $escape($item['age'] ?? ''); ?></td>
                                <td data-label="Contact"><?php echo $escape($item['contact'] ?? ''); ?></td>
                                <td data-label="Address"><?php echo $escape($item['address'] ?? ''); ?></td>
                                <td data-label="Aid Status">
                                    <span class="badge <?php echo !empty($item['has_received_aid']) ? 'badge-yes' : 'badge-no'; ?>">
                                        <?php echo !empty($item['has_received_aid']) ? 'Received aid' : 'No aid record'; ?>
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <a class="resident-link" href="resident_management.php?edit=<?php echo urlencode((string)$item['resident_id']); ?>">View / Edit</a>
                                    <a class="open-record-btn" href="resident_record.php?id=<?php echo urlencode((string)$item['resident_id']); ?>">Aid History</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$residents): ?><tr><td class="empty-message" colspan="7"><?php echo $search !== '' ? 'No matching residents found.' : 'No resident records yet.'; ?></td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($formOpen): ?>
            <section class="panel" style="margin-top: 18px">
                <div class="panel-header"><h3><?php echo $isEditing ? 'Resident Profile' : 'Add Resident'; ?></h3></div>
                <?php if ($duplicates): ?>
                    <p>Possible duplicate records:</p>
                    <ul>
                        <?php foreach ($duplicates as $duplicate): ?>
                            <li>
                                <?php echo $escape(trim(implode(' ', array_filter([$duplicate['firstname'], $duplicate['middlename'], $duplicate['lastname']])))); ?>
                                — Resident #<?php echo $escape($duplicate['resident_id']); ?>
                                (<a href="resident_management.php?edit=<?php echo urlencode((string)$duplicate['resident_id']); ?>">Review profile</a>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="resident-form">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="resident_id" value="<?php echo $escape($residentId); ?>">
                    <?php require "../application/resident_profile_fields.php"; ?>

                    <section class="application-form-section full-width" aria-labelledby="resident-contact-heading">
                        <h4 id="resident-contact-heading">Resident Contact</h4>
                        <div class="application-fields-grid">
                            <label>Email address
                                <input class="form-control" type="email" name="email" maxlength="100" value="<?php echo $escape($displayResident['email'] ?? ''); ?>" <?php echo !$isEditing ? 'required' : ''; ?>>
                            </label>
                            <?php if (!$isEditing): ?>
                                <label>Temporary password
                                    <input class="form-control" type="password" name="account_password" minlength="1" maxlength="1" pattern="[0-9]" inputmode="numeric" title="Enter exactly one digit (0–9)." autocomplete="new-password" required>
                                </label>
                                <p class="application-help">Enter exactly one digit (0–9). The resident will be required to create a new password of at least 8 characters on the first login.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <?php if (!$isEditing): ?>
                        <div class="application-create full-width">
                            <div class="application-heading">
                                <h3>Financial assistance application</h3>
                                <p>Optional. Save the resident only, or create a pending application at the same time.</p>
                            </div>
                            <label class="application-create-toggle">
                                <input
                                    type="checkbox"
                                    id="create-application"
                                    name="create_application"
                                    value="1"
                                    <?php echo $createApplication ? 'checked' : ''; ?>
                                    aria-controls="application-fields"
                                >
                                <span>Create an application for this resident now</span>
                            </label>

                            <div class="application-fields" id="application-fields">
                                <section class="application-form-section">
                                    <h4>Application details</h4>
                                    <p class="application-help">The complete profile above will be used for the pending application. Choose the assistance program and attach its supporting documents.</p>
                                    <div class="application-fields-grid">
                                        <label class="application-full">Assistance program *
                                            <select class="form-control" name="application[aid_type]" data-application-required <?php echo empty($activePrograms) ? 'disabled' : ''; ?>>
                                                <option value="">Select assistance program</option>
                                                <?php foreach ($activePrograms as $activeProgram): ?>
                                                    <option value="<?php echo $escape($activeProgram['program_name']); ?>" <?php echo $postedAidType === $activeProgram['program_name'] ? 'selected' : ''; ?>>
                                                        <?php echo $escape($activeProgram['program_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </label>
                                        <?php if (empty($activePrograms)): ?>
                                            <p class="application-help application-full">There are no active assistance programs. You can still save the resident without an application.</p>
                                        <?php endif; ?>
                                    </div>
                                </section>

                                <section class="application-form-section">
                                    <h4>Supporting documents *</h4>
                                    <p class="application-help">Upload JPEG, PNG, WebP, or PDF files, up to 5 MB each. At least one is required for an application.</p>
                                    <input
                                        class="application-file"
                                        type="file"
                                        name="application_documents[]"
                                        multiple
                                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        data-application-required
                                    >
                                </section>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="resident-actions full-width">
                        <button type="submit">Save Resident</button>
                        <a href="resident_management.php">Cancel</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>
</div>
<script>
    const applicationToggle = document.getElementById('create-application');
    const applicationFields = document.getElementById('application-fields');

    if (applicationToggle && applicationFields) {
        const syncApplicationFields = () => {
            applicationFields.hidden = !applicationToggle.checked;
            applicationToggle.setAttribute('aria-expanded', applicationToggle.checked ? 'true' : 'false');
            document.querySelectorAll('[data-application-required]').forEach((field) => {
                field.required = applicationToggle.checked;
            });
        };
        applicationToggle.addEventListener('change', syncApplicationFields);
        syncApplicationFields();
    }
</script>
</body>
</html>
