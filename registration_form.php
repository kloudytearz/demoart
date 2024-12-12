<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$phonenum = $_POST['phonenum'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($username) && !empty($password)) {

    $name = $fname . ' ' . $lname;

    $host = "localhost";
    $dbname = "ProjectTest";
    $dbusername = "root";
    $dbpassword = "";

    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists, please choose another.";
        exit;
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql_users = "INSERT INTO users (username, password, name, email, address) VALUES (?, ?, ?, ?, ?)";
        $stmt_users = $conn->prepare($sql_users);

        if ($stmt_users === false) {
            die("Error preparing the users SQL query: " . $conn->error);
        }

        $address = ""; 
        if (!$stmt_users->bind_param("sssss", $username, $hashed_password, $name, $email, $address)) {
            die("Error binding parameters for users: " . $stmt_users->error);
        }

        $stmt_users->execute();
        
        $sql_registration = "INSERT INTO registration (fname, lname, email, phonenum, username, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_registration = $conn->prepare($sql_registration);

        if ($stmt_registration === false) {
            die("Error preparing the registration SQL query: " . $conn->error);
        }

        if (!$stmt_registration->bind_param("ssssss", $fname, $lname, $email, $phonenum, $username, $hashed_password)) {
            die("Error binding parameters for registration: " . $stmt_registration->error);
        }

        $stmt_registration->execute();

        header("Location: art.html");
        exit;
    }
} else {
    echo "Please fill all required fields.";
}
?>
