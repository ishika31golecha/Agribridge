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

// Ensure that the session contains the dealer_id
$dealer_id = isset($_SESSION['dealer_id']) ? $_SESSION['dealer_id'] : null;

if ($dealer_id) {
    // Prepare a SQL statement to get dealer's name and image
    $sql = "SELECT name, image FROM dealer_signup WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare() was successful
    if ($stmt === false) {
        die("Prepare failed for dealer query: (" . $conn->errno . ") " . $conn->error . " | SQL: $sql");
    }

    $stmt->bind_param("i", $dealer_id);
    $stmt->execute();
    $stmt->bind_result($dealer_name, $dealer_image);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error: Dealer ID not set in session.";
    exit;
}

// Get the farmer_id and crop_name from the URL
$farmer_id = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : null;
$crop_name = isset($_GET['crop_name']) ? $_GET['crop_name'] : null;

$farmer_cost = [];
$crop_cost = [];

// Fetch farmer cost based on the farmer_id
if ($farmer_id && $crop_name) {
    $sql = "SELECT fs.id, fs.name AS farmer_name, fs.image, fs.city, fi.cropName, fi.cost
            FROM farmer_signup fs
            INNER JOIN farmer_input fi ON fs.id = fi.farmer_id
            WHERE fs.id = ? AND fi.cropName = ?";

    $stmt = $conn->prepare($sql);
    
    // Check if prepare() was successful
    if ($stmt === false) {
        die("Prepare failed for farmer query: (" . $conn->errno . ") " . $conn->error . " | SQL: $sql");
    }

    $stmt->bind_param("is", $farmer_id, $crop_name);
    $stmt->execute();
    $stmt->bind_result($farmer_id, $farmer_name, $farmer_image, $farmer_city, $crop_name, $crop_cost);

    while ($stmt->fetch()) {
        $farmer_cost = [
            'id' => $farmer_id,
            'name' => $farmer_name,
            'image' => $farmer_image,
            'city' => $farmer_city
        ];
        $crop_cost = [
            'cropName' => $crop_name,
            'cost' => $crop_cost
        ];
    }
    $stmt->close();
} else {
    echo "Error: Missing farmer_id or crop_name in the URL.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer and Crop cost</title>
    <style>
        .container {
            margin: 50px;
        }

        .farmer-profile {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .farmer-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .farmer-profile .cost {
            font-size: 18px;
        }

        .crop-cost {
            margin-top: 30px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }

        .crop-cost h3 {
            margin-bottom: 20px;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #6D6D03;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #3b3b02;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Farmer Profile Section -->
    <div class="farmer-profile">
        <img src="<?php echo isset($farmer_cost['image']) && $farmer_cost['image'] != "" ? 'uploads/' . $farmer_cost['image'] : 'img/pfppic.jpg'; ?>" alt="Farmer Image">
        <div class="cost">
            <h2><?php echo isset($farmer_cost['name']) ? $farmer_cost['name'] : 'Unknown Farmer'; ?></h2>
            <p>Location: <?php echo isset($farmer_cost['city']) ? $farmer_cost['city'] : 'Unknown City'; ?></p>
        </div>
    </div>

    <!-- Crop cost Section -->
    <div class="crop-cost">
        <h3>Crop Name: <?php echo isset($crop_cost['cropName']) ? $crop_cost['cropName'] : 'Unknown Crop'; ?></h3>
        <p><strong>cost:</strong></p>
        <p><?php echo isset($crop_cost['cost']) ? $crop_cost['cost'] : 'No cost available for this crop.'; ?></p>
    </div>

    <!-- Back Button -->
    <form action="dealer_search.php" method="get">
        <button type="submit" class="back-button">Back to Dashboard</button>
    </form>
</div>

</body>
</html>
