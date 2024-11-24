<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Farmer Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .payment-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .payment-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .payment-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .payment-form input[type="text"],
        .payment-form input[type="date"],
        .payment-form input[type="number"],
        .payment-form select,
        .payment-form input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .payment-form button {
            width: 100%;
            padding: 10px;
            background-color: #6D6D03;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .payment-form button:hover {
            background-color: #3b3b02;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="payment-form">
        <h2>Record Payment to Farmer</h2>
        
        <!-- Display success or error messages -->
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<p class="success">Payment data inserted successfully.</p>';
            } elseif ($_GET['status'] == 'error') {
                echo '<p class="error">There was an error inserting the payment data.</p>';
            }
        }
        ?>

        <form action="insert_payment.php" method="POST" enctype="multipart/form-data">
            <label for="dealer_name">Dealer Name:</label>
            <input type="text" id="dealer_name" name="dealer_name" required>

            <label for="pay_date">Payment Date:</label>
            <input type="date" id="pay_date" name="pay_date" required>

            <label for="amount_recieved">Amount Received (₹):</label>
            <input type="number" id="amount_recieved" name="amount_recieved" step="0.01" min="0" required>

            <label for="quantity_sold">Quantity Sold (Kg):</label>
            <input type="number" id="quantity_sold" name="quantity_sold" min="0" required>

            <label for="image">Payment Receipt Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <label for="farmer_id">Select Farmer:</label>
            <select id="farmer_id" name="farmer_id" required>
                <option value="">--Select Farmer--</option>
                <?php
                // Fetch farmers from the database
                $conn = new mysqli("localhost", "root", "", "agribridge");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT id, name FROM farmer_signup";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select>

            <button type="submit">Submit Payment</button>
        </form>
    </div>

</body>
</html>
