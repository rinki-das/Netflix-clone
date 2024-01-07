<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="src/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="forgot-password-verify-page">
    <div class="container">
        <h2>Forgot Password</h2>
        
        <form id="otpForm" action="verify_otp.php" method="post">
            <label for="otp">Enter OTP:</label>
            <input type="text" name="otp" required>
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            <input type="hidden" name="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">
            <button type="submit">Verify OTP</button>
        </form>

        <script>
            // Check if OTP has been sent and show notification
            document.addEventListener('DOMContentLoaded', function () {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const otpSent = urlParams.get('otp_sent');

                if (otpSent === 'true') {
                    Toastify({
                        text: "OTP sent successfully!",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#4caf50",
                    }).showToast();
                }
            });
        </script>
    </div>
</body>
</html>

