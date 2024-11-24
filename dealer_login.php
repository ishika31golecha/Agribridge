<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agribridge";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the email and password from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $sql = "SELECT * FROM dealer_signup WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with the given email exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct, start a session and redirect to the dashboard

            // Store user information in the session
            $_SESSION['dealer_id'] = $row['id'];
            $_SESSION['dealer_name'] = $row['name'];
            $_SESSION['dealer_email'] = $row['email'];
            $_SESSION['dealer_image'] = $row['image']; // Assuming the image is stored in the DB

            // Redirect to the dashboard
            header("Location: dealer_search.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with that email.";
    }
    
    $stmt->close();
}

$conn->close();
?>
