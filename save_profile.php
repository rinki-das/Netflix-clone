<!-- php/save_profile.php -->
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $profileName = filter_input(INPUT_POST, 'profileName', FILTER_SANITIZE_STRING);
    $profileImageURL = filter_input(INPUT_POST, 'profileImageURL', FILTER_VALIDATE_URL);

    if ($profileName && $profileImageURL) {
        $conn = new mysqli("localhost", "your_db_username", "your_db_password", "your_db_name");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $userId = $_SESSION["user_id"];
        $insertQuery = "INSERT INTO profiles (user_id, profile_name, profile_image_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iss", $userId, $profileName, $profileImageURL);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true, 'message' => 'Profile added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

