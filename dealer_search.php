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
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind parameters and execute query
    $stmt->bind_param("i", $dealer_id);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind the result and fetch data
    $stmt->bind_result($dealer_name, $dealer_image);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error: Dealer ID not set in session.";
    exit;
}

// Check if a crop name is searched
$crop_search = isset($_POST['crop_search']) ? $_POST['crop_search'] : '';

$farmer_info = [];

if ($crop_search) {
    // Prepare a SQL statement to search for farmers selling the specified crop
    $sql = "SELECT fs.id AS farmer_id, fs.name AS farmer_name, fi.cropName, fs.image, fs.city
            FROM farmer_signup fs
            INNER JOIN farmer_input fi ON fs.id = fi.farmer_id
            WHERE fi.cropName LIKE ?";

    $stmt = $conn->prepare($sql);

    // Check if prepare() was successful
    if ($stmt === false) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind parameters and execute query
    $search_term = '%' . $crop_search . '%';
    $stmt->bind_param("s", $search_term);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind the result and fetch data
    $stmt->bind_result($farmer_id, $farmer_name, $farmer_crop, $farmer_image, $farmer_city);

    while ($stmt->fetch()) {
        $farmer_info[] = [
            'id' => $farmer_id,
            'name' => $farmer_name,
            'cropName' => $farmer_crop,
            'image' => $farmer_image,
            'city' => $farmer_city
        ];
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer - Dashboard</title>
    <style>
        .decoration {
            overflow: hidden;
            position: relative;
            height: 625px;
        }

        .green1 {
            position: absolute;
            right: 40px;
            top: -160px;
            height: 400px;
            width: 400px;
            transform: rotate(-45deg);
            border-radius: 30px;
        }

        .dealer2 {
            transform: rotate(45deg);
            border-radius: 30px;
            position: absolute;
            right: -140px;
            top: 190px;
            height: 300px;
            width: 300px;
        }

        .dealer1 {
            position: absolute;
            top: 190px;
            left: -140px;
            height: 400px;
            width: 400px;
            transform: rotate(-45deg);
            border-radius: 30px;
        }

        .green2 {
            position: absolute;
            top: 470px;
            left: 210px;
            height: 300px;
            width: 300px;
            transform: rotate(-45deg);
            border-radius: 30px;
        }

        .search-container {
            position: absolute;
            top: 300px;
            left: 450px;
            display: flex;
            align-items: center;
            width: 500px;
            margin: 20px;
        }

        .search-input {
            width: 100%;
            padding: 18px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-button {
            padding: 18px;
            border: 1px solid #ccc;
            border-left: none;
            background-color: #6D6D03;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        .search-button:hover {
            background-color: #3b3b02;
        }

        .profile-section {
            position: absolute;
            top: 200px;
            left: 350px;
            display: flex;
            align-items: center;
            background-color: #6D6D03;
            padding: 10px;
            border-radius: 50px;
            color: white;
            width: 500px;
        }

        .profile-section img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile-section span {
            font-size: 25px;
        }

        .farmer-info {
            margin-top: 50px;
            padding: 20px;
        }

        .farmer-info div {
            background-color: #f4f4f4;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .farmer-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .farmer-info span {
            font-size: 18px;
        }

        .view-details-button {
            padding: 10px 20px;
            background-color: #6D6D03;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .view-details-button:hover {
            background-color: #3b3b02;
        }
    </style>
</head>

<body>
    <!-- Decoration and Search Section -->
    <div class="decoration">
        <img src="img/green bg.PNG" class="green1">
        <img src="img/dealer_search_img2.jpg" class="dealer2">
        <img src="img/dealer_search_img1.jpg" class="dealer1">
        <img src="img/green bg.PNG" class="green2">

        <form method="POST" action="">
            <div class="search-container">
                <input type="text" name="crop_search" class="search-input" placeholder="Search for Crop Name" value="<?php echo isset($crop_search) ? $crop_search : ''; ?>">
                <button class="search-button" type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
        <img src="<?php echo isset($dealer_image) && $dealer_image != "" ? 'uploads/' . $dealer_image : 'img/pfppic.jpg'; ?>" alt="Profile Image">
        <span><?php echo isset($dealer_name) ? $dealer_name : 'Dealer Name'; ?></span>
    </div>

    <!-- Farmer Information Section -->
    <div class="farmer-info">
        <?php
        if (!empty($farmer_info)) {
            foreach ($farmer_info as $farmer) {
                $farmer_name = isset($farmer['name']) ? $farmer['name'] : 'Unknown Farmer';
                $farmer_crop = isset($farmer['cropName']) ? $farmer['cropName'] : 'Unknown Crop';
                $farmer_image = isset($farmer['image']) ? 'uploads/' . $farmer['image'] : 'img/pfppic.jpg';
                $farmer_city = isset($farmer['city']) ? $farmer['city'] : 'Unknown City';
                $farmer_id = $farmer['id'];

                echo "<div><img src='" . $farmer_image . "' alt='Farmer Image'>";
                echo "<span><strong>" . $farmer_name . "</strong> from <strong>" . $farmer_city . "</strong> is selling <strong>" . $farmer_crop . "</strong></span>";
                echo "<form action='dealer_farmer_detail.php' method='get' style='display:inline;'>";
                echo "<input type='hidden' name='farmer_id' value='" . $farmer_id . "'>";
                echo "<input type='hidden' name='crop_name' value='" . $farmer_crop . "'>";
                echo "<button type='submit' class='view-details-button'>View Details</button>";
                echo "</form></div>";
            }
        } else {
            echo "<p>No farmers found selling this crop.</p>";
        }
        ?>
    </div>
</body>
</html>