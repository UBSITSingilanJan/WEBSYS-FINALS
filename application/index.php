<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    (int)$_SESSION['user_id'] <= 0 ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'resident'
) {
    header("Location: ../index.php");
    exit();
}

require_once "../dbconn.php";
require_once "../classes/applicant.php";
require_once "../classes/program_manager.php";
require_once "../classes/resident.php";
require_once "../classes/resident_profile.php";

$applicantManager = new Applicant($pdo);
$residentManager = new Resident($pdo);
$programManager = new ProgramManager($pdo);
$activePrograms = $programManager->fetch_active_programs();
$userId = (int)$_SESSION['user_id'];
$row = $applicantManager->fetch_profile_data($userId);
if (!$row) {
    http_response_code(400);
    exit("Please complete your resident profile before applying.");
}

$eligibilityDate = ResidentProfile::aid_eligibility_date($row['move_in_date'] ?? null);
$isAidEligible = $eligibilityDate !== null && $eligibilityDate <= new DateTimeImmutable('today');
$uploadError = null;
$profileValues = $row;
$aidType = '';

if (isset($_POST['apply_btn']) && $isAidEligible) {
    $profileResult = ResidentProfile::from_post($_POST);
    $profileValues = array_merge($_POST, $profileResult['data']);
    $aidType = is_string($_POST['aid_type'] ?? null) ? trim($_POST['aid_type']) : '';
    $uploadedFiles = [];
    $uploadError = $profileResult['error'];

    if ($uploadError === null && !$programManager->is_active_program($aidType)) {
        $uploadError = "Select an active assistance program.";
    }
    if ($uploadError === null && $profileResult['data']['education'] === '') {
        $uploadError = "Select the highest educational attainment.";
    } elseif ($uploadError === null && $profileResult['data']['birthplace'] === '') {
        $uploadError = "Enter the place of birth.";
    } elseif ($uploadError === null && $profileResult['data']['occupation'] === '') {
        $uploadError = "Enter the occupation.";
    }

    $documentsUpload = $_FILES['documents'] ?? null;
    if (
        $uploadError === null &&
        (
            !is_array($documentsUpload) ||
            !isset($documentsUpload['error'], $documentsUpload['name'], $documentsUpload['tmp_name'], $documentsUpload['size']) ||
            !is_array($documentsUpload['error']) ||
            !is_array($documentsUpload['name']) ||
            !is_array($documentsUpload['tmp_name']) ||
            !is_array($documentsUpload['size']) ||
            count($documentsUpload['error']) === 0
        )
    ) {
        $uploadError = "Please select at least one supporting document.";
    }

    if ($uploadError === null) {
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($documentsUpload['error'] as $key => $error) {
            if ($error !== UPLOAD_ERR_OK) {
                $uploadError = "A supporting document could not be uploaded.";
                break;
            }

            $tmpName = $documentsUpload['tmp_name'][$key] ?? '';
            $fileSize = $documentsUpload['size'][$key] ?? 0;
            if (
                !is_string($tmpName) ||
                !is_uploaded_file($tmpName) ||
                !is_numeric($fileSize) ||
                (int)$fileSize <= 0 ||
                (int)$fileSize > 5 * 1024 * 1024
            ) {
                $uploadError = "Each supporting document must be a valid file no larger than 5 MB.";
                break;
            }

            $fileType = $finfo->file($tmpName);
            if (!isset($allowedTypes[$fileType])) {
                $uploadError = "Supporting documents must be JPEG, PNG, WebP, or PDF files.";
                break;
            }

            $fileData = file_get_contents($tmpName);
            if ($fileData === false) {
                $uploadError = "A supporting document could not be read.";
                break;
            }

            $originalName = $documentsUpload['name'][$key] ?? '';
            if (!is_string($originalName)) {
                $uploadError = "A supporting document has an invalid filename.";
                break;
            }

            $originalName = basename(str_replace('\\', '/', $originalName));
            $originalName = preg_replace('/[\x00-\x1F\x7F]/', '', $originalName);
            $originalName = substr(trim($originalName), 0, 255);
            if ($originalName === '') {
                $originalName = 'document.' . $allowedTypes[$fileType];
            }

            $uploadedFiles[] = [
                'original_name' => $originalName,
                'mime_type' => $fileType,
                'file_data' => $fileData,
            ];
        }
    }

    if ($uploadError === null && count($uploadedFiles) > 0) {
        $profile = $profileResult['data'];
        $profile['email'] = $row['account_email'];
        $pdo->beginTransaction();
        try {
            $residentManager->update_profile($userId, $profile);
            $applicantManager->insert_data(
                $profile['firstname'], $profile['middlename'], $profile['lastname'], $profile['suffix'],
                $profile['contact'], $row['account_email'], $profile['gender'], $profile['civilstatus'],
                $profile['education'], $profile['soloparent'], $profile['children'], $profile['member'],
                $profile['program'], $profile['dependent'], $profile['relationship'],
                $profile['housenumber'], $profile['street'], $profile['barangay'], $profile['city'],
                $profile['province'], $profile['zipcode'], $profile['birthplace'], $profile['occupation'],
                $profile['school'], $profile['yearlevel'], $profile['course'], $profile['benefits'],
                $profile['enrolled'], $profile['attained'], $profile['senior'], $profile['issues'],
                $aidType, $row['resident_id'], $userId, $uploadedFiles
            );
            $pdo->commit();
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }

        header("Location: ../resident/index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply for Financial Aid</title>
    <link rel="stylesheet" href="application_style.css">
</head>
<body class="resident-form-body">
<main class="resident-form-page">
    <header class="resident-form-header">
        <p class="resident-form-kicker">HONEYMOON HOLYGHOST BARANGAY</p>
        <h1>Resident Profile and Aid Application</h1>
        <p>Review and complete your resident profile, then submit your financial assistance request.</p>
    </header>

    <?php if ($uploadError !== null): ?>
        <p class="application-error" role="alert"><?= htmlspecialchars($uploadError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if (!$isAidEligible): ?>
        <p class="application-error" role="status">
            <?php if ($eligibilityDate === null): ?>
                Your move-in date is not recorded. Contact the barangay office to update your resident record before applying.
            <?php else: ?>
                Your account is under residency assessment. You can apply for aid after six months, on <?= htmlspecialchars($eligibilityDate->format('F j, Y'), ENT_QUOTES, 'UTF-8') ?>.
            <?php endif; ?>
        </p>
    <?php else: ?>
        <p class="application-help" role="status">
            You meet the six-month residency requirement and are eligible to apply for aid.
        </p>
    <?php endif; ?>

    <form class="resident-form-card" method="post" enctype="multipart/form-data">
        <section class="application-form-section" aria-labelledby="account-heading">
            <h4 id="account-heading">Resident Account</h4>
            <p class="application-help">
                Signed in as <?= htmlspecialchars($row['account_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>.
                Your resident details below are saved to your profile when you submit this application.
            </p>
        </section>

        <?php $requireAidProfileFields = true; ?>
        <?php require "resident_profile_fields.php"; ?>

        <section class="application-form-section" aria-labelledby="aid-heading">
            <h4 id="aid-heading">Financial Assistance Application</h4>
            <div class="application-fields-grid">
                <label class="application-full">Assistance program *
                    <select class="form-control" name="aid_type" required>
                        <option value="">Select an active assistance program</option>
                        <?php foreach ($activePrograms as $activeProgram): ?>
                            <option value="<?= htmlspecialchars($activeProgram['program_name'], ENT_QUOTES, 'UTF-8') ?>" <?= $aidType === $activeProgram['program_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($activeProgram['program_name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php if (empty($activePrograms)): ?>
                    <p class="no-program-message application-full" role="status">There are no active assistance programs available right now.</p>
                <?php endif; ?>
                <label class="application-full">Supporting documents *
                    <span class="application-help">Upload JPEG, PNG, WebP, or PDF files, up to 5 MB each. Files are stored with this application.</span>
                    <input class="application-file" type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                </label>
            </div>
        </section>

        <div class="application-actions">
            <a class="btn-secondary" href="../resident/index.php">Cancel</a>
            <button class="btn-primary" type="submit" name="apply_btn" <?= empty($activePrograms) || !$isAidEligible ? 'disabled' : '' ?>>Submit Application</button>
        </div>
    </form>
</main>
</body>
</html>
