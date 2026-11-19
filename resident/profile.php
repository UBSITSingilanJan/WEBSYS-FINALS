<?php
    session_start();

    require_once "../dbconn.php";
    require_once "../classes/resident.php";

    $conn = new Resident($pdo);

    $user_id = $_SESSION['user_id'];

    $row = $conn->show_update_data($user_id);

    $resident_id = $row['resident_id'];

    if(isset($_POST['save_btn'])){

        $firstname = trim($_POST['firstname']);
        $middlename = trim($_POST['middlename']);
        $lastname = trim($_POST['lastname']);
        $suffix = trim($_POST['suffix']);
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $contact = trim($_POST['contact']);
        $email = trim($_POST['email']);
        $housenumber = trim($_POST['housenumber']);
        $street = trim($_POST['street']);
        $barangay = trim($_POST['barangay']);
        $city = trim($_POST['city']);
        $province = trim($_POST['province']);
        $zipcode = trim($_POST['zipcode']);

        $conn->update_data($resident_id, $firstname, $middlename, $lastname, $suffix, $age, $gender, $contact, $email, $housenumber, $street, $barangay, $city, $province, $zipcode);

        header("Location: profile.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Profile</title>

    <link rel="stylesheet" href="resident_style.css">
</head>

<body>

<div class="dashboard">
    <aside class="sidebar">

        <h3>Financial Aid System</h3>

        <a href="index.php">My Dashboard</a>
        <a href="profile.php" class="active">My Profile</a>
        <a href="../application/index.php">Apply Now</a>
        <a href="logout.php">Logout</a>

    </aside>
    <main class="main-content">
        <div class="topbar">

            <h2>My Profile</h2>

            <div class="user-label">
                Resident &#9662;
            </div>

        </div>
        <div class="content-grid">
            <section class="panel">

                <h3>Personal Information</h3>

                <form method="post">

                    <div class="form-group">
                        <label>Firstname</label>
                        <input
                            type="text"
                            name="firstname"
                            class="form-control"
                            placeholder="Firstname"
                            value="<?= htmlspecialchars($row['firstname'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Middlename</label>
                        <input
                            type="text"
                            name="middlename"
                            class="form-control"
                            placeholder="Middlename"
                            value="<?= htmlspecialchars($row['middlename'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Lastname</label>
                        <input
                            type="text"
                            name="lastname"
                            class="form-control"
                            placeholder="Lastname"
                            value="<?= htmlspecialchars($row['lastname'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Suffix</label>
                        <input
                            type="text"
                            name="suffix"
                            class="form-control"
                            placeholder="Suffix"
                            value="<?= htmlspecialchars($row['suffix'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Age</label>
                        <input
                            type="number"
                            name="age"
                            class="form-control"
                            placeholder="Age"
                            value="<?= htmlspecialchars($row['age'] ?? '') ?>"
                            min="1"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>

                        <select name="gender" class="form-control" required>
                            <option value="" disabled selected>Gender</option>
                            <option value="Male" <?= ($row['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($row['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($row['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input
                            type="text"
                            name="contact"
                            class="form-control"
                            placeholder="Contact Number"
                            value="<?= htmlspecialchars($row['contact'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
                            value="<?= htmlspecialchars($row['email'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <tr>
                            <td><input type="text" name="housenumber" class="form-control" placeholder="House Number" value="<?= htmlspecialchars($row['housenumber'] ?? '') ?>" required></td>
                            <td><input type="text" name="street" class="form-control" placeholder="Street" value="<?= htmlspecialchars($row['street'] ?? '') ?>"></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="barangay" class="form-control" placeholder="Barangay" value="<?= htmlspecialchars($row['barangay'] ?? '') ?>" required></td>
                            <td><input type="text" name="city" class="form-control" placeholder="City" value="<?= htmlspecialchars($row['city'] ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="province" class="form-control" placeholder="Province" value="<?= htmlspecialchars($row['province'] ?? '') ?>" required></td>
                            <td><input type="text" name="zipcode" class="form-control" placeholder="Zip Code" value="<?= htmlspecialchars($row['zipcode'] ?? '') ?>" required></td>
                        </tr>
                    </div>

                    <div class="form-actions">

                        <button
                            type="submit"
                            name="save_btn"
                            class="btn-primary">
                            Save Profile
                        </button>

                        <a href="index.php" class="btn-secondary">
                            Cancel
                        </a>

                    </div>

                </form>

            </section>
        </div>
    </main>

</div>

</body>
</html>
