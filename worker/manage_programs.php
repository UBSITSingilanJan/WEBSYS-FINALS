
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

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function redirect_programs() {
    $search = trim($_POST['q'] ?? $_GET['q'] ?? "");

    $query = $search !== ""
        ? "?q=" . urlencode($search)
        : "";

    header("Location: manage_programs.php" . $query);
    exit();
}

// HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? "";

    // ADD PROGRAM
    if ($action === "add_program") {

        $name = trim($_POST['program_name'] ?? "");
        $description = trim($_POST['description'] ?? "");

        if ($name === "" || $description === "") {

            $_SESSION['flash'] = "Please complete all required fields.";

        } else {

            // Check if program already exists
            $check = $pdo->prepare("
                SELECT program_id
                FROM assistance_program
                WHERE LOWER(program_name) = LOWER(:name)
            ");

            $check->execute([
                ":name" => $name
            ]);

            if ($check->fetch(PDO::FETCH_ASSOC)) {

                $_SESSION['flash'] = "This program already exists.";

            } else {

                $sql = "INSERT INTO assistance_program
                        (program_name, description, status, created_by)
                        VALUES
                        (:name, :description, 'Active', :created_by)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":name" => $name,
                    ":description" => $description,
                    ":created_by" => $_SESSION['user_id']
                ]);

                $_SESSION['flash'] = "Assistance program added successfully.";
            }
        }

        redirect_programs();
    }

    // EDIT PROGRAM
    if ($action === "edit_program") {

        $program_id = filter_var(
            $_POST['program_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $name = trim($_POST['program_name'] ?? "");
        $description = trim($_POST['description'] ?? "");

        if (!$program_id || $name === "" || $description === "") {

            $_SESSION['flash'] = "Invalid program information.";

        } else {

            $check = $pdo->prepare("
                SELECT program_id
                FROM assistance_program
                WHERE LOWER(program_name) = LOWER(:name)
                AND program_id != :program_id
            ");

            $check->execute([
                ":name" => $name,
                ":program_id" => $program_id
            ]);

            if ($check->fetch(PDO::FETCH_ASSOC)) {

                $_SESSION['flash'] = "Another program already uses that name.";

            } else {

                $sql = "UPDATE assistance_program
                        SET program_name = :name,
                            description = :description
                        WHERE program_id = :program_id";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":name" => $name,
                    ":description" => $description,
                    ":program_id" => $program_id
                ]);

                $_SESSION['flash'] = "Program updated successfully.";
            }
        }

        redirect_programs();
    }

    // ACTIVATE / DEACTIVATE PROGRAM
    if ($action === "change_status") {

        $program_id = filter_var(
            $_POST['program_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = $_POST['status'] ?? "";

        if (
            !$program_id ||
            !in_array($status, ["Active", "Inactive"], true)
        ) {

            $_SESSION['flash'] = "Invalid program information.";

        } else {

            $sql = "UPDATE assistance_program
                    SET status = :status
                    WHERE program_id = :program_id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":status" => $status,
                ":program_id" => $program_id
            ]);

            $_SESSION['flash'] = "Program status updated successfully.";
        }

        redirect_programs();
    }

    $_SESSION['flash'] = "Invalid action.";
    redirect_programs();
}

// FETCH PROGRAMS
$search = trim($_GET['q'] ?? "");

$sql = "SELECT *
        FROM assistance_program";

$params = [];

if ($search !== "") {

    $sql .= " WHERE (
                program_name LIKE :search1
                OR description LIKE :search2
              )";

    $keyword = "%" . $search . "%";

    $params = [
        ":search1" => $keyword,
        ":search2" => $keyword
    ];
}

