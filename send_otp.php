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

    // Check if the entered email exists in the database
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkQuery->bind_param("s", $email);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Store the OTP in the database for verification
        $sql = "UPDATE users SET otp='$otp', otp_expiry=DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE email='$email'";
        
        if ($conn->query($sql) === TRUE) {
            // Send email with OTP
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = ''; // Your SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = ''; // Your Gmail email address
                $mail->Password   = '';  // Your Gmail email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = ;

                // Recipients
                $mail->setFrom('your email here', 'Company Admin');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body    = 'Your OTP is: ' . $otp;

                $mail->send();

                // Redirect to forgot_password.php with a parameter to indicate OTP sent
                header("Location: forgot_password.php?otp_sent=true&email={$email}");
            } catch (Exception $e) {
                echo "Error sending OTP: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Email not registered. Please enter a registered email address.";
    }

    $checkQuery->close();
}

$conn->close();
?>

