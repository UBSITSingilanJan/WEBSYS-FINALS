<<<<<<< HEAD

<?php
    session_start();
    require_once "../dbconn.php";
    require_once "../classes/applicant.php";
    if(!isset($_SESSION['user_id'])){
        header("Location: ../index.php");
        exit();
    }

    $conn = new Applicant($pdo);

    if(isset($_POST['apply_btn'])){

        $firstname = trim($_POST['firstname']);
        $middlename = trim($_POST['middlename']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $aid_type = $_POST['aid_type'];
        $user_id = $_SESSION['user_id'];
        $conn -> insert_data(
            $firstname,
            $middlename,
            $lastname,
            $email,
            $aid_type,
            $user_id
=======
<?php
    session_start();

    require_once "../dbconn.php";
    require_once "../classes/applicant.php";

    $conn = new Applicant($pdo);

    $user_id = $_SESSION['user_id'];

    $row = $conn->fetch_profile_data($user_id);

    
    if(isset($_POST['apply_btn'])){

        $firstname = $row['firstname'];
        $middlename = $row['middlename'];
        $lastname = $row['lastname'];
        $suffix = $row['suffix'];
        $contact = $row['contact'];
        $email = $row['email'];
        $gender = $_POST['gender'];
        $civilstatus = $_POST['civilstatus'];
        $education = $_POST['education'];
        $soloparent = $_POST['soloparent'];
        $children = $_POST['children'];
        $member = $_POST['member'];
        $program = $_POST['program'];
        $dependent = $_POST['dependent'];
        $relationship = $_POST['relationship'];
        $housenumber = $row['housenumber'];
        $street = $row['street'];
        $barangay = $row['barangay'];
        $city = $row['city'];
        $province = $row['province'];
        $zipcode = $row['zipcode'];
        $birthplace = $_POST['birthplace'];
        $occupation = $_POST['occupation'];
        $school = $_POST['school'];
        $yearlevel = $_POST['yearlevel'];
        $course = $_POST['course'];
        $benefits = $_POST['benefits'];
        $enrolled = $_POST['enrolled'];
        $attained = $_POST['attained'];
        $senior = $_POST['senior'];
        $issues = $_POST['issues'];
        $aid_type = $_POST['aid_type'];

        $conn->insert_data(
            $firstname, $middlename, $lastname, $suffix,
            $contact, $email, $gender, $civilstatus, $education,
            $soloparent, $children, $member, $program, $dependent,
            $relationship, $housenumber, $street, $barangay,
            $city, $province, $zipcode, $birthplace, $occupation,
            $school, $yearlevel, $course, $benefits, $enrolled,
            $attained, $senior, $issues, $aid_type, $user_id
>>>>>>> 52fad0d (fixed profile and modified application form)
        );

        header("Location: ../resident/index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Apply for Financial Aid</title>
</head>

<body>

    <h1>Financial Aid Application</h1>

    <form method="post">

        <table>

            <tr>
                <td>
<<<<<<< HEAD
                    <input type="text" name="firstname"
                    placeholder="Firstname" required>
=======
                    <input type="text" name="firstname" value="<?= htmlspecialchars($row['firstname'] ?? '') ?>" readonly>
>>>>>>> 52fad0d (fixed profile and modified application form)
                </td>
            </tr>

            <tr>
                <td>
<<<<<<< HEAD
                    <input type="text" name="middlename"
                    placeholder="Middlename" required>
=======
                    <input type="text" name="middlename" value="<?= htmlspecialchars($row['middlename'] ?? '') ?>" readonly>
>>>>>>> 52fad0d (fixed profile and modified application form)
                </td>
            </tr>

            <tr>
                <td>
<<<<<<< HEAD
                    <input type="text" name="lastname"
                    placeholder="Lastname" required>
=======
                    <input type="text" name="lastname" value="<?= htmlspecialchars($row['lastname'] ?? '') ?>" readonly>
>>>>>>> 52fad0d (fixed profile and modified application form)
                </td>
            </tr>

            <tr>
                <td>
<<<<<<< HEAD
                    <input type="email" name="email"
                    placeholder="Email" required>
=======
                    <input type="text" name="suffix" value="<?= htmlspecialchars($row['suffix'] ?? '') ?>" readonly>
>>>>>>> 52fad0d (fixed profile and modified application form)
                </td>
            </tr>

            <tr>
                <td>
<<<<<<< HEAD
=======
                    <input type="tel" name="contact" value="<?= htmlspecialchars($row['contact'] ?? '') ?>" readonly>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="email" name="email" value="<?= htmlspecialchars($row['email'] ?? '') ?>" readonly>
                </td>
            </tr>

            <tr>
                <td>
                    <select name="gender" class="form-control" required>
                        <option value="" disabled selected>Gender</option>
                        <option value="Male" <?= ($row['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($row['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= ($row['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Civil Status</h3>
                    <input type="radio" name="civilstatus" value="Single" required>Single
                    <input type="radio" name="civilstatus" value="Married" required>Married
                    <input type="radio" name="civilstatus" value="Widowed" required>Widowed
                    <input type="radio" name="civilstatus" value="Annulled" required>Annulled
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Highest Educational Attainment</h3>
                    <input type="radio" name="education" value="Elementary" required>Elementary
                    <input type="radio" name="education" value="High School" required>High School
                    <input type="radio" name="education" value="Senior High School" required>Senior High School
                    <input type="radio" name="education" value="College" required>College
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Are you a Solo Parent?</h3>
                    <input type="radio" name="soloparent" value="Yes">Yes
                    <input type="radio" name="soloparent" value="No">No
                    <input type="text" name="children" placeholder="Number of children">
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Are you a 4p's Member?</h3>
                    <input type="radio" name="member" value="Yes">Yes
                    <input type="radio" name="member" value="No">No
                    <input type="text" name="program" placeholder="Program">
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Are you an OFW Dependent?</h3>
                    <input type="radio" name="dependent" value="Yes">Yes
                    <input type="radio" name="dependent" value="No">No
                    <input type="text" name="relationship" placeholder="Relation to the OFW">
                </td>
            </tr>

            <tr>
                <td><input type="text" name="housenumber" class="form-control" value="<?= htmlspecialchars($row['housenumber'] ?? '') ?>" readonly></td>
                <td><input type="text" name="street" class="form-control" value="<?= htmlspecialchars($row['street'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td><input type="text" name="barangay" class="form-control" value="<?= htmlspecialchars($row['barangay'] ?? '') ?>" readonly></td>
                <td><input type="text" name="city" class="form-control" value="<?= htmlspecialchars($row['city'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td><input type="text" name="province" class="form-control" value="<?= htmlspecialchars($row['province'] ?? '') ?>" readonly></td>
                <td><input type="text" name="zipcode" class="form-control" value="<?= htmlspecialchars($row['zipcode'] ?? '') ?>" readonly></td>
            </tr>

            <tr>
                <td><input type="text" name="birthplace" class="form-control" placeholder="Place of Birth" required></td>
                <td><input type="text" name="occupation" class="form-control" placeholder="Occupation" required></td>
            </tr>

            <tr>
                <td>
                <h4>For the students</h4>
                <input type="text" name="school" class="form-control" placeholder="School" required>
                <input type="text" name="yearlevel" class="form-control" placeholder="Year Level" required>
                <input type="text" name="course" class="form-control" placeholder="Course" required>
                <h4>Educational Benefits</h4>
                <input type="radio" name="benefits" value="LGU">LGU
                <input type="radio" name="benefits" value="DSWD">DSWD
                <input type="radio" name="benefits" value="CSWDO">CSWDO
                <input type="radio" name="benefits" value="CHED">CHED
                <input type="radio" name="benefits" value="NGO">NGO
                <input type="radio" name="benefits" value="Athlete">Athlete
                <input type="radio" name="benefits" value="Scholar">Scholar
            </td>
            </tr>

            <tr>
                <td>
                <h4>For out of school youth</h4>
                <input type="text" name="enrolled" class="form-control" placeholder="Last Year Enrolled">
                <input type="text" name="attained" class="form-control" placeholder="Last Year Attained">
            </td>
            </tr>

            <tr>
                <td>
                <h4>Senior Citizen</h4>
                <input type="radio" name="senior" value="LGU">LGU
                <input type="radio" name="senior" value="DSWD / SOCPEN">DSWD / SOCPEN
                <input type="radio" name="senior" value="CSWDO">CSWDO
                <input type="radio" name="senior" value="SSS">SSS
                <input type="radio" name="senior" value="GSIS">GSIS
                <input type="radio" name="senior" value="PAGIBIG">PAGIBIG
                <input type="radio" name="senior" value="Private Agency">Private Agency
                <input type="radio" name="senior" value="Insurance">Insurance
            </td>
            </tr>

            <tr>
                <td>
                <h4>Issues and Concern</h4>
                <input type="text" name="issues">
            </td>
            </tr>

            <tr>
                <td>
>>>>>>> 52fad0d (fixed profile and modified application form)
                    <select name="aid_type" required>
                        <option value="Financial Aid">Financial Aid</option>
                        <option value="Scholarship">Scholarship</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <a href="../resident/index.php">Cancel</a>
                </td>

                <td>
                    <button type="submit" name="apply_btn">
                        Apply
                    </button>
                </td>
            </tr>

        </table>

    </form>

</body>
</html>
