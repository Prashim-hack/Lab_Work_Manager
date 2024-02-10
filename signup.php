<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Work Manager - Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl text-center mb-5">Signup for Lab Work Manager</h1>
        <div class="max-w-md mx-auto bg-white rounded p-5 shadow-md">
            <form action="signup.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Name" name="name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email" name="email" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Username" name="username" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Password" name="password" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id_number">ID Number</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="id_number" type="text" placeholder="ID Number" name="id_number" required>
                </div>
                <input type="submit" class="bg-gray-700 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" label = "signup">
            </form>
        </div>
    </div>
</body>

<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "lab_work_manager";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id_number = $_POST['id_number'];

    $create_file_table_query = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name varchar(100),
        email varchar(100),
        username varchar(100),
        password varchar(1000),
        id_number int(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if (!mysqli_query($conn, $create_file_table_query)) {
        echo "Error creating file table: " . mysqli_error($conn);
        exit();
    }

    if (empty($name) || empty($email) || empty($username) || empty($password) || empty($id_number)) {
        header("Location: ./signup.php?error=empty_fields");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, username, password, id_number) VALUES ('$name', '$email', '$username', '$hashed_password', '$id_number')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $username;
        header("Location: ./dashboard.php");
        exit();
    } else {
        header("Location: ./signup.php?error=signup_error");
        exit();
    }
}

mysqli_close($conn);
?>



</html>