$sql .= " ORDER BY program_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_SESSION['flash'] ?? "";
unset($_SESSION['flash']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Programs</title>

    <link rel="stylesheet" href="worker_style.css">
    <link rel="stylesheet" href="manage_programs.css">

</head>

<body>

<div class="dashboard">


    <aside class="sidebar">
        <h3>Financial Aid System</h3>
        <a href="index.php">Dashboard</a>
        <a href="manage_programs.php" class="active">Add Program</a>
        <a href="manage_applications.php">Manage Applications</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main-content">

        <div class="topbar">

            <div>
                <h2>Manage Programs</h2>

                <p class="page-subtitle">
                    Create and manage available financial assistance programs.
                </p>
            </div>

        </div>


        <!-- FLASH MESSAGE -->
        <?php if ($flash !== "") { ?>

            <div class="alert-message">
                <?php echo e($flash); ?>
            </div>

        <?php } ?>


        <!-- PROGRAM MANAGEMENT -->
        <section class="panel programs-panel">

            <div class="programs-header">

                <h3>Assistance Programs</h3>

                <button
                    type="button"
                    class="add-program-btn"
                    onclick="toggleAddForm()"
                >
                    + Add New Program
                </button>

            </div>


            <!-- ADD PROGRAM FORM -->
            <form
                method="POST"
                class="program-form"
                id="programForm"
            >

                <input
                    type="hidden"
                    name="action"
                    value="add_program"
                >

                <h3>Add New Assistance Program</h3>

                <div class="form-group">

                    <label>Program Name</label>

                    <input
                        type="text"
                        name="program_name"
                        class="form-control"
                        placeholder="e.g. Livelihood Assistance"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Description</label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"
                        placeholder="Enter program description..."
                        required
                    ></textarea>

                </div>


                <button
                    type="submit"
                    class="save-program-btn"
                >
                    Save Program
                </button>

            </form>


            <!-- SEARCH -->
            <form method="GET" class="program-search">

                <input
                    type="text"
                    name="q"
                    class="search-input"
                    placeholder="Search programs..."
                    value="<?php echo e($search); ?>"
                >

                <button type="submit" class="search-btn">
                    Search
                </button>

                <?php if ($search !== "") { ?>

                    <a
                        href="manage_programs.php"
                        class="search-clear"
                    >
                        Clear Search
                    </a>

                <?php } ?>

            </form>


            <!-- PROGRAM TABLE -->
            <div class="table-wrapper">

                <table class="program-table">

                    <thead>

                        <tr>
                            <th>Program Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php if (!empty($programs)) { ?>

                            <?php foreach ($programs as $program) { ?>

                                <?php
                                    $statusClass = strtolower(
                                        $program['status']
                                    );
                                ?>

                                <tr>

                                    <!-- PROGRAM NAME -->
                                    <td>

                                        <strong>
                                            <?php echo e($program['program_name']); ?>
                                        </strong>

                                    </td>


                                    <!-- DESCRIPTION -->
                                    <td>

                                        <div class="program-description">
                                            <?php echo e($program['description']); ?>
                                        </div>

                                    </td>


                                    <!-- STATUS -->
                                    <td>

                                        <span class="program-status status-<?php echo e($statusClass); ?>">
                                            <?php echo e($program['status']); ?>
                                        </span>

                                    </td>


                                    <!-- ACTIONS -->
                                    <td class="program-actions">

                                        <!-- EDIT -->
                                        <details class="edit-program">

                                            <summary class="edit-btn">
                                                Edit
                                            </summary>

                                            <form
                                                method="POST"
                                                class="edit-form"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="edit_program"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="program_id"
                                                    value="<?php echo (int)$program['program_id']; ?>"
                                                >

                                                <label>Program Name</label>

                                                <input
                                                    type="text"
                                                    name="program_name"
                                                    value="<?php echo e($program['program_name']); ?>"
                                                    maxlength="150"
                                                    required
                                                >

                                                <label>Description</label>

                                                <textarea
                                                    name="description"
                                                    rows="4"
                                                    required
                                                ><?php echo e($program['description']); ?></textarea>

                                                <button
                                                    type="submit"
                                                    class="save-program-btn"
                                                >
                                                    Save Changes
                                                </button>

                                            </form>

                                        </details>


                                        <!-- ACTIVATE / DEACTIVATE -->
                                        <form
                                            method="POST"
                                            class="status-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="change_status"
                                            >

                                            <input
                                                type="hidden"
                                                name="program_id"
                                                value="<?php echo (int)$program['program_id']; ?>"
                                            >

                                            <?php if ($program['status'] === "Active") { ?>

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="Inactive"
                                                >

                                                <button
                                                    type="submit"
                                                    class="deactivate-btn"
                                                    onclick="return confirm('Are you sure you want to deactivate this program?');"
                                                >
                                                    Deactivate
                                                </button>

                                            <?php } else { ?>

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="Active"
                                                >

                                                <button
                                                    type="submit"
                                                    class="activate-btn"
                                                >
                                                    Activate
                                                </button>

                                            <?php } ?>

                                        </form>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="4" class="empty-message">
                                    No assistance programs found.
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>


            <p class="program-count">
                Showing <?php echo count($programs); ?> program(s).
            </p>

        </section>


        <div class="footer">
            Financial Aid System | Worker Management
        </div>

    </main>

</div>


<script>

function toggleAddForm() {

    const form = document.getElementById("programForm");

    form.classList.toggle("show");

}

</script>

</body>
</html>
