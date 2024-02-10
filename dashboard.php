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

$sql = "SELECT *  FROM `$user_table_name`";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Work Manager - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200">
    <nav class="bg-gray-700 py-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Lab Work Manager</h1>
                <a href="logout.php" class="text-white hover:text-gray-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        <h2 class="text-3xl mb-5">Welcome to Your Dashboard</h2>
        <div class="bg-white p-6 rounded shadow-md">
            <p class="text-lg">Hello,
                <?php echo $_SESSION['username']; ?>!
            </p>
            <p class="mt-4">You can start managing your lab works from here.</p>
            <a href="add_lab_work.php"
                class="bg-gray-700 text-white py-2 px-4 rounded mt-4 inline-block hover:bg-gray-500">Add Lab Work</a>

            <!-- Display lab works as cards -->
            <h1 class="text-xl font-bold text-black py-6">Your Lab Works</h1>
            <div class="grid grid-cols-3 gap-4 mt-6">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="border border-gray-300 bg-white rounded-lg p-4 shadow-md">
                        <h3 class="text-lg font-semibold mb-2">Title:
                            <?php echo $row['title']; ?>
                        </h3>
                        <p class="text-gray-500">Lab Work Number:
                            <?php echo $row['lab_work_number']; ?>
                        </p>
                        <p class="text-gray-700 mb-2">Description:
                            <?php echo $row['description']; ?>
                        </p>
                        <p class="text-gray-700 mb-3">Download Files:</p>
                        <?php
                        // Fetch files associated with this lab work
                        $files_query = "SELECT * FROM `$file_table_name` WHERE lab_work_id = " . $row['id'];
                        $files_result = mysqli_query($conn, $files_query);

                        // Display download links for each file
                        while ($file_row = mysqli_fetch_assoc($files_result)) {
                            echo '<a href="download.php?file_id=' . $file_row['id'] . '" class="bg-blue-500 text-white py-2 px-4 rounded mt-2 inline-block hover:bg-blue-600">' . $file_row['file_name'] . '</a>';
                        }
                        ?>
                        <!-- Delete button with confirmation -->
                        <div>
                        <form action="delete_lab_work.php" method="post" class="inline" onsubmit="return confirmDelete()">
                            <input type="hidden" name="lab_work_id" value="<?php echo $row['id']; ?>">
                            <button type="submit"
                                class="bg-red-500 text-white py-2 px-4 rounded mt-2 hover:bg-red-600">Delete</button>
                        </form>
                        <form action="update_lab_work.php" method="get" class="inline">
                            <input type="hidden" name="lab_work_id" value="<?php echo $row['id']; ?>">
                            <button type="submit"
                                class="bg-yellow-500 text-white py-2 px-4 rounded mt-2 inline-block hover:bg-yellow-600">Update</button>
                        </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php

            mysqli_close($conn);
            ?>
        </div>
    </div>
</body>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this lab work?");
    }
</script>

</html>