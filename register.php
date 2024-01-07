<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "demolog");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to check if the email already exists
    $checkEmailQuery = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmailQuery->bind_param("s", $email);
    $checkEmailQuery->execute();
    $resultEmail = $checkEmailQuery->get_result();

    if ($resultEmail->num_rows > 0) {
        echo "Email already exists. Please choose a different email.";
        exit();
    }
    $checkEmailQuery->close();

    // Use prepared statement to insert the new user
    $insertQuery = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $insertQuery->bind_param("sss", $username, $email, $password);

    if ($insertQuery->execute()) {
        // Registration successful
        echo '<script>alert("Registration successful!"); window.location.href = "login.html";</script>';
        exit();
    } else {
        echo "Error: " . $insertQuery->error;
    }

    // Close the prepared statement
    $insertQuery->close();

    // Close the database connection
    $conn->close();
}
?>








