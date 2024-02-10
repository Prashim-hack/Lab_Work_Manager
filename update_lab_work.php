<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "lab_work_manager");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'];
$user_table_name = "lab_works_" . strtolower($username);

// Initialize variables to store lab work details
$title = $lab_work_number = $description = "";

// Check if lab work ID is provided via GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['lab_work_id'])) {
    $lab_work_id = $_GET['lab_work_id'];
    
    // Retrieve existing lab work details from the database
    $select_query = "SELECT * FROM `$user_table_name` WHERE id = $lab_work_id";
    $result = mysqli_query($conn, $select_query);

    if ($row = mysqli_fetch_assoc($result)) {
        $title = $row['title'];
        $lab_work_number = $row['lab_work_number'];
        $description = $row['description'];
    } else {
        echo "Lab work not found.";
        exit();
    }
}

// Check if form is submitted via POST request


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Work Manager - Update Lab Work</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200">
    <nav class="bg-gray-700 py-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Lab Work Manager</h1>
                <a href="dashboard.php" class="text-white hover:text-gray-300">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        <h2 class="text-3xl mb-5">Update Lab Work</h2>
        <div class="bg-white p-6 rounded shadow-md">
            <form action="update_lab_work.php" method="post">
                <input type="hidden" name="lab_work_id" value="<?php echo $lab_work_id; ?>">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="lab_work_number" class="block text-gray-700 font-semibold mb-2">Lab Work Number</label>
                    <input type="text" id="lab_work_number" name="lab_work_number" value="<?php echo $lab_work_number; ?>" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2 border rounded-lg"><?php echo $description; ?></textarea>
                </div>
                <button type="submit" name = "submit"class="bg-blue-500 text-white py-2 px-4 rounded mt-2 hover:bg-blue-600">Update Lab Work</button>
            </form>
        </div>
    </div>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $lab_work_id = $_POST['lab_work_id'];

        // Retrieve form data
        $title = $_POST['title'];
        $lab_work_number = $_POST['lab_work_number'];
        $description = $_POST['description'];

        // Update lab work entry in the database
        $update_query = "UPDATE `$user_table_name` SET title = '$title', lab_work_number = '$lab_work_number', description = '$description' WHERE id = $lab_work_id";

        if (mysqli_query($conn, $update_query)) {
            echo "Lab work updated successfully.";
        } else {
            echo "Error updating lab work: " . mysqli_error($conn);
        }
    } else {
        echo "Lab work ID not provided.";
    }
}
mysqli_close($conn);
?>
