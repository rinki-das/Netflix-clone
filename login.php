<?php
session_start(); // Start the session

$conn = new mysqli("localhost", "root", "", "demolog");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = ""; // Initialize the login message variable
$loginFailed = false; // Flag to indicate login failure

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the "identifier" key exists in the $_POST array
    $identifier = isset($_POST["identifier"]) ? $_POST["identifier"] : '';
    $password = $_POST["password"];

    // Check if the form is for login or forgot password
    if (isset($_POST["forgot_password"])) {
        header("Location: forgot_password.html");
        exit();
    } else {
        // Use prepared statement to check both username and email
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set loggedInUsername in the session
                $_SESSION['loggedInUsername'] = $row["username"];

                // Uncomment the line below if you want to test without redirection
                // $loginFailed = false;
            } else {
                $loginFailed = true;
                $loginMessage = "Invalid password";
            }
        } else {
            $loginFailed = true;
            $loginMessage = "User not found";
        }
    }
}

$conn->close();
?>

<script>
    // Display an alert for login success or failure
    document.addEventListener("DOMContentLoaded", function () {
        <?php if ($loginFailed): ?>
            alert("Invalid credentials. <?php echo $loginMessage; ?>");
        <?php elseif (isset($_SESSION['loggedInUsername'])): ?>
            alert("Login successful!");
            window.location.href = "user.php"; // Redirect to user.php
        <?php endif; ?>
    });
</script>

