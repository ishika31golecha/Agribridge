<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agribridge";  // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data in the correct order
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNo = $_POST['contactNo'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $regDate = $_POST['regDate'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Check if an image file was uploaded
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
        // Handle file upload for image
        $image = $_FILES['imageUpload']['tmp_name'];
        $imgContent = file_get_contents($image); // Convert image to binary data
    } else {
        // Use an empty string instead of NULL if no image is uploaded
        $imgContent = '';  
    }

    // Prepare the SQL statement to insert the data
    $sql = "INSERT INTO dealer_signup (name, email, contactNo, city, district, state, country, regDate, password, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind the parameters (s = string)
    $stmt->bind_param("ssssssssss", $name, $email, $contactNo, $city, $district, $state, $country, $regDate, $password, $imgContent);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: dealer_login.html");
        exit(); // Stop further script execution after redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
