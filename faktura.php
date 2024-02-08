<?php
session_start();


$customerId = $_SESSION['id'];
// Establish database connection
$servername = "localhost";
$username = "Formutor";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "formutor";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include the Dompdf library
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Create a new Dompdf instance
$dompdf = new Dompdf();

    if (isset($_GET['order_id'])) {
        // Retrieve the order ID from the URL parameter

        $orderNumber = $_GET['order_id'];


$sql = "SELECT email FROM users WHERE id = $customerId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $customerUsername = $row['email'];
} else {
    $customerUsername = "Unknown";
}


        $contractor = "mrowczarnia.support@gmail.com";

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Mrówczarnia - Faktura</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">

            <style>
                /* Define your CSS styles here */
                body {
                    font-family: Arial, sans-serif;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 5px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
            </style>
        </head>
        <body>
            <h1>Faktura</h1>
            <h2>Numer faktury: ' . $orderNumber . '</h2>
            
            <h3>Odbiorca: ' . $customerUsername . '</h3>
            <h3>Zleceniodawca: ' . $contractor . '</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Cena</th>
                        <th>Ilosc</th>
                        <th>Suma</th>
                    </tr>
                </thead>
                <tbody>';

       // Calculate the total amount
        $total = $_SESSION['totalPrice'];

        foreach ($_SESSION['cart'] as $item) {
            $productName = $item['name'];
            $price = $item['price'];
            $quantity = $item['quantity'];
            $itemTotal = $price * $quantity;

            // Add a row for each item
            $html .= '
                    <tr>
                        <td>' . $productName . '</td>
                        <td>' . $price . '</td>
                        <td>' . $quantity . '</td>
                        <td>' . $itemTotal . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td>' . $total . '</td>
                    </tr>
                </tfoot>
            </table>';

  
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream('order_invoice.pdf', ['Attachment' => false]);

        unset($_SESSION['cart']);
    } else {
        echo "Invalid order number.";
    }
