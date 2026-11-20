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

$residentManager = new Resident($pdo);
$escape = function ($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
};
$search = trim((string)($_GET['q'] ?? ''));
$flash = $_SESSION['resident_flash'] ?? '';
unset($_SESSION['resident_flash']);
$error = '';
$duplicates = [];
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
    foreach ([
        'firstname', 'middlename', 'lastname', 'suffix', 'age', 'gender',
        'contact', 'email', 'housenumber', 'street', 'barangay', 'city',
        'province', 'zipcode'
    ] as $field) {
        $resident[$field] = trim((string)($_POST[$field] ?? ''));
    }

    $addressParts = array_filter([
        $resident['housenumber'], $resident['street'], $resident['barangay'],
        $resident['city'], $resident['province']
    ], function ($part) {
        return $part !== '';
    });
    $resident['address'] = implode(', ', $addressParts);

    if ($resident['firstname'] === '' || $resident['lastname'] === '') {
        $error = 'First name and last name are required.';
    } elseif (
        $resident['age'] !== '' &&
        (!ctype_digit($resident['age']) || (int)$resident['age'] > 127)
    ) {
        $error = 'Enter a valid age between 0 and 127.';
    } elseif (
        $resident['gender'] !== '' &&
        !in_array($resident['gender'], ['Male', 'Female', 'Other'], true)
    ) {
        $error = 'Select a valid gender.';
    } elseif (
        $resident['email'] !== '' &&
        !filter_var($resident['email'], FILTER_VALIDATE_EMAIL)
    ) {
        $error = 'Enter a valid email address.';
    }

    if ($error === '') {
        $duplicates = $residentManager->find_possible_duplicates($resident, $residentId);
        if ($duplicates) {
            $error = 'A resident with the same name and contact number or address already exists. Review the matching record before continuing.';
        } else {
            if ($isEditing) {
                $residentManager->update_resident($residentId, $resident);
                $_SESSION['resident_flash'] = 'Resident information updated.';
            } else {
                $residentId = $residentManager->create_resident($resident);
                $_SESSION['resident_flash'] = 'Resident added to the masterlist.';
            }
            header('Location: resident_management.php?edit=' . urlencode((string)$residentId));
            exit();
        }
    }
}

$residents = $residentManager->search_residents($search);
$formOpen = $isEditing || (($_GET['action'] ?? '') === 'add') || ($_SERVER['REQUEST_METHOD'] === 'POST');
$displayResident = is_array($resident) ? $resident : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resident Masterlist</title>
    <link rel="stylesheet" href="worker_style.css">
    <style>
        .resident-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .resident-form label { display: flex; flex-direction: column; gap: 6px; color: #555; font-size: 13px; }
        .resident-form input, .resident-form select, .search-input { width: 100%; padding: 10px; border: 1px solid #bbb; border-radius: 5px; font: inherit; }
        .resident-form .full-width { grid-column: 1 / -1; }
        .resident-actions { display: flex; gap: 10px; align-items: center; }
        .resident-actions button, .resident-actions a, .resident-link { display: inline-block; padding: 9px 13px; border: 0; border-radius: 5px; background: #2563eb; color: white; text-decoration: none; cursor: pointer; font: inherit; }
        .resident-actions a { background: #666; }
        .resident-search { display: flex; gap: 10px; margin-bottom: 16px; }
        .resident-notice { margin-bottom: 15px; padding: 12px; border-radius: 5px; background: #fff3cd; color: #664d03; }
        @media (max-width: 600px) { .resident-form { grid-template-columns: 1fr; } .resident-form .full-width { grid-column: auto; } }
    </style>
</head>
<body>
<div class="dashboard">
    <?php $activePage = 'residents'; require __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="topbar">
            <h2>Resident Masterlist</h2>
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
                    <table class="applicant-table">
                        <thead><tr><th>Resident ID</th><th>Name</th><th>Age</th><th>Contact</th><th>Address</th><th>Aid Status</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($residents as $item): ?>
                            <tr>
                                <td><?php echo $escape($item['resident_id']); ?></td>
                                <td><?php echo $escape(trim(implode(' ', array_filter([$item['firstname'], $item['middlename'], $item['lastname'], $item['suffix']])))); ?></td>
                                <td><?php echo $escape($item['age'] ?? ''); ?></td>
                                <td><?php echo $escape($item['contact'] ?? ''); ?></td>
                                <td><?php echo $escape($item['address'] ?? ''); ?></td>
                                <td>
                                    <span class="badge <?php echo !empty($item['has_received_aid']) ? 'badge-yes' : 'badge-no'; ?>">
                                        <?php echo !empty($item['has_received_aid']) ? 'Received aid' : 'No aid record'; ?>
                                    </span>
                                </td>
                                <td>
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
                <form method="post" class="resident-form">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="resident_id" value="<?php echo $escape($residentId); ?>">
                    <label>First name *<input name="firstname" maxlength="100" required value="<?php echo $escape($displayResident['firstname'] ?? ''); ?>"></label>
                    <label>Middle name<input name="middlename" maxlength="100" value="<?php echo $escape($displayResident['middlename'] ?? ''); ?>"></label>
                    <label>Last name *<input name="lastname" maxlength="100" required value="<?php echo $escape($displayResident['lastname'] ?? ''); ?>"></label>
                    <label>Suffix<input name="suffix" maxlength="20" value="<?php echo $escape($displayResident['suffix'] ?? ''); ?>"></label>
                    <label>Age<input type="number" name="age" min="0" max="127" value="<?php echo $escape($displayResident['age'] ?? ''); ?>"></label>
                    <label>Gender
                        <select name="gender">
                            <option value="">Select</option>
                            <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                                <option value="<?php echo $escape($gender); ?>" <?php echo ($displayResident['gender'] ?? '') === $gender ? 'selected' : ''; ?>><?php echo $escape($gender); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Contact number<input name="contact" maxlength="11" value="<?php echo $escape($displayResident['contact'] ?? ''); ?>"></label>
                    <label>Email<input type="email" name="email" maxlength="100" value="<?php echo $escape($displayResident['email'] ?? ''); ?>"></label>
                    <label>House number<input name="housenumber" maxlength="50" value="<?php echo $escape($displayResident['housenumber'] ?? ''); ?>"></label>
                    <label>Street<input name="street" maxlength="100" value="<?php echo $escape($displayResident['street'] ?? ''); ?>"></label>
                    <label>Barangay<input name="barangay" maxlength="100" value="<?php echo $escape($displayResident['barangay'] ?? ''); ?>"></label>
                    <label>City / Municipality<input name="city" maxlength="100" value="<?php echo $escape($displayResident['city'] ?? ''); ?>"></label>
                    <label>Province<input name="province" maxlength="100" value="<?php echo $escape($displayResident['province'] ?? ''); ?>"></label>
                    <label>ZIP code<input name="zipcode" maxlength="20" value="<?php echo $escape($displayResident['zipcode'] ?? ''); ?>"></label>
                    <div class="resident-actions full-width">
                        <button type="submit">Save Resident</button>
                        <a href="resident_management.php">Cancel</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
