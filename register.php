<?php
    require_once "dbconn.php";
    require_once "classes/applicant.php";

    $conn = new Applicant($pdo);

    $upload_error = null;

    if(isset($_POST['apply_btn'])){

        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $suffix = $_POST['suffix'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $civilstatus = $_POST['civilstatus'];
        $education = $_POST['education'];
        $soloparent = $_POST['soloparent'];
        $children = $_POST['children'];
        $member = $_POST['member'];
        $program = $_POST['program'];
        $dependent = $_POST['dependent'];
        $relationship = $_POST['relationship'];
        $housenumber = $_POST['housenumber'];
        $street = $_POST['street'];
        $barangay = $_POST['barangay'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $zipcode = $_POST['zipcode'];
        $birthplace = $_POST['birthplace'];
        $occupation = $_POST['occupation'];
        $school = $_POST['school'];
        $yearlevel = $_POST['yearlevel'];
        $course = $_POST['course'];
        $benefits = json_encode($_POST['benefits'] ?? []);
        $enrolled = $_POST['enrolled'];
        $attained = $_POST['attained'];
        $senior = json_encode($_POST['senior'] ?? []);
        $issues = $_POST['issues'];

        $uploaded_files = [];
        $upload_dir = __DIR__ . "/../uploads/applications/";

        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            $upload_error = "The upload directory could not be created.";
        }

        $documents_upload = $_FILES['documents'] ?? null;
        if (
            $upload_error === null &&
            (!is_array($documents_upload) || !isset($documents_upload['error']) || !is_array($documents_upload['error']))
        ) {
            $upload_error = "Please select at least one supporting document.";
        }

        if ($upload_error === null) {
            $allowed_types = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
            ];
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            foreach ($documents_upload['error'] as $key => $error) {
                if ($error !== UPLOAD_ERR_OK) {
                    $upload_error = "A supporting document could not be uploaded.";
                    break;
                }

                $tmp_name = $documents_upload['tmp_name'][$key] ?? '';
                $file_size = $documents_upload['size'][$key] ?? 0;
                if (!is_uploaded_file($tmp_name) || $file_size > 5 * 1024 * 1024) {
                    $upload_error = "Each supporting document must be a valid file no larger than 5 MB.";
                    break;
                }

                $file_type = $finfo->file($tmp_name);
                if (!isset($allowed_types[$file_type])) {
                    $upload_error = "Supporting documents must be JPEG, PNG, WebP, or PDF files.";
                    break;
                }

                $new_name = 'application_' . bin2hex(random_bytes(16)) . '.' . $allowed_types[$file_type];
                if (!move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $upload_error = "A supporting document could not be saved.";
                    break;
                }

                $uploaded_files[] = "uploads/applications/" . $new_name;
            }
        }

        if ($upload_error === null && count($uploaded_files) > 0) {
            $documents = json_encode($uploaded_files);

            $conn->insert_data(
                $firstname, $middlename, $lastname, $suffix,
                $contact, $email, $gender, $password, $civilstatus, $education,
                $soloparent, $children, $member, $program, $dependent,
                $relationship, $housenumber, $street, $barangay,
                $city, $province, $zipcode, $birthplace, $occupation,
                $school, $yearlevel, $course, $benefits, $enrolled,
                $attained, $senior, $issues, $documents);

            header("Location: resident/index.php");
            exit();
        }

        foreach ($uploaded_files as $uploaded_file) {
            $file_path = __DIR__ . "/../" . $uploaded_file;
            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
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
    <?php if ($upload_error !== null): ?>
        <p role="alert"><?= htmlspecialchars($upload_error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <table>

            <tr>
			    <td>
			        <input type="text" name="firstname" placeholder="First Name">
			    </td>
			</tr>

			<tr>
			    <td>
			        <input type="text" name="middlename" placeholder="Middle Name">
			    </td>
			</tr>

			<tr>
			    <td>
			        <input type="text" name="lastname" placeholder="Last Name">
			    </td>
			</tr>

			<tr>
			    <td>
			        <input type="text" name="suffix" placeholder="Suffix">
			    </td>
			</tr>

			<tr>
			    <td>
			        <input type="tel" name="contact" placeholder="Contact Number">
			    </td>
			</tr>

			<tr>
			    <td>
			        <input type="email" name="email" placeholder="Email Address">
			    </td>
			</tr>

			<tr>
			    <td>
			        <select name="gender" class="form-control" required>
			            <option value="" disabled selected>Gender</option>
			            <option value="Male">Male</option>
			            <option value="Female">Female</option>
			            <option value="Other">Other</option>
			        </select>
			    </td>
			</tr>

			<tr>
				<td>
					<input type="password" name="password" placeholder="Password">
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
			    <td><input type="text" name="housenumber" class="form-control" placeholder="House Number"></td>
			    <td><input type="text" name="street" class="form-control" placeholder="Street"></td>
			</tr>

			<tr>
			    <td><input type="text" name="barangay" class="form-control" placeholder="Barangay"></td>
			    <td><input type="text" name="city" class="form-control" placeholder="City"></td>
			</tr>

			<tr>
			    <td><input type="text" name="province" class="form-control" placeholder="Province"></td>
			    <td><input type="text" name="zipcode" class="form-control" placeholder="ZIP Code"></td>
			</tr>

			<tr>
			    <td><input type="text" name="birthplace" class="form-control" placeholder="Birthplace"></td>
			    <td><input type="text" name="occupation" class="form-control" placeholder="Occupation"></td>
			</tr>

            <tr>
                <td>
                    <h4>For the students</h4>
                        <input type="text" name="school" class="form-control" placeholder="School" required>
                        <input type="text" name="yearlevel" class="form-control" placeholder="Year Level" required>
                        <input type="text" name="course" class="form-control" placeholder="Course" required>
                    <h4>Educational Benefits</h4>
                        <input type="checkbox" name="benefits[]" value="LGU">LGU
                        <input type="checkbox" name="benefits[]" value="DSWD">DSWD
                        <input type="checkbox" name="benefits[]" value="CSWDO">CSWDO
                        <input type="checkbox" name="benefits[]" value="CHED">CHED
                        <input type="checkbox" name="benefits[]" value="NGO">NGO
                        <input type="checkbox" name="benefits[]" value="Athlete">Athlete
                        <input type="checkbox" name="benefits[]" value="Scholar">Scholar
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
                        <input type="checkbox" name="senior[]" value="LGU">LGU
                        <input type="checkbox" name="senior[]" value="DSWD / SOCPEN">DSWD / SOCPEN
                        <input type="checkbox" name="senior[]" value="CSWDO">CSWDO
                        <input type="checkbox" name="senior[]" value="SSS">SSS
                        <input type="checkbox" name="senior[]" value="GSIS">GSIS
                        <input type="checkbox" name="senior[]" value="PAGIBIG">PAGIBIG
                        <input type="checkbox" name="senior[]" value="Private Agency">Private Agency
                        <input type="checkbox" name="senior[]" value="Insurance">Insurance
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
                    <h4>Upload Supporting Documents</h4>
                    <input type="file" name="documents[]" multiple accept="image/*,.pdf" required>
                </td>
            </tr>

            <tr>
                <td>
                    <a href="index.php">Cancel</a>
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
