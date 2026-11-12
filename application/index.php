
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
                    <input type="text" name="firstname"
                    placeholder="Firstname" required>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="text" name="middlename"
                    placeholder="Middlename" required>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="text" name="lastname"
                    placeholder="Lastname" required>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="email" name="email"
                    placeholder="Email" required>
                </td>
            </tr>

            <tr>
                <td>
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
