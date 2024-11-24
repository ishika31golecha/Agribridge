<?php
session_start(); // Ensure session is started at the very beginning

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

// Ensure that the session contains farmer_id, farmer_name, and farmer_image
if (!isset($_SESSION['farmer_id']) || !isset($_SESSION['farmer_name']) || !isset($_SESSION['farmer_image'])) {
    die("Error: Required session variables are not set. Please log in.");
}

// Get farmer_id from session
$farmer_id = $_SESSION['farmer_id'];

// Fetch the crop data for this farmer
$sql = "SELECT * FROM farmer_input WHERE farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

// Store all the crop information
$cropData = [];
while ($row = $result->fetch_assoc()) {
    $cropData[] = $row;
}

// Close the connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .sideHeader {
            position: absolute;
            right: 0;
            background-color: #6D6D03;
            height: 100%;
            width: 300px;
            padding-top: 20px;
            overflow: hidden;
        }

        .sideHeader button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 20px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
            width: 90%;
        }

        .sideHeader button.active {
            background-color: white;
            color: #6D6D03;
            font-weight: bold;
        }

        .content {
            margin-left: 50px;
            margin-top: 50px;
            max-width: 800px;
            font-size: 16px;
        }

        .cropInfo, .cropImage {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-top: 10px;
            background-color: #f9f9f9;
        }

        .cropInfo h2, .cropImage h2 {
            font-size: 24px;
            color: #6D6D03;
            margin-bottom: 15px;
        }

        .cropInfo p, .cropImage p {
            line-height: 1.5;
            margin: 8px 0;
        }

        .cropImage img {
            width: 200px;
            height: auto;
            border-radius: 10px;
            margin: 10px 0;
        }

        .navButtons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .navButtons button {
            padding: 10px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 45%;
        }

        .profile {
            position: absolute;
            bottom: 20px;
            right: 10px;
            display: flex;
            align-items: center;
            background-color: #6D6D03;
            padding: 10px;
            border-radius: 50px;
        }

        .profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile span {
            color: white;
            font-size: 18px;
        }

        .payment_gain_history{
            position: absolute;
            right: 50px;
            top: 210px;
            padding: 10px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 200px;
        }
    </style>
</head>
<a>
    <!-- Sidebar for Information and Image Sections -->
    <div class="sideHeader">
        <button type="button" onclick="showSection('info')" class="info">Information</button>
        <button type="button" onclick="showSection('img')" class="img">Image</button>
    </div>

    <!-- Information Section -->
    <div class="content" id="info" style="display: block;">
        <div class="cropInfo" id="cropData"></div>
        <div class="navButtons">
            <button id="prevBtn" onclick="showPreviousCrop()">Previous Crop</button>
            <button id="nextBtn" onclick="showNextCrop()">Next Crop</button>
        </div>
    </div>

    <!-- Image Section -->
    <div class="content" id="img" style="display: none;">
        <div class="cropImage" id="cropImage"></div>
    </div>

    <a href="farmer_payment_recieved.php"><button type="button" class="payment_gain_history">Payments Recieved</button></a>

    <!-- Farmer Profile -->
    <div class="profile">
        <img src="<?php echo isset($_SESSION['farmer_image']) ? $_SESSION['farmer_image'] : 'img/pfppic.jpg'; ?>" alt="Profile Picture">
        <span><?php echo isset($_SESSION['farmer_name']) ? $_SESSION['farmer_name'] : 'Farmer Name'; ?></span>
    </div>

    <script>
        let currentCropIndex = 0;
        const cropData = <?php echo !empty($cropData) ? json_encode($cropData) : '[]'; ?>;

        // Display crop information for the current crop index
        function displayCropInfo(index) {
            const crop = cropData[index];
            let infoHtml = `
                <h2>Crop Information:</h2>
                <p><strong>Crop Id:</strong> ${crop.cropId}</p>
                <p><strong>Crop Name:</strong> ${crop.cropName}</p>
                <p><strong>Crop Type:</strong> ${crop.cropType}</p>
                <p><strong>Season:</strong> ${crop.season}</p>
                <p><strong>Yield Per Acre:</strong> ${crop.ypa}</p>
                <p><strong>Land Location:</strong> ${crop.landLocation}</p>
                <p><strong>Land Area (acres):</strong> ${crop.areaLand}</p>
                <p><strong>Soil Type:</strong> ${crop.soilType}</p>
                <p><strong>Irrigation Type:</strong> ${crop.irrigationType}</p>
                <p><strong>Payment Date:</strong> ${crop.payDate}</p>
                <p><strong>Name of Dealer:</strong> ${crop.nod}</p>
                <p><strong>Cost:</strong> ${crop.cost}</p>
                <p><strong>Amount Sold:</strong> ${crop.amountSold}</p>
            `;
            document.getElementById("cropData").innerHTML = infoHtml;
        }

        // Display crop image
        function displayCropImage(index) {
            const crop = cropData[index];
            const cropImage = crop.cimg || 'img/default_crop.jpg';
            const landImage = crop.limg || 'img/default_land.jpg';
            const paymentImage = crop.pimg || 'img/default_payment.jpg';

            let imageHtml = `
                <h2>Uploaded Images:</h2>
                <p><strong>Crop Image:</strong> <img src="uploads/${cropImage}" alt="Crop Image"></p>
                <p><strong>Land Image:</strong> <img src="uploads/${landImage}" alt="Land Image"></p>
                <p><strong>Payment Image:</strong> <img src="uploads/${paymentImage}" alt="Payment Image"></p>
            `;
            document.getElementById("cropImage").innerHTML = imageHtml;
        }

        // Show the next crop record
        function showNextCrop() {
            currentCropIndex = (currentCropIndex + 1) % cropData.length;
            displayCropInfo(currentCropIndex);
            displayCropImage(currentCropIndex);
        }

        // Show the previous crop record
        function showPreviousCrop() {
            currentCropIndex = (currentCropIndex - 1 + cropData.length) % cropData.length;
            displayCropInfo(currentCropIndex);
            displayCropImage(currentCropIndex);
        }

        // Toggle between information and image sections
        function showSection(section) {
            const sections = ['info', 'img'];
            sections.forEach((sec) => {
                document.getElementById(sec).style.display = sec === section ? 'block' : 'none';
            });
        }

        // Initial display of crop data
        if (cropData.length > 0) {
            displayCropInfo(currentCropIndex);
            displayCropImage(currentCropIndex);
        }

        document.addEventListener("DOMContentLoaded", function() {
            showSection('info');
        });
    </script>
</body>
</html>
