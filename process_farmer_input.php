<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agribridge";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if farmer_id is set in the session
if (!isset($_SESSION['farmer_id'])) {
    die("Error: Farmer ID not set in session. Please log in.");
}

// Get farmer_id from session
$farmer_id = $_SESSION['farmer_id'];

// Define upload directory
define("UPLOAD_DIR", "uploads/");

// Allowed file types for upload validation
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Function to upload files with validation
function uploadFile($file, $uploadDir, $allowedTypes, $maxFileSize) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxFileSize) {
            $targetPath = $uploadDir . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return basename($file['name']);
            } else {
                die("Error uploading file: " . basename($file['name']));
            }
        } else {
            die("Invalid file type or file too large for " . basename($file['name']));
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form input
    $cropId = $conn->real_escape_string($_POST['cropId']);
    $cropName = $conn->real_escape_string($_POST['cropName']);
    $cropType = $conn->real_escape_string($_POST['cropType']);
    $season = $conn->real_escape_string($_POST['season']);
    $ypa = $conn->real_escape_string($_POST['ypa']);
    $landId = $conn->real_escape_string($_POST['landId']);
    $landLocation = $conn->real_escape_string($_POST['landLocation']);
    $areaLand = $conn->real_escape_string($_POST['areaLand']);
    $soilType = $conn->real_escape_string($_POST['soilType']);
    $irrigationType = $conn->real_escape_string($_POST['irrigationType']);
    $payDate = $conn->real_escape_string($_POST['payDate']);
    $nod = $conn->real_escape_string($_POST['nod']);
    $cost = $conn->real_escape_string($_POST['cost']);
    $amountSold = $conn->real_escape_string($_POST['amountSold']);

    // Handle image uploads
    $cimg = $_FILES['cimg']['name'] ? uploadFile($_FILES['cimg'], UPLOAD_DIR, $allowedTypes, $maxFileSize) : null;
    $limg = $_FILES['limg']['name'] ? uploadFile($_FILES['limg'], UPLOAD_DIR, $allowedTypes, $maxFileSize) : null;
    $pimg = $_FILES['pimg']['name'] ? uploadFile($_FILES['pimg'], UPLOAD_DIR, $allowedTypes, $maxFileSize) : null;

    // Insert crop input data using prepared statements
    $stmt = $conn->prepare("INSERT INTO farmer_input (farmer_id, cropId, cropName, cropType, season, ypa, landId, cimg, landLocation, areaLand, soilType, irrigationType, limg, payDate, nod, cost, amountSold, pimg) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Check if prepare() was successful
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("iisssisssisssssdds", $farmer_id, $cropId, $cropName, $cropType, $season, $ypa, $landId, $cimg, $landLocation, $areaLand, $soilType, $irrigationType, $limg, $payDate, $nod, $cost, $amountSold, $pimg);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Crop data saved successfully.";
        header("refresh:2;url=farmerInput.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
