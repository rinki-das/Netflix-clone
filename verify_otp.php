<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "demolog");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $otp = $_POST["otp"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Verify OTP
    $checkOtpSql = "SELECT * FROM users WHERE email='$email' AND otp='$otp' AND otp_expiry > NOW()";
    $result = $conn->query($checkOtpSql);

    if ($result->num_rows > 0 && $newPassword === $confirmPassword) {
        // OTP is valid, update the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updatePasswordSql = "UPDATE users SET password='$hashedPassword', otp=NULL, otp_expiry=NULL WHERE email='$email'";
        
        if ($conn->query($updatePasswordSql) === TRUE) {
            echo "Password updated successfully!";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "Invalid or expired OTP, or passwords do not match.";
    }
}

$conn->close();
?>
