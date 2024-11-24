<?php
session_start(); // Start the session

// Check if the user is logged in by verifying the 'farmer_id' session variable
if (!isset($_SESSION['farmer_id'])) {
    echo "Error: Farmer ID not set in session. Please log in.";
    exit(); // Stop further execution if the session is not set
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer - Information filling</title>
    <style>
        .profile-pic {
            position: absolute;
            top: 30px;
            left: 40px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #6D6D03;
        }

        .header1 {
            position: absolute;
            top: 60px;
            left: 160px;
            width: 500px;
            height: 100px;
        }

        .header {
            background-color: #6D6D03;
            height: 65px;
        }

        .header button {
            position: relative;
            top: 19px;
            left: 950px;
            padding: 10px 20px;
            margin: 5px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
        }

        .header button.active {
            background-color: white;
            color: #6D6D03;
        }

        .img1 {
            position: relative;
            top: 0;
            left: 50px;
            border-radius: 50px;
            height: 200px;
            width: 350px;
        }

        .img2 {
            position: absolute;
            top: 400px;
            left: 55px;
            border-radius: 50px;
            height: 200px;
            width: 350px;
        }

        .form-section {
            height: 470px;
            overflow: hidden;
            display: none;
            margin-top: 20px;
        }

        .form-section.active {
            display: block;
        }

        .form-section label {
            font-size: 20px;
            margin: 10px 0 5px;
        }

        .form-section input {
            padding: 10px;
            width: 500px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-section button[type="submit"] {
            position: absolute;
            top: 410px;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 0;
        }

        .f1 {
            position: relative;
            top: -200px;
            left: 500px;
        }

        .f2 {
            position: relative;
            top: -170px;
            left: 500px;
        }

        .f3 {
            position: relative;
            top: -200px;
            left: 500px;
        }

        .addMore {
            position: absolute;
            top: 550px;
            right: 200px;
            margin-top: 0;
            padding: 10px 20px;
            margin: 5px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
        }

        .dashboard{
            position: fixed;
            top: 20px;
            right: 20px;
            margin-top: 0;
            padding: 10px 20px;
            margin: 5px;
            font-size: 18px;
            background-color: #6D6D03;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php
// Check if the user is logged in by verifying if 'farmer_id' exists in the session
if (isset($_SESSION['farmer_id'])) {
    // Retrieve and display farmer's profile info
    $profileImage = isset($_SESSION['farmer_image']) && !empty($_SESSION['farmer_image']) ? $_SESSION['farmer_image'] : 'img/pfppic.jpg'; // Default image path
    echo "<div class='header'>";
    echo "<img src='" . htmlspecialchars($profileImage) . "' alt='Profile Picture' class='profile-pic'>";
    echo "<h1 class='header1'>" . htmlspecialchars($_SESSION['farmer_name']) . "</h1>";
    echo "</div>";
} else {
    echo "<div class='header'><h1>Farmer</h1></div>";
}
?>

<div class="header">
    <button type="button" onclick="showSection('crop')" class="c">Crop</button>
    <button type="button" onclick="showSection('land')" class="l">Land</button>
    <button type="button" onclick="showSection('payment')" class="p">Payment</button>
</div>

<!-- Crop Section -->
<form action="process_farmer_input.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="farmer_id" value="<?php echo htmlspecialchars($_SESSION['farmer_id']); ?>">

    <div class="form-section" id="crop">
        <img src="img/crop2.jfif" class="img1">
        <img src="img/crop1.jfif" class="img2">

        <div class="f1">
            <label for="cropId">Crop Id: </label>
            <input type="number" id="cropId" name="cropId" placeholder="Enter crop id"><br><br>

            <label for="cropName">Crop Name: </label>
            <input type="text" id="cropName" name="cropName" placeholder="Enter name"><br><br>

            <label for="cropType">Crop Type: </label>
            <input type="text" id="cropType" name="cropType" placeholder="Enter crop type"><br><br>

            <label for="season">Season: </label>
            <input type="text" id="season" name="season" placeholder="Enter yield season"><br><br>

            <label for="ypa">Crop Area (acres):</label>
            <input type="number" id="ypa" name="ypa" placeholder="Enter yield per acre"><br><br>

            <label for="cimg">Add Image: </label>
            <input type="file" id="cimg" name="cimg" accept="image/*" required><br><br>
        </div>
    </div>

    <!-- Land Section -->
    <div class="form-section" id="land">
        <img src="img/crop3.jfif" class="img1">
        <img src="img/crop4.jfif" class="img2">

        <div class="f2">
            <label for="landId">Land Id: </label>
            <input type="number" id="landId" name="landId" placeholder="Enter land id"><br><br>

            <label for="landLocation">Land Location:</label>
            <input type="text" id="landLocation" name="landLocation" placeholder="Enter land location"><br><br>

            <label for="areaLand">Land Area (acres):</label>
            <input type="number" id="areaLand" name="areaLand" placeholder="Enter land area"><br><br>

            <label for="soilType">Soil Type:</label>
            <input type="text" id="soilType" name="soilType" placeholder="Enter soil type"><br><br>

            <label for="irrigationType">Irrigation Type:</label>
            <input type="text" id="irrigationType" name="irrigationType" placeholder="Enter irrigation type"><br><br>

            <label for="limg">Add Image: </label>
            <input type="file" id="limg" name="limg" accept="image/*" required><br><br>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="form-section" id="payment">
        <img src="img/crop5.jfif" class="img1">
        <img src="img/crop6.jfif" class="img2">

        <div class="f3">
            <label for="payDate">Payment Date:</label>
            <input type="date" id="payDate" name="payDate" placeholder="Enter payment date"><br><br>

            <label for="nod">Name of Dealer:</label>
            <input type="text" id="nod" name="nod" placeholder="Enter dealer's name"><br><br>

            <label for="cost">Cost:</label>
            <input type="number" id="cost" name="cost" placeholder="Enter amount"><br><br>

            <label for="amountSold">Amount Sold:</label>
            <input type="number" id="amountSold" name="amountSold" placeholder="Enter amount sold"><br><br>

            <label for="pimg">Add Image: </label>
            <input type="file" id="pimg" name="pimg" accept="image/*" required><br><br>

            <button type="submit">Save</button>
        </div>
        <a href="farmerInput.php"><button type="button" class="addMore">Add More Crop</button></a>
    </div>
</form>

<a href="farmerOutput.php"><button type="button" class="dashboard"> Go to Dashboard</button></a>

<script>
    function showSection(section) {
        var sections = document.querySelectorAll('.form-section');
        sections.forEach(function (sec) {
            sec.classList.remove('active');
        });

        var activeSection = document.getElementById(section);
        activeSection.classList.add('active');
    }
</script>

</body>
</html>