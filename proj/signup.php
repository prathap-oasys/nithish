<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = "localhost";
$username = "root";
$dbpassword = "root";
$database = "rest";

// Create a new mysqli connection
$connec = new mysqli($host, $username, $dbpassword);

// Check the connection
if ($connec->connect_error) {
    die("Connection failed: " . $connec->connect_error);
}

// Create the database if it does not exist
$createDbSql = "CREATE DATABASE IF NOT EXISTS $database";
if ($connec->query($createDbSql) === TRUE) {
    echo "Database created successfully or already exists.\n";
} else {
    echo "Error creating database: " . $connec->error . "\n";
}

// Select the database
$connec->select_db($database);

// Create the table if it does not exist
$createTableSql = "CREATE TABLE IF NOT EXISTS register (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mail VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL
)";
if ($connec->query($createTableSql) === TRUE) {
    echo "Table created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $connec->error . "\n";
}

// Validate and sanitize input
$name = filter_input(INPUT_POST, 'name');
$mail = filter_input(INPUT_POST, 'mail');
$password = filter_input(INPUT_POST, 'password');
$phone = filter_input(INPUT_POST, 'phone');

// Hash the password
$hash_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute the SQL statement using a prepared statement
$sql = "INSERT INTO register (name, mail, password, phone) VALUES (?, ?, ?, ?)";
$stmt = $connec->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssss", $name, $mail, $hash_password, $phone);

    if ($stmt->execute()) {
        echo 'Register Success';
        header('Location:signin.html');

    } else {
        // Log the error instead of displaying it to the user
        error_log("Error executing statement: " . $stmt->error);
        echo 'Registration failed. Please try again later.';
    }

    $stmt->close();
} else {
    // Log the error instead of displaying it to the user
    error_log("Error preparing statement: " . $connec->error);
    echo 'Registration failed. Please try again later.';
}

// Close the database connection
$connec->close();

?>

