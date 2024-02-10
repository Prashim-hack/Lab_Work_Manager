<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Work Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl text-center mb-5">Lab Work Manager</h1>
        <div class="max-w-md mx-auto bg-white rounded p-5 shadow-md">
            <form action="./login.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Username" name="username">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Password" name="password">
                </div>
                <button type="submit" class="bg-gray-700 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Login</button>
                <p class="mt-4">Don't have an account? <a href="signup.php" class="text-blue-500">Create one</a>.</p>
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
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: ./login.php?error=empty_fields");
        exit();
    }
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: ./dashboard.php");
            exit();
        } else {
            header("Location: ./login.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: ./login.php?error=user_not_found");
        exit();
    }
}
mysqli_close($conn);
?>

</html>
