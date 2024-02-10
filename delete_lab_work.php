<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lab_work_id'])) {
    $conn = mysqli_connect("localhost", "root", "", "lab_work_manager");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = $_SESSION['username'];
    $user_table_name = "lab_works_" . strtolower($username);

    $lab_work_id = $_POST['lab_work_id'];

    $sql = "DELETE FROM `$user_table_name` WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $lab_work_id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);


    header("Location: dashboard.php");
    exit();
} else {

    header("Location: dashboard.php");
    exit();
}
?>
