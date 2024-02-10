<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "lab_work_manager");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch file details by ID
function getFileDetails($conn, $file_id) {
    $file_table_name = "lab_work_" . strtolower($_SESSION['username']) . "_files";
    $query = "SELECT * FROM `$file_table_name` WHERE id = $file_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Check if file ID is provided in the URL
if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];

    // Fetch file details
    $file = getFileDetails($conn, $file_id);

    if ($file) {
        // Set headers for file download
        header("Content-Disposition: attachment; filename=\"" . $file['file_name'] . "\"");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . strlen($file['file_content']));
        // Output file content
        echo $file['file_content'];
        exit();
    } else {
        echo "File not found.";
    }
} else {
    echo "File ID not provided.";
}

// Close database connection
mysqli_close($conn);
?>
