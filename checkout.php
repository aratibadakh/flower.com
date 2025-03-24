
<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['order'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $address = mysqli_real_escape_string($conn, 'Flat No. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products = [];

    $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    if ($cart_total == 0) {
        $message[] = 'Your cart is empty!';
    } else {
        mysqli_query($conn, "INSERT INTO orders(user_id, name, number, email, method, address, total_products, total_price, placed_on) 
        VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')")
        or die('query failed');

        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');
        $message[] = 'Order placed successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Checkout Order</h3>
    <p> <a href="home.php">Home</a> / Checkout </p>
</section>

<section class="checkout">
    <form action="" method="POST">

        <h3>Place Your Order</h3>

        <div class="flex">
            <div class="inputBox">
                <span>Your Name:</span>
                <input type="text" name="name" placeholder="Enter Your Name" required>
            </div>
            <div class="inputBox">
                <span>Your Number:</span>
                <input type="number" name="number" min="0" placeholder="Enter Your Number" required>
            </div>
            <div class="inputBox">
                <span>Your Email:</span>
                <input type="email" name="email" placeholder="Enter Your Email" required>
            </div>
            <div class="inputBox">
                <span>Payment Method:</span>
                <select name="method" id="payment-method" onchange="togglePaymentFields()" required>
                    <option value="cash on delivery">Cash on Delivery</option>
                    <option value="credit card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="paytm">Paytm</option>
                </select>
            </div>

            <!-- Credit Card Fields -->
            <div id="credit-card-fields" class="payment-fields" style="display: none;">
                <div class="inputBox">
                    <span>Card Number:</span>
                    <input type="text" name="card_number" placeholder="Enter Card Number">
                </div>
                <div class="inputBox">
                    <span>Expiry Date:</span>
                    <input type="month" name="expiry_date">
                </div>
                <div class="inputBox">
                    <span>CVV:</span>
                    <input type="text" name="cvv" placeholder="Enter CVV">
                </div>
            </div>

            <!-- PayPal Fields -->
            <div id="paypal-fields" class="payment-fields" style="display: none;">
                <div class="inputBox">
                    <span>PayPal Email:</span>
                    <input type="email" name="paypal_email" placeholder="Enter PayPal Email">
                </div>
            </div>

            <!-- Paytm Fields -->
            <div id="paytm-fields" class="payment-fields" style="display: none;">
                <div class="inputBox">
                    <span>Paytm Number:</span>
                    <input type="text" name="paytm_number" placeholder="Enter Paytm Number">
                </div>
            </div>

            <div class="inputBox">
                <span>Address Line 01:</span>
                <input type="text" name="flat" placeholder="Flat No." required>
            </div>
            <div class="inputBox">
                <span>Street:</span>
                <input type="text" name="street" placeholder="Street Name" required>
            </div>
            <div class="inputBox">
                <span>City:</span>
                <input type="text" name="city" placeholder="City" required>
            </div>
            <div class="inputBox">
                <span>State:</span>
                <input type="text" name="state" placeholder="State" required>
            </div>
            <div class="inputBox">
                <span>Country:</span>
                <input type="text" name="country" placeholder="Country" required>
            </div>
            <div class="inputBox">
                <span>Pin Code:</span>
                <input type="number" name="pin_code" placeholder="Pin Code" required>
            </div>
        </div>

        <input type="submit" name="order" value="Order Now" class="btn">

    </form>
</section>

<?php @include 'footer.php'; ?>

<script>
function togglePaymentFields() {
    let method = document.getElementById("payment-method").value;

    document.getElementById("credit-card-fields").style.display = "none";
    document.getElementById("paypal-fields").style.display = "none";
    document.getElementById("paytm-fields").style.display = "none";

    if (method === "credit card") {
        document.getElementById("credit-card-fields").style.display = "block";
    } else if (method === "paypal") {
        document.getElementById("paypal-fields").style.display = "block";
    } else if (method === "paytm") {
        document.getElementById("paytm-fields").style.display = "block";
    }
}
</script>

</body>
</html>