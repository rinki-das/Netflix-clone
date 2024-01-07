<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedInUsername'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demolog"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create a profile and save it in the database
function createProfile($conn, $username, $loggedInUsername) {
    $profileIconUrl = 'https://mir-s3-cdn-cf.behance.net/project_modules/disp/366be133850498.56ba69ac36858.png';

    // Get the user ID of the logged-in user
    $userIdResult = $conn->query("SELECT id FROM users WHERE username = '$loggedInUsername'");
    $userId = ($userIdResult->num_rows > 0) ? $userIdResult->fetch_assoc()['id'] : null;

    if ($userId !== null) {
        // Insert the profile into the database with the associated user ID
        $sql = "INSERT INTO profiles (user_id, username, profile_icon_url) VALUES ($userId, '$username', '$profileIconUrl')";
        $result = $conn->query($sql);

        if ($result) {
            // Profile saved successfully, update the UI
            echo 'createProfile("' . $username . '");';
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error: User not found.";
    }
}

// Function to fetch existing profiles
function fetchProfiles($conn, $loggedInUsername) {
    $profiles = array();

    if ($loggedInUsername === 'guest') {
        // If the user is a guest, fetch only their profile
        $fetchProfileSql = "SELECT username FROM profiles WHERE username = 'guest' LIMIT 1";
    } else {
        // Get the user ID of the logged-in user
        $userIdResult = $conn->query("SELECT id FROM users WHERE username = '$loggedInUsername'");
        $userId = ($userIdResult->num_rows > 0) ? $userIdResult->fetch_assoc()['id'] : null;

        if ($userId !== null) {
            // Fetch only the profiles created by the logged-in user
            $fetchProfileSql = "SELECT username FROM profiles WHERE user_id = $userId";
        } else {
            // Fallback to fetch all profiles if user not found
            $fetchProfileSql = "SELECT username FROM profiles";
        }
    }

    $fetchProfilesResult = $conn->query($fetchProfileSql);

    if ($fetchProfilesResult->num_rows > 0) {
        while ($row = $fetchProfilesResult->fetch_assoc()) {
            $profiles[] = $row['username'];
        }
    }

    return $profiles;
}

// Process the addProfile action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProfile"])) {
    $guestName = $_POST["guestName"];
    createProfile($conn, $guestName, $_SESSION['loggedInUsername']);
}

// Fetch existing profiles and store them in $existingProfiles
$loggedInUsername = isset($_SESSION['loggedInUsername']) ? $_SESSION['loggedInUsername'] : '';
$existingProfiles = fetchProfiles($conn, $loggedInUsername);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Profiles</title>
    <style>
        body {
            background: #141414;
            font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            font-weight: 300;
            color: #6D6D6D;
            opacity: 0;
            animation: fade-in 500ms ease 200ms 1 forwards;
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: scale(1.2);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        h1 {
            color: #fff;
            font-size: 50px;
        }

        a {
            padding: 10px 20px;
            color: #6D6D6D;
            font-size: 17px;
            text-decoration: none;
            text-transform: uppercase;
            border: 2px solid #6D6D6D;
            transition: all 300ms ease;
        }

        a:hover {
            color: #EBECEC;
            border-color: #EBECEC;
        }

        .logo {
            width: 110px;
            height: 30px;
            margin: 15px 60px;
            background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Netflix_2015_logo.svg/2000px-Netflix_2015_logo.svg.png');
            background-size: cover;
        }

        .wrapper {
            margin: 100px 0;
            text-align: center;
        }

        .profile-wrap {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 800px;
            margin: 50px auto;
        }

        .profile {
            width: 150px;
            margin-bottom: 20px;
        }

        .profile-icon {
            width: 150px;
            height: 150px;
            border: 6px solid #1f1f1f;
            background-size: cover;
            transition: all 300ms ease;
        }

        .profile-name {
            margin: 20px 0;
            line-height: 1.25em;
            transition: all 300ms ease;
            color: #EBECEC;
        }

        .profile:hover .profile-icon {
            border: 6px solid #EBECEC;
        }

        .profile:hover .profile-name {
            color: #EBECEC;
        }

        .user-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border: 6px solid #1f1f1f;
            background-size: cover;
            margin-bottom: 10px;
        }

        .profile-username {
            color: #FCFDFF;
            font-size: 18px;
        }

        .logout-button {
            padding: 10px 20px;
            background-color: #EBECEC;
            color: #141414;
            text-decoration: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 300ms ease;
        }

        .logout-button:hover {
            background-color: #6D6D6D;
            color: #EBECEC;
        }
    </style>
</head>

<body>
    <div class="logo"></div>

    <div class="wrapper">
        <h1>Who's Watching?</h1>
        <div class="profile-wrap" id="profileWrap">
            <?php
// Add your code here to fetch and display existing profiles
foreach ($existingProfiles as $profile) {
    $profileLink = ($profile === $loggedInUsername)
        ? 'myprofile.html'  // Link to myprofile.html for the logged-in user
        : 'myprofile.html?username=' . $profile;  // Link to other profiles
    echo '<a href="' . $profileLink . '" class="profile-link">
            <div class="profile">
                <div class="profile-icon" style="background: url(\'https://mir-s3-cdn-cf.behance.net/project_modules/disp/366be133850498.56ba69ac36858.png\'); background-size: cover;">
                </div>
                <div class="profile-name">
                    ' . $profile . '
                </div>
            </div>
        </a>';
}

            
            ?>
        </div>
        <a href="#" onclick="addProfile()">Add Profile</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function createProfile(username) {
                const profileWrap = document.getElementById("profileWrap");

                const newProfile = document.createElement("div");
                newProfile.classList.add("profile");

                newProfile.innerHTML = `
                    <div class="profile-icon" style="background: url('https://mir-s3-cdn-cf.behance.net/project_modules/disp/366be133850498.56ba69ac36858.png'); background-size: cover;">
                    </div>
                    <div class="profile-name">
                        ${username}
                    </div>
                `;

                profileWrap.appendChild(newProfile);
            }

            window.addProfile = function () {
                promptAndSubmit();
            };

            <?php
            if (isset($_SESSION['loggedInUsername'])) {
                echo 'createProfile("' . $_SESSION['loggedInUsername'] . '");';
            }
            ?>
        });

        function promptAndSubmit() {
            var guestName = prompt("Enter the guest name:");
            if (guestName !== null && guestName.trim() !== "") {
                var form = document.createElement("form");
                form.method = "post";
                form.action = "user.php";
                form.style.display = "none";

                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "addProfile";
                input.value = "true";
                form.appendChild(input);

                var guestNameInput = document.createElement("input");
                guestNameInput.type = "hidden";
                guestNameInput.name = "guestName";
                guestNameInput.value = guestName;
                form.appendChild(guestNameInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>

