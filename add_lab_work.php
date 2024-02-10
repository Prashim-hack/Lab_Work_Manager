<?php
session_start();
$showAlert = false;
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $lab_work_number = $_POST['lab_work_number'];
    $description = $_POST['description'];
    $username = $_SESSION['username'];

    $conn = mysqli_connect("localhost", "root", "", "lab_work_manager");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $user_table_name = "lab_works_" . strtolower($username);
    $file_table_name = "lab_work_" . strtolower($username) . "_files";

    $create_table_query = "CREATE TABLE IF NOT EXISTS `$user_table_name` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `lab_work_number` VARCHAR(50) NOT NULL,
        `description` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if (!mysqli_query($conn, $create_table_query)) {
        echo "Error creating table: " . mysqli_error($conn);
        exit();
    }

    $create_file_table_query = "CREATE TABLE IF NOT EXISTS `$file_table_name` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `lab_work_id` INT(11) NOT NULL,
        `file_name` VARCHAR(255) NOT NULL,
        `file_size` INT(11) NOT NULL,
        `file_type` VARCHAR(100) NOT NULL,
        `file_content` LONGBLOB NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (lab_work_id) REFERENCES `$user_table_name` (id) ON DELETE CASCADE
    )";
    if (!mysqli_query($conn, $create_file_table_query)) {
        echo "Error creating file table: " . mysqli_error($conn);
        exit();
    }

    // Check if the same lab work number and title already exist
    $check_query = "SELECT COUNT(*) AS count FROM `$user_table_name` WHERE title = ? AND lab_work_number = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $title, $lab_work_number);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);
    if ($row['count'] > 0) {
        $showAlert = true;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO `$user_table_name` (title, lab_work_number, description) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $title, $lab_work_number, $description);
    mysqli_stmt_execute($stmt);
    $lab_work_id = mysqli_insert_id($conn); // Get the ID of the inserted lab work

    $file_stmt = mysqli_prepare($conn, "INSERT INTO `$file_table_name` (lab_work_id, file_name, file_size, file_type, file_content) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($file_stmt, "issss", $lab_work_id, $file_name, $file_size, $file_type, $file_content);

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['files']['name'][$key];
        $file_size = $_FILES['files']['size'][$key];
        $file_type = $_FILES['files']['type'][$key];
        $file_content = file_get_contents($_FILES['files']['tmp_name'][$key]);

        mysqli_stmt_execute($file_stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($file_stmt);
    mysqli_close($conn);

    header("Location: dashboard.php");

    if ($row['count'] > 0) {
        $showAlert = true;
    }
    if ($showAlert) {
        echo '<script>alert("Lab work with the same title and lab work number already exists.");</script>';
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lab Work - Lab Work Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200">
    
    <nav class="bg-gray-800 py-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Lab Work Manager</h1>
                <a href="dashboard.php" class="text-white hover:text-gray-300">Dashboard</a>
            </div>
        </div>
    </nav>


    <div class="container mx-auto mt-10">
        <h2 class="text-3xl mb-5">Add Lab Work</h2>
        <div class="bg-white p-6 rounded shadow-md">
            <!-- Add lab work form -->
            <form action="add_lab_work.php" method="POST" enctype="multipart/form-data" id="addLabWorkForm">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700">Title:</label>
                    <input type="text" id="title" name="title"
                        class="form-input mt-1 block w-full border-2 border-gray-500" required>
                </div>
                <div class="mb-4">
                    <label for="lab_work_number" class="block text-gray-700">Lab Work Number:</label>
                    <input type="text" id="lab_work_number" name="lab_work_number"
                        class="form-input mt-1 block w-full border-2 border-gray-500" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description:</label>
                    <textarea id="description" name="description"
                        class="form-textarea mt-1 block w-full border-2 border-gray-500 " required></textarea>
                </div>
                <div class="mb-4">
                    <label for="files" class="block text-gray-700">Files:</label>
                    <input type="file" id="files" name="files[]" class="form-input mt-1 block w-full" required>
                    <div id="additionalFiles"></div>
                    <button type="button" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-500 mt-2"
                        onclick="addFileInput()">Add Another File</button>
                </div>
                <button type="submit" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-500">Add Lab
                    Work</button>
            </form>
        </div>
    </div>

    <script>
        function addFileInput() {
            var newFileInput = document.createElement('input');
            newFileInput.type = 'file';
            newFileInput.name = 'files[]';
            newFileInput.className = 'form-input mt-1 block w-full';
            newFileInput.required = true;
            document.getElementById('additionalFiles').appendChild(newFileInput);
        }
    </script>

</body>

</html>