
<?php
    require_once "../dbconn.php";
    require_once "../classes/resident.php";

    $conn = new Resident($pdo);

    if(isset($_POST['save_btn'])){

        $firstname = trim($_POST['firstname']);
        $middlename = trim($_POST['middlename']);
        $lastname = trim($_POST['lastname']);
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $contact = trim($_POST['contact']);
        $address = trim($_POST['address']);

        $conn -> insert_data(
            $firstname,
            $middlename,
            $lastname,
            $age,
            $gender,
            $contact,
            $address
        );

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
                            placeholder="Enter firstname"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Middlename</label>
                        <input
                            type="text"
                            name="middlename"
                            class="form-control"
                            placeholder="Enter middlename"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Lastname</label>
                        <input
                            type="text"
                            name="lastname"
                            class="form-control"
                            placeholder="Enter lastname"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Age</label>
                        <input
                            type="number"
                            name="age"
                            class="form-control"
                            placeholder="Enter age"
                            min="1"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>

                        <select name="gender" class="form-control" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input
                            type="text"
                            name="contact"
                            class="form-control"
                            placeholder="Enter contact number"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input
                            type="text"
                            name="address"
                            class="form-control"
                            placeholder="Enter complete address"
                            required>
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
            <section class="panel">

                <h3>Profile Information</h3>

                <div class="status-number">
                    &#128100;
                </div>

                <p class="status-description">
                    Please complete your personal information
                    accurately. This information will be used
                    for your financial aid application.
                </p>

                <br>

                <p class="status-description">
                    Make sure your contact number and address
                    are correct before saving your profile.
                </p>

            </section>
        </div>
    </main>

</div>

</body>
</html>
